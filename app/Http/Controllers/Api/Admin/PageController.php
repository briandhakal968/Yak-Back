<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

/**
 * Admin CRUD controller for CMS pages.
 */
class PageController extends Controller
{
    /**
     * Display a paginated listing of pages.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Page::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search): void {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $paginator = $query->latest()->paginate($request->integer('limit', 12));

        return response()->json([
            'success' => true,
            'message' => 'Pages retrieved successfully.',
            'data' => [
                'data' => $paginator->map(fn (Page $page) => $this->serializePage($page))->values(),
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
     * Store a newly created page.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->pageRules());
        $page = Page::create([
            'title' => $data['title'],
            'slug' => $data['slug'],
            'content' => $data['content'],
            'banner_image' => $data['bannerImage'] ?? null,
            'seo' => $data['seo'] ?? [],
            'status' => $data['status'],
            'published_at' => $data['status'] === 'published' ? now() : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Page created successfully.',
            'data' => $this->serializePage($page),
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified page.
     */
    public function show(string $page): JsonResponse
    {
        $pageModel = Page::where('slug', $page)
            ->orWhere('id', $page)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'message' => 'Page retrieved successfully.',
            'data' => $this->serializePage($pageModel),
        ]);
    }

    /**
     * Update the specified page.
     */
    public function update(Request $request, string $page): JsonResponse
    {
        $pageModel = Page::where('slug', $page)
            ->orWhere('id', $page)
            ->firstOrFail();

        $data = $request->validate($this->pageRules($pageModel));
        $attributes = [
            'title' => $data['title'],
            'slug' => $data['slug'],
            'content' => $data['content'],
            'banner_image' => $data['bannerImage'] ?? null,
            'seo' => $data['seo'] ?? [],
            'status' => $data['status'],
        ];

        if ($data['status'] === 'published' && $pageModel->published_at === null) {
            $attributes['published_at'] = now();
        }

        $pageModel->update($attributes);

        return response()->json([
            'success' => true,
            'message' => 'Page updated successfully.',
            'data' => $this->serializePage($pageModel->fresh()),
        ]);
    }

    /**
     * Remove the specified page.
     */
    public function destroy(string $page): JsonResponse
    {
        $pageModel = Page::where('slug', $page)
            ->orWhere('id', $page)
            ->firstOrFail();
        $pageModel->delete();

        return response()->json([
            'success' => true,
            'message' => 'Page deleted successfully.',
            'data' => null,
        ]);
    }

    /**
     * Validation rules for pages.
     *
     * @return array<string, mixed>
     */
    private function pageRules(?Page $page = null): array
    {
        $slugRule = Rule::unique('pages', 'slug');
        if ($page !== null) {
            $slugRule->ignore($page->id);
        }

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', $slugRule],
            'content' => ['required', 'array'],
            'bannerImage' => ['nullable', 'array'],
            'seo' => ['nullable', 'array'],
            'status' => ['required', 'string', 'in:draft,published'],
        ];
    }

    /**
     * Serialize a page for the API.
     *
     * @return array<string, mixed>
     */
    private function serializePage(Page $page): array
    {
        return [
            'id' => (string) $page->id,
            'title' => $page->title,
            'slug' => $page->slug,
            'content' => $page->content ?? [],
            'bannerImage' => $page->banner_image ?? null,
            'seo' => $page->seo ?? [],
            'status' => $page->status,
            'publishedAt' => $page->published_at?->toIso8601String(),
            'updatedAt' => $page->updated_at?->toIso8601String(),
        ];
    }
}
