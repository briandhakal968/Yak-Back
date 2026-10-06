<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Trek;
use App\Services\EmailNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Public booking API controller.
 */
class BookingController extends Controller
{
    /**
     * Store a newly created booking from the frontend checkout.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->bookingRules());

        $booking = Booking::create([
            'reference_code' => Str::upper(Str::random(8)),
            'type' => $data['type'],
            'status' => 'pending',
            'customer_info' => $data['customerInfo'],
            'details' => $data['details'],
            'price' => $data['price'],
            'payment' => $data['payment'] ?? null,
            'user_id' => Auth::id(),
            'booked_at' => now(),
        ]);

        // Send confirmation and admin notification emails
        $customer = $data['customerInfo'];
        $details = $data['details'];
        $price = $data['price'];
        $passengers = $details['passengers'] ?? 1;
        $totalPrice = $price['amount'] * $passengers;
        $tourTitle = $this->resolveTourTitle($details);
        EmailNotificationService::sendBoth(
            $customer['email'],
            'Booking Confirmed — Yakspirit Adventures',
            'emails.booking-confirmation',
            'New Booking — ' . $tourTitle,
            'emails.booking-admin-notification',
            [
                'fullName' => trim($customer['firstName'] . ' ' . $customer['lastName']),
                'tourTitle' => $tourTitle,
                'date' => $details['date'] ?? 'N/A',
                'passengers' => $passengers,
                'specialRequests' => $details['specialRequests'] ?? '',
                'customerEmail' => $customer['email'],
                'phone' => $customer['phone'],
                'country' => $customer['country'],
                'city' => $customer['city'],
                'pricePerPerson' => $price['amount'],
                'priceCurrency' => $price['currency'],
                'priceLabel' => $price['label'] ?? '',
                'totalPrice' => $totalPrice,
            ],
        );

        return response()->json([
            'success' => true,
            'message' => 'Booking created successfully.',
            'data' => [
                'id' => (string) $booking->id,
                'referenceCode' => $booking->reference_code,
                'type' => $booking->type,
                'status' => $booking->status,
                'customerInfo' => $booking->customer_info,
                'details' => $booking->details,
                'price' => $booking->price,
                'createdAt' => $booking->created_at?->toIso8601String(),
            ],
        ], Response::HTTP_CREATED);
    }

    /**
     * Validation rules for public bookings.
     *
     * @return array<string, mixed>
     */
    private function bookingRules(): array
    {
        return [
            'type' => ['required', 'string', 'max:32'],
            'customerInfo' => ['required', 'array'],
            'customerInfo.firstName' => ['required', 'string', 'max:255'],
            'customerInfo.lastName' => ['required', 'string', 'max:255'],
            'customerInfo.email' => ['required', 'email', 'max:255'],
            'customerInfo.phone' => ['required', 'string', 'max:255'],
            'customerInfo.country' => ['required', 'string', 'max:255'],
            'customerInfo.city' => ['required', 'string', 'max:255'],
            'details' => ['required', 'array'],
            'details.tourId' => ['nullable', 'string', 'max:64'],
            'details.tourTitle' => ['nullable', 'string', 'max:255'],
            'details.date' => ['required', 'date'],
            'details.passengers' => ['required', 'integer', 'min:1'],
            'details.specialRequests' => ['nullable', 'string'],
            'price' => ['required', 'array'],
            'price.amount' => ['required', 'numeric', 'min:0'],
            'price.currency' => ['required', 'string', 'max:8'],
            'price.label' => ['nullable', 'string'],
            'payment' => ['nullable', 'array'],
        ];
    }

    /**
     * Resolve the tour display name.
     *
     * Priority:
     *   1. Stored tourTitle in the request
     *   2. Look up by tourId from the treks table
     *   3. Default fallback
     */
    private function resolveTourTitle(array $details): string
    {
        if (!empty($details['tourTitle'])) {
            return $details['tourTitle'];
        }

        if (!empty($details['tourId'])) {
            $trek = Trek::find($details['tourId']);
            if ($trek && !empty($trek->title)) {
                return $trek->title;
            }
        }

        return 'Tour';
    }
}
