<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * Public CMS API controller.
 */
class CmsController extends Controller
{
    /**
     * Display a listing of published pages.
     */
    public function index(): JsonResponse
    {
        $pages = Page::query()
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->latest('published_at')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Pages retrieved successfully.',
            'data' => $pages->map(fn (Page $page) => $this->serializePage($page))->values(),
        ]);
    }

    /**
     * Display the specified published page by slug.
     */
    public function show(string $slug): JsonResponse
    {
        $page = Page::query()
            ->where('status', 'published')
            ->where('slug', $slug)
            ->first();

        if (! $page) {
            return response()->json([
                'success' => false,
                'message' => 'Page not found.',
                'data' => null,
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'message' => 'Page retrieved successfully.',
            'data' => $this->serializePage($page),
        ]);
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
