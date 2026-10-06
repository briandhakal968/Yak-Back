<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Services\EmailNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Handles Plan Your Trip inquiry submissions from the public site.
 */
class PlanYourTripController extends Controller
{
    /**
     * Validate and persist the inquiry.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'travelGroup' => 'required|string|max:32',
            'numberOfPeople' => 'nullable|string|max:32',
            'dateType' => 'required|string|max:32',
            'arrivalDate' => 'nullable|date',
            'departureDate' => 'nullable|date',
            'travelMonth' => 'nullable|string|max:32',
            'destination' => 'nullable|string|max:128',
            'activity' => 'nullable|string|max:64',
            'packages' => 'nullable|array',
            'packages.*' => 'string',
            'accommodation' => 'required|string|max:32',
            'exercise' => 'nullable|string|max:32',
            'fullName' => 'required|string|max:255',
            'country' => 'required|string|max:128',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:64',
            'message' => 'nullable|string',
        ]);

        $inquiry = Inquiry::create([
            'travel_group' => $validated['travelGroup'],
            'number_of_people' => $validated['numberOfPeople'] ?? null,
            'date_type' => $validated['dateType'],
            'arrival_date' => $validated['arrivalDate'] ?? null,
            'departure_date' => $validated['departureDate'] ?? null,
            'travel_month' => $validated['travelMonth'] ?? null,
            'destination' => $validated['destination'] ?? null,
            'activity' => $validated['activity'] ?? null,
            'packages' => $validated['packages'] ?? [],
            'accommodation' => $validated['accommodation'],
            'exercise' => $validated['exercise'] ?? null,
            'full_name' => $validated['fullName'],
            'country' => $validated['country'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'message' => $validated['message'] ?? null,
            'status' => 'new',
        ]);

        Log::info('Plan Your Trip inquiry received', ['id' => $inquiry->id]);

        // Build date info string for emails
        $dateInfo = match ($validated['dateType'] ?? 'later') {
            'exact' => $validated['arrivalDate'] . ' to ' . ($validated['departureDate'] ?? 'TBD'),
            'certain' => $validated['travelMonth'] ?? 'To be confirmed',
            default => 'Flexible / Not yet decided',
        };

        $exerciseLabel = match ($validated['exercise'] ?? '') {
            'yes' => 'Yes',
            'occasionally' => 'Occasionally',
            'no' => 'No',
            default => 'Not specified',
        };

        // Send confirmation and admin notification emails
        EmailNotificationService::sendBoth(
            $validated['email'],
            'Trip Inquiry Received — Yakspirit Adventures',
            'emails.plan-your-trip-confirmation',
            'New Trip Inquiry — ' . ($validated['fullName']),
            'emails.plan-your-trip-admin-notification',
            [
                'fullName' => $validated['fullName'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'country' => $validated['country'],
                'activity' => $validated['activity'] ?? '',
                'numberOfPeople' => $validated['numberOfPeople'] ?? '',
                'travelGroup' => $validated['travelGroup'],
                'dateInfo' => $dateInfo,
                'accommodation' => $validated['accommodation'],
                'exercise' => $exerciseLabel,
                'packages' => $validated['packages'] ?? [],
                'message' => $validated['message'] ?? '',
            ],
        );

        return response()->json([
            'success' => true,
            'message' => 'Your inquiry has been received. We will get back to you within 24 hours.',
            'data' => $this->serializeInquiry($inquiry),
        ], 201);
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
