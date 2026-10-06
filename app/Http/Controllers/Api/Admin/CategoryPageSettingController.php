<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoryPageSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Admin controller for category listing page hero settings.
 * Manages the banner image for the trekking / peak-climbing / mountain-biking
 * listing pages.
 */
class CategoryPageSettingController extends Controller
{
    /** All categories that have a listing page with a hero banner. */
    public const CATEGORIES = ['trekking', 'peak-climbing', 'mountain-biking', 'wellness-journey', 'fast-packing-trips', 'heli-tours', 'tibet'];

    /**
     * Display all category page settings (one per category).
     */
    public function index(): JsonResponse
    {
        $settings = CategoryPageSetting::query()->get();

        // Ensure every known category has an entry in the response.
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
     * Update the hero banner for a specific category. Creates the row if missing.
     */
    public function update(Request $request, string $category): JsonResponse
    {
        if (!in_array($category, self::CATEGORIES, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Unknown category.',
                'data' => null,
            ], 422);
        }

        $data = $request->validate([
            'bannerImage' => ['nullable', 'string', 'max:500'],
            'bannerAlt' => ['nullable', 'string', 'max:255'],
        ]);

        $setting = CategoryPageSetting::updateOrCreate(
            ['category' => $category],
            [
                'banner_image' => $data['bannerImage'] ?? null,
                'banner_alt' => $data['bannerAlt'] ?? null,
            ],
        );

        return response()->json([
            'success' => true,
            'message' => 'Category page setting updated successfully.',
            'data' => $this->serialize($setting),
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
