<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Public blog API controller.
 */
class BlogController extends Controller
{
    /**
     * Display a paginated listing of published blog posts.
     */
    public function index(Request $request): JsonResponse
    {
        $query = BlogPost::query()
            ->with(['category', 'author'])
            ->where('status', 'published')
            ->whereNotNull('published_at');

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request): void {
                $q->where('slug', $request->input('category'));
            });
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search): void {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $perPage = $request->integer('limit', 12);
        $paginator = $query->latest('published_at')->paginate($perPage);

        $categories = BlogCategory::orderBy('name')
            ->get()
            ->map(fn (BlogCategory $category) => $this->serializeCategory($category));

        return response()->json([
            'success' => true,
            'message' => 'Blog posts retrieved successfully.',
            'data' => [
                'posts' => $paginator->map(fn (BlogPost $post) => $this->serializePost($post))->values(),
                'categories' => $categories,
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
     * Display the specified published blog post by slug.
     */
    public function show(string $slug): JsonResponse
    {
        $post = BlogPost::query()
            ->with(['category', 'author'])
            ->where('status', 'published')
            ->where('slug', $slug)
            ->first();

        if (! $post) {
            return response()->json([
                'success' => false,
                'message' => 'Blog post not found.',
                'data' => null,
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'message' => 'Blog post retrieved successfully.',
            'data' => $this->serializePost($post),
        ]);
    }

    /**
     * Display a listing of blog categories.
     */
    public function categories(): JsonResponse
    {
        $categories = BlogCategory::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'message' => 'Blog categories retrieved successfully.',
            'data' => $categories->map(fn (BlogCategory $category) => $this->serializeCategory($category))->values(),
        ]);
    }

    /**
     * Serialize a blog category for the API.
     *
     * @return array<string, mixed>
     */
    private function serializeCategory(BlogCategory $category): array
    {
        return [
            'id' => (string) $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'description' => $category->description,
            'postCount' => $category->posts()->where('status', 'published')->count(),
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
            'category' => $post->category ? $this->serializeCategory($post->category) : null,
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
