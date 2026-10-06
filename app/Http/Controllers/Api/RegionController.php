<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Http\JsonResponse;

/**
 * Public Region controller.
 */
class RegionController extends Controller
{
    /**
     * Display a listing of active regions.
     */
    public function index(): JsonResponse
    {
        $regions = Region::query()
            ->where('is_active', true)
            ->orderBy('display_order')
            ->orderBy('name')
            ->get()
            ->map(fn (Region $region) => $this->serialize($region))
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'Regions retrieved successfully.',
            'data' => $regions,
        ]);
    }

    /**
     * Display the specified region (by slug).
     */
    public function show(string $slug): JsonResponse
    {
        $region = Region::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$region) {
            return response()->json([
                'success' => false,
                'message' => 'Region not found.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Region retrieved successfully.',
            'data' => $this->serialize($region),
        ]);
    }

    /**
     * Serialize a region for the API.
     *
     * @return array<string, mixed>
     */
    private function serialize(Region $region): array
    {
        return [
            'id' => (string) $region->id,
            'name' => $region->name,
            'slug' => $region->slug,
            'image' => $region->image_url,
            'imageUrl' => $region->image_url,
            'description' => $region->description ?? '',
            'shortDescription' => $region->short_description ?? '',
            'order' => (int) $region->display_order,
            'isActive' => (bool) $region->is_active,
            'createdAt' => $region->created_at?->toIso8601String(),
            'updatedAt' => $region->updated_at?->toIso8601String(),
        ];
    }
}
