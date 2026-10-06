<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\EmailNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class FixDepartureController extends Controller
{
    /**
     * Store a new fix departure booking.
     *
     * Reuses the existing `bookings` table with type = 'fix-departure'.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'tourTitle' => ['required', 'string', 'max:255'],
            'date' => ['required', 'string', 'max:32'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:64'],
            'country' => ['nullable', 'string', 'max:128'],
            'groupSize' => ['required', 'string', 'max:32'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        $referenceCode = strtoupper(Str::random(8));

        $booking = Booking::create([
            'reference_code' => $referenceCode,
            'type' => 'fix-departure',
            'status' => 'pending',
            'customer_info' => [
                'firstName' => $data['name'],
                'lastName' => '',
                'email' => $data['email'],
                'phone' => $data['phone'],
                'country' => $data['country'] ?? '',
            ],
            'details' => [
                'tourTitle' => $data['tourTitle'],
                'date' => $data['date'],
                'groupSize' => $data['groupSize'],
                'specialRequests' => $data['message'] ?? '',
            ],
            'price' => ['amount' => 0, 'currency' => 'USD', 'label' => 'To be determined'],
            'booked_at' => now(),
        ]);

        // Send emails (fire-and-forget style — failures are logged, not thrown)
        EmailNotificationService::sendBoth(
            $data['email'],
            'Your Fix Departure Booking — Yakspirit Adventures',
            'emails.fix-departure-confirmation',
            'New Fix Departure Booking — ' . $data['tourTitle'],
            'emails.fix-departure-admin-notification',
            [
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'country' => $data['country'] ?? '',
                'tourTitle' => $data['tourTitle'],
                'date' => $data['date'],
                'groupSize' => $data['groupSize'],
                'message' => $data['message'] ?? '',
                'referenceCode' => $referenceCode,
            ],
        );

        return response()->json([
            'success' => true,
            'message' => 'Fix departure booking submitted successfully.',
            'data' => [
                'referenceCode' => $referenceCode,
                'id' => $booking->id,
            ],
        ], Response::HTTP_CREATED);
    }
}
