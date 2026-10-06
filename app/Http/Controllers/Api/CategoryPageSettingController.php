<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CategoryPageSetting;
use Illuminate\Http\JsonResponse;

/**
 * Public endpoint exposing category page hero settings so the public
 * listing pages can render the admin-controlled hero banner.
 */
class CategoryPageSettingController extends Controller
{
    /** Categories exposed publicly (must match the admin controller's list). */
    public const CATEGORIES = ['trekking', 'peak-climbing', 'mountain-biking', 'wellness-journey', 'fast-packing-trips', 'heli-tours'];

    public function index(): JsonResponse
    {
        $settings = CategoryPageSetting::query()->get();
        $byCategory = $settings->keyBy('category');
        $data = [];
        foreach (self::CATEGORIES as $category) {
            $row = $byCategory->get($category);
            $data[] = $row ? $this->serialize($row) : [
                'category' => $category,
                'bannerImage' => null,
                'bannerAlt' => null,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Category page settings retrieved successfully.',
            'data' => $data,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function serialize(CategoryPageSetting $s): array
    {
        return [
            'category' => $s->category,
            'bannerImage' => $s->banner_image,
            'bannerAlt' => $s->banner_alt,
            'updatedAt' => $s->updated_at?->toIso8601String(),
        ];
    }
}
