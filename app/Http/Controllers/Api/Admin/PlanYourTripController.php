<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Admin controller for managing Plan Your Trip inquiries.
 */
class PlanYourTripController extends Controller
{
    /**
     * Display a paginated listing of inquiries.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Inquiry::query();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search): void {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('destination', 'like', "%{$search}%")
                  ->orWhere('activity', 'like', "%{$search}%");
            });
        }

        $paginator = $query->latest()->paginate($request->integer('limit', 12));

        return response()->json([
            'success' => true,
            'message' => 'Inquiries retrieved successfully.',
            'data' => [
                'data' => $paginator->map(fn (Inquiry $inquiry) => $this->serializeInquiry($inquiry))->values(),
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
     * Display the specified inquiry.
     */
    public function show(Inquiry $inquiry): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Inquiry retrieved successfully.',
            'data' => $this->serializeInquiry($inquiry),
        ]);
    }

    /**
     * Update the inquiry status.
     */
    public function update(Request $request, Inquiry $inquiry): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:new,contacted,converted,archived'],
        ]);

        $inquiry->update(['status' => $data['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Inquiry updated successfully.',
            'data' => $this->serializeInquiry($inquiry->fresh()),
        ]);
    }

    /**
     * Remove the specified inquiry.
     */
    public function destroy(Inquiry $inquiry): JsonResponse
    {
        $inquiry->delete();

        return response()->json([
            'success' => true,
            'message' => 'Inquiry deleted successfully.',
            'data' => null,
        ]);
    }

    /**
     * Serialize an inquiry for the API.
     *
     * @return array<string, mixed>
     */
    private function serializeInquiry(Inquiry $inquiry): array
    {
        return [
            'id' => (string) $inquiry->id,
            'travelGroup' => $inquiry->travel_group,
            'numberOfPeople' => $inquiry->number_of_people ?? '',
            'dateType' => $inquiry->date_type,
            'arrivalDate' => $inquiry->arrival_date?->toDateString() ?? '',
            'departureDate' => $inquiry->departure_date?->toDateString() ?? '',
            'travelMonth' => $inquiry->travel_month ?? '',
            'destination' => $inquiry->destination ?? '',
            'activity' => $inquiry->activity ?? '',
            'packages' => $inquiry->packages ?? [],
            'accommodation' => $inquiry->accommodation,
            'exercise' => $inquiry->exercise ?? '',
            'fullName' => $inquiry->full_name,
            'country' => $inquiry->country,
            'email' => $inquiry->email,
            'phone' => $inquiry->phone,
            'message' => $inquiry->message ?? '',
            'status' => $inquiry->status,
            'createdAt' => $inquiry->created_at?->toIso8601String(),
            'updatedAt' => $inquiry->updated_at?->toIso8601String(),
        ];
    }
}
