<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Admin CRUD controller for tours.
 *
 * Tours are addressed by their `external_id` (e.g. "tour-mustang-bike-001")
 * in the admin API so the slug-like identifier matches the value returned
 * in serialized tour payloads.
 */
class TourController extends Controller
{
    /** Allowed sub-category values for tours that opt into a sub-category. */
    private const SUBCATEGORIES = ['tibet-trip', 'tibet-mountain-biking'];

    /**
     * Display a paginated listing of tours (including inactive).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Tour::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search): void {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('overview', 'like', "%{$search}%")
                  ->orWhere('region', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category') && in_array($request->input('category'), ['trekking', 'peak-climbing', 'mountain-biking', 'wellness-journey', 'fast-packing-trips', 'heli-tours', 'tibet'], true)) {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('sub_category') && in_array($request->input('sub_category'), self::SUBCATEGORIES, true)) {
            $query->where('sub_category', $request->input('sub_category'));
        }

        $paginator = $query->latest()->paginate($request->integer('limit', 12));

        return response()->json([
            'success' => true,
            'message' => 'Tours retrieved successfully.',
            'data' => [
                'data' => $paginator->map(fn (Tour $tour) => $this->serializeTour($tour))->values(),
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
     * Store a newly created tour.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->tourRules());
        $attributes = $this->mapToAttributes($data);
        $attributes['external_id'] = Str::uuid()->toString();

        $tour = Tour::create($attributes);

        return response()->json([
            'success' => true,
            'message' => 'Tour created successfully.',
            'data' => $this->serializeTour($tour),
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified tour (looked up by external_id).
     */
    public function show(string $externalId): JsonResponse
    {
        $tour = Tour::where('external_id', $externalId)->first();

        if (!$tour) {
            return response()->json([
                'success' => false,
                'message' => 'Tour not found.',
                'data' => null,
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tour retrieved successfully.',
            'data' => $this->serializeTour($tour),
        ]);
    }

    /**
     * Update the specified tour (looked up by external_id).
     */
    public function update(Request $request, string $externalId): JsonResponse
    {
        $tour = Tour::where('external_id', $externalId)->first();

        if (!$tour) {
            return response()->json([
                'success' => false,
                'message' => 'Tour not found.',
                'data' => null,
            ], Response::HTTP_NOT_FOUND);
        }

        $data = $request->validate($this->tourRules($tour));
        $tour->update($this->mapToAttributes($data));

        return response()->json([
            'success' => true,
            'message' => 'Tour updated successfully.',
            'data' => $this->serializeTour($tour->fresh()),
        ]);
    }

    /**
     * Remove the specified tour (looked up by external_id).
     */
    public function destroy(string $externalId): JsonResponse
    {
        $tour = Tour::where('external_id', $externalId)->first();

        if (!$tour) {
            return response()->json([
                'success' => false,
                'message' => 'Tour not found.',
                'data' => null,
            ], Response::HTTP_NOT_FOUND);
        }

        $tour->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tour deleted successfully.',
            'data' => null,
        ]);
    }

    /**
     * Validation rules for tour create/update.
     *
     * @return array<string, mixed>
     */
    private function tourRules(?Tour $tour = null): array
    {
        $slugRule = Rule::unique('tours', 'slug');
        if ($tour !== null) {
            $slugRule->ignore($tour->id);
        }

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', $slugRule],
            'overview' => ['required', 'string'],
            'highlights' => ['required', 'array'],
            'itinerary' => ['required', 'array'],
            'inclusions' => ['required', 'array'],
            'exclusions' => ['required', 'array'],
            'gallery' => ['present', 'array'],
            'routeMapImage' => ['nullable', 'string', 'max:255'],
            'altitudeChartImage' => ['nullable', 'string', 'max:255'],
            'video' => ['nullable', 'string', 'max:255'],
            'faqs' => ['present', 'array'],
            'routeInfo' => ['required', 'array'],
            'duration.value' => ['required', 'integer', 'min:1'],
            'duration.unit' => ['required', 'string', 'max:32'],
            'departureInfo' => ['required', 'array'],
            'price.amount' => ['required', 'integer', 'min:0'],
            'price.currency' => ['required', 'string', 'max:8'],
            'price.label' => ['nullable', 'string', 'max:64'],
            'discountPercent' => ['integer', 'min:0', 'max:100'],
            'maxAltitude' => ['required', 'string', 'max:32'],
            'bestSeason' => ['required', 'string', 'max:64'],
            'groupSize' => ['required', 'string', 'max:64'],
            'tripGrade' => ['required', 'string', 'max:64'],
            'country' => ['required', 'string', 'max:64'],
            'activities' => ['required', 'string', 'max:255'],
            'region' => ['nullable', 'string', 'max:64'],
            'category' => ['nullable', 'string', 'in:trekking,peak-climbing,mountain-biking,wellness-journey,fast-packing-trips,heli-tours,tibet'],
            'subCategory' => ['nullable', 'string', 'in:tibet-trip,tibet-mountain-biking'],
            'isPopular' => ['boolean'],
            'status' => ['required', 'string', 'in:active,inactive,draft'],
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
        return [
            'title' => $data['title'],
            'slug' => $data['slug'],
            'overview' => $data['overview'],
            'highlights' => $data['highlights'],
            'itinerary' => $data['itinerary'],
            'inclusions' => $data['inclusions'],
            'exclusions' => $data['exclusions'],
            'gallery' => $data['gallery'],
            'route_map_image' => $data['routeMapImage'] ?? null,
            'altitude_chart_image' => $data['altitudeChartImage'] ?? null,
            'video' => $data['video'] ?? null,
            'faqs' => $data['faqs'],
            'route_info' => $data['routeInfo'],
            'duration_value' => $data['duration']['value'],
            'duration_unit' => $data['duration']['unit'],
            'departure_info' => $data['departureInfo'],
            'price_amount' => $data['price']['amount'],
            'price_currency' => $data['price']['currency'],
            'price_label' => $data['price']['label'] ?? null,
            'discount_percent' => $data['discountPercent'] ?? 0,
            'max_altitude' => $data['maxAltitude'],
            'best_season' => $data['bestSeason'],
            'group_size' => $data['groupSize'],
            'region' => $data['region'] ?? null,
            'category' => $data['category'] ?? 'trekking',
            'sub_category' => $data['subCategory'] ?? null,
            'is_popular' => $data['isPopular'] ?? false,
            'status' => $data['status'],
            'trip_grade' => $data['tripGrade'],
            'country' => $data['country'],
            'activities' => $data['activities'],
        ];
    }

    /**
     * Transform a Tour model into the frontend API shape.
     *
     * @return array<string, mixed>
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
