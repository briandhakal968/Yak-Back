<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

/**
 * Admin Region controller.
 */
class RegionController extends Controller
{
    /**
     * Display a paginated listing of regions.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Region::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && in_array($request->input('status'), ['active', 'inactive'], true)) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $paginator = $query
            ->orderBy('display_order')
            ->orderBy('name')
            ->paginate($request->integer('limit', 40));

        return response()->json([
            'success' => true,
            'message' => 'Regions retrieved successfully.',
            'data' => [
                'data' => $paginator->map(fn (Region $r) => $this->serialize($r))->values(),
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
     * Store a newly created region.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->regionRules());
        $attributes = $this->mapToAttributes($data);
        $attributes['slug'] = $attributes['slug'] ?: Str::slug($data['name']);

        $region = Region::create($attributes);

        return response()->json([
            'success' => true,
            'message' => 'Region created successfully.',
            'data' => $this->serialize($region),
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified region (by id).
     */
    public function show(Region $region): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Region retrieved successfully.',
            'data' => $this->serialize($region),
        ]);
    }

    /**
     * Update the specified region.
     */
    public function update(Request $request, Region $region): JsonResponse
    {
        $data = $request->validate($this->regionRules($region));
        $region->update($this->mapToAttributes($data));

        return response()->json([
            'success' => true,
            'message' => 'Region updated successfully.',
            'data' => $this->serialize($region->fresh()),
        ]);
    }

    /**
     * Remove the specified region.
     */
    public function destroy(Region $region): JsonResponse
    {
        $region->delete();

        return response()->json([
            'success' => true,
            'message' => 'Region deleted successfully.',
            'data' => null,
        ]);
    }

    /**
     * Validation rules for region create/update.
     *
     * @return array<string, mixed>
     */
    private function regionRules(?Region $region = null): array
    {
        $slugRule = \Illuminate\Validation\Rule::unique('regions', 'slug');
        if ($region !== null) {
            $slugRule->ignore($region->id);
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', $slugRule],
            'imageUrl' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'shortDescription' => ['nullable', 'string', 'max:500'],
            'order' => ['nullable', 'integer', 'min:0'],
            'isActive' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Map validated frontend payload to database columns.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function mapToAttributes(array $data): array
    {
        $image = $data['image'] ?? $data['imageUrl'] ?? null;
        return [
            'name' => $data['name'],
            'slug' => $data['slug'] ?? Str::slug($data['name']),
            'image_url' => $image,
            'description' => $data['description'] ?? null,
            'short_description' => $data['shortDescription'] ?? null,
            'display_order' => $data['order'] ?? 0,
            'is_active' => $data['isActive'] ?? true,
        ];
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
