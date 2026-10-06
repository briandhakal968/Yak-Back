<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

/**
 * Admin CRUD controller for blog posts.
 */
class BlogPostController extends Controller
{
    /**
     * Display a paginated listing of blog posts.
     */
    public function index(Request $request): JsonResponse
    {
        $query = BlogPost::query()->with(['category', 'author']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search): void {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('category')) {
            $categorySlug = $request->input('category');
            $query->whereHas('category', function ($q) use ($categorySlug): void {
                $q->where('slug', $categorySlug);
            });
        }

        $paginator = $query->latest()->paginate($request->integer('limit', 20));

        return response()->json([
            'success' => true,
            'message' => 'Blog posts retrieved successfully.',
            'data' => [
                'data' => $paginator->map(fn (BlogPost $post) => $this->serializePost($post))->values(),
                'meta' => [
                    'page' => $paginator->currentPage(),
                    'limit' => $paginator->perPage(),
                    'total' => $paginator->total(),
                    'totalPages' => $paginator->lastPage(),
                ],
            ],
        ]);
    }

    /**
     * Store a newly created blog post.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->postRules());
        $attributes = $this->mapToAttributes($data);
        $attributes['author_id'] = $request->user()->id;
        $attributes['published_at'] = $data['status'] === 'published' ? now() : null;

        $post = BlogPost::create($attributes);

        return response()->json([
            'success' => true,
            'message' => 'Blog post created successfully.',
            'data' => $this->serializePost($post->load(['category', 'author'])),
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified blog post.
     */
    public function show(string $blog_post): JsonResponse
    {
        $post = BlogPost::with(['category', 'author'])
            ->where('slug', $blog_post)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'message' => 'Blog post retrieved successfully.',
            'data' => $this->serializePost($post),
        ]);
    }

    /**
     * Update the specified blog post.
     */
    public function update(Request $request, string $blog_post): JsonResponse
    {
        $post = BlogPost::where('slug', $blog_post)->firstOrFail();

        $data = $request->validate($this->postRules($post));
        $attributes = $this->mapToAttributes($data);

        if ($data['status'] === 'published' && $post->published_at === null) {
            $attributes['published_at'] = now();
        }

        $post->update($attributes);

        return response()->json([
            'success' => true,
            'message' => 'Blog post updated successfully.',
            'data' => $this->serializePost($post->fresh()->load(['category', 'author'])),
        ]);
    }

    /**
     * Remove the specified blog post.
     */
    public function destroy(string $blog_post): JsonResponse
    {
        $post = BlogPost::where('slug', $blog_post)
            ->orWhere('id', $blog_post)
            ->firstOrFail();
        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Blog post deleted successfully.',
            'data' => null,
        ]);
    }

    /**
     * Bulk delete blog posts by their slugs.
     */
    public function bulkDestroy(Request $request): JsonResponse
    {
        $data = $request->validate([
            'slugs' => ['required', 'array', 'min:1'],
            'slugs.*' => ['required', 'string', 'max:255'],
        ]);

        $count = BlogPost::whereIn('slug', $data['slugs'])->delete();

        return response()->json([
            'success' => true,
            'message' => "{$count} blog post(s) deleted successfully.",
            'data' => ['deleted' => $count],
        ]);
    }

    /**
     * Map validated frontend payload to database columns.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function mapToAttributes(array $data): array
    {
        return [
            'title' => $data['title'],
            'slug' => $data['slug'],
            'content' => $data['content'],
            'featured_image' => $data['featuredImage'],
            'category_id' => $data['categoryId'],
            'tags' => $data['tags'] ?? [],
            'status' => $data['status'],
            'seo' => $data['seo'] ?? [],
            'reading_time' => $data['readingTime'] ?? 0,
        ];
    }

    /**
     * Validation rules for blog posts.
     *
     * @return array<string, mixed>
     */
    private function postRules(?BlogPost $post = null): array
    {
        $slugRule = Rule::unique('blog_posts', 'slug');
        if ($post !== null) {
            $slugRule->ignore($post->id);
        }

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', $slugRule],
            'content' => ['required', 'string'],
            'featuredImage' => ['required', 'array'],
            'categoryId' => ['required', 'exists:blog_categories,id'],
            'tags' => ['nullable', 'array'],
            'status' => ['required', 'string', 'in:draft,published,archived'],
            'seo' => ['nullable', 'array'],
            'readingTime' => ['integer', 'min:0'],
        ];
    }

    /**
     * Serialize a blog post for the API.
     *
     * @return array<string, mixed>
     */
    private function serializePost(BlogPost $post): array
    {
        return [
            'id' => (string) $post->id,
            'title' => $post->title,
            'slug' => $post->slug,
            'excerpt' => $post->excerpt,
            'content' => $post->content,
            'featuredImage' => $post->featured_image ?? [],
            'category' => $post->category ? [
                'id' => (string) $post->category->id,
                'name' => $post->category->name,
                'slug' => $post->category->slug,
                'postCount' => $post->category->posts()->count(),
            ] : null,
            'tags' => $post->tags ?? [],
            'author' => $post->author ? [
                'id' => (string) $post->author->id,
                'name' => $post->author->name,
                'avatar' => null,
                'bio' => null,
            ] : null,
            'status' => $post->status,
            'seo' => $post->seo ?? [],
            'readingTime' => $post->reading_time,
            'publishedAt' => $post->published_at?->toIso8601String(),
            'updatedAt' => $post->updated_at?->toIso8601String(),
        ];
    }
}
