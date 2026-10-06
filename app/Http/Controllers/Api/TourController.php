<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TourController extends Controller
{
    /**
     * Display a paginated listing of tours.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Tour::query()->where('status', 'active');

        if ($request->boolean('isPopular')) {
            $query->where('is_popular', true);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('overview', 'like', "%{$search}%")
                  ->orWhere('region', 'like', "%{$search}%");
            });
        }

        if ($request->filled('minPrice')) {
            $query->where('price_amount', '>=', $request->integer('minPrice'));
        }

        if ($request->filled('maxPrice')) {
            $query->where('price_amount', '<=', $request->integer('maxPrice'));
        }

        if ($request->has('region')) {
            $regions = $request->input('region');
            $regions = is_array($regions) ? $regions : [$regions];
            $query->whereIn('region', $regions);
        }

        if ($request->filled('category')) {
            $categories = $request->input('category');
            $categories = is_array($categories) ? $categories : [$categories];
            $query->whereIn('category', $categories);
        }

        if ($request->filled('subCategory')) {
            $subCategories = $request->input('subCategory');
            $subCategories = is_array($subCategories) ? $subCategories : [$subCategories];
            $query->whereIn('sub_category', $subCategories);
        }

        $perPage = $request->integer('limit', 12);
        $paginator = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Tours retrieved successfully',
            'data' => [
                'tours' => $paginator->map(fn (Tour $tour) => $this->serializeTour($tour))->values(),
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
     * Display the specified tour by slug or external_id.
     */
    public function show(string $id): JsonResponse
    {
        $tour = Tour::query()
            ->where('status', 'active')
            ->where(function ($q) use ($id) {
                $q->where('slug', $id)->orWhere('external_id', $id);
            })
            ->first();

        if (! $tour) {
            return response()->json([
                'success' => false,
                'message' => 'Tour not found',
                'data' => null,
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tour retrieved successfully',
            'data' => $this->serializeTour($tour),
        ]);
    }

    /**
     * Transform a Tour model into the frontend API shape.
     */
    private function serializeTour(Tour $tour): array
    {
        return [
            'id' => $tour->external_id,
            'createdAt' => $tour->created_at?->toIso8601String(),
            'updatedAt' => $tour->updated_at?->toIso8601String(),
            'title' => $tour->title,
            'slug' => $tour->slug,
            'overview' => $tour->overview,
            'highlights' => $tour->highlights ?? [],
            'itinerary' => $tour->itinerary ?? [],
            'inclusions' => $tour->inclusions ?? [],
            'exclusions' => $tour->exclusions ?? [],
            'gallery' => $tour->gallery ?? [],
            'routeMapImage' => $tour->route_map_image,
            'altitudeChartImage' => $tour->altitude_chart_image,
            'video' => $tour->video,
            'faqs' => $tour->faqs ?? [],
            'routeInfo' => $tour->route_info ?? [],
            'duration' => [
                'value' => $tour->duration_value,
                'unit' => $tour->duration_unit,
            ],
            'departureInfo' => $tour->departure_info ?? [],
            'price' => [
                'amount' => $tour->price_amount,
                'currency' => $tour->price_currency,
                'label' => $tour->price_label,
            ],
            'discountPercent' => $tour->discount_percent,
            'maxAltitude' => $tour->max_altitude,
            'bestSeason' => $tour->best_season,
            'groupSize' => $tour->group_size,
            'region' => $tour->region,
            'category' => $tour->category ?? 'trekking',
            'subCategory' => $tour->sub_category,
            'isPopular' => $tour->is_popular,
            'status' => $tour->status,
            'tripGrade' => $tour->trip_grade,
            'country' => $tour->country,
            'activities' => $tour->activities,
        ];
    }
}
