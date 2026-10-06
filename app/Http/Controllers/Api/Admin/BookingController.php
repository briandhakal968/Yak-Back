<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Trek;
use App\Services\EmailNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

/**
 * Admin controller for booking management.
 */
class BookingController extends Controller
{
    /**
     * Display a paginated listing of bookings.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Booking::query()->with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search): void {
                $q->where('reference_code', 'like', "%{$search}%")
                  ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(customer_info, '$.email')) like ?", ["%{$search}%"]);
            });
        }

        $paginator = $query->latest()->paginate($request->integer('limit', 12));

        return response()->json([
            'success' => true,
            'message' => 'Bookings retrieved successfully.',
            'data' => [
                'data' => $paginator->map(fn (Booking $booking) => $this->serializeBooking($booking))->values(),
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
     * Store a newly created booking.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->bookingRules());
        $booking = Booking::create([
            ...$this->mapToAttributes($data),
            'reference_code' => Str::upper(Str::random(8)),
            'booked_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking created successfully.',
            'data' => $this->serializeBooking($booking->load('user')),
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified booking.
     */
    public function show(Booking $booking): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Booking retrieved successfully.',
            'data' => $this->serializeBooking($booking->load('user')),
        ]);
    }

    /**
     * Update the specified booking status.
     */
    public function update(Request $request, Booking $booking): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:pending,confirmed,cancelled,completed'],
            'payment' => ['nullable', 'array'],
            'cancellationReason' => ['nullable', 'string', 'max:2000'],
        ]);

        $oldStatus = $booking->status;
        $newStatus = $data['status'];
        $attributes = ['status' => $newStatus];
        if (isset($data['payment'])) {
            $attributes['payment'] = $data['payment'];
        }

        $booking->update($attributes);

        // Send status-change email if status actually changed
        if ($oldStatus !== $newStatus) {
            $customerEmail = $booking->customer_info['email'] ?? null;
            $customerName = trim(($booking->customer_info['firstName'] ?? '') . ' ' . ($booking->customer_info['lastName'] ?? ''));

            if ($customerEmail && $customerName) {
                $tourTitle = $this->resolveTourTitle($booking->details ?? []);
                $date = $booking->details['date'] ?? '';
                $referenceCode = $booking->reference_code ?? '';
                $bookingTypeLabel = match ($booking->type) {
                    'fix-departure' => 'Fix Departure',
                    'trekking' => 'Trekking',
                    'peak-climbing' => 'Peak Climbing',
                    'mountain-biking' => 'Mountain Biking',
                    default => ucfirst(str_replace('-', ' ', $booking->type)),
                };

                $emailData = [
                    'name' => $customerName,
                    'status' => $newStatus,
                    'tourTitle' => $tourTitle,
                    'date' => $date,
                    'referenceCode' => $referenceCode,
                    'bookingType' => $bookingTypeLabel,
                ];

                // Include cancellation reason if status is cancelled
                if ($newStatus === 'cancelled' && !empty($data['cancellationReason'])) {
                    $emailData['cancellationReason'] = $data['cancellationReason'];
                }

                EmailNotificationService::sendCustomerConfirmation(
                    $customerEmail,
                    "Your Booking Status Has Been Updated — Yakspirit Adventures",
                    'emails.booking-status-update',
                    $emailData,
                );
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Booking updated successfully.',
            'data' => $this->serializeBooking($booking->fresh()->load('user')),
        ]);
    }

    /**
     * Remove the specified booking.
     */
    public function destroy(Booking $booking): JsonResponse
    {
        $booking->delete();

        return response()->json([
            'success' => true,
            'message' => 'Booking deleted successfully.',
            'data' => null,
        ]);
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
            'type' => $data['type'],
            'status' => $data['status'],
            'customer_info' => $data['customerInfo'],
            'details' => $data['details'],
            'price' => $data['price'],
            'payment' => $data['payment'] ?? null,
            'user_id' => $data['userId'] ?? null,
        ];
    }

    /**
     * Validation rules for bookings.
     *
     * @return array<string, mixed>
     */
    private function bookingRules(): array
    {
        return [
            'type' => ['required', 'string', 'max:32'],
            'status' => ['required', 'string', 'in:pending,confirmed,cancelled,completed'],
            'customerInfo' => ['required', 'array'],
            'details' => ['required', 'array'],
            'price' => ['required', 'array'],
            'payment' => ['nullable', 'array'],
            'userId' => ['nullable', 'exists:users,id'],
        ];
    }

    /**
     * Serialize a booking for the API.
     *
     * @return array<string, mixed>
     */
    private function serializeBooking(Booking $booking): array
    {
        return [
            'id' => (string) $booking->id,
            'referenceCode' => $booking->reference_code,
            'type' => $booking->type,
            'status' => $booking->status,
            'customer' => $booking->customer_info ?? [],
            'customerInfo' => $booking->customer_info ?? [],
            'details' => $booking->details ?? [],
            'price' => $booking->price ?? [],
            'payment' => $booking->payment ?? [],
            'user' => $booking->user ? [
                'id' => (string) $booking->user->id,
                'name' => $booking->user->name,
                'email' => $booking->user->email,
            ] : null,
            'bookedAt' => $booking->booked_at?->toIso8601String(),
            'createdAt' => $booking->created_at?->toIso8601String(),
            'updatedAt' => $booking->updated_at?->toIso8601String(),
        ];
    }

    /**
     * Resolve the tour display name from stored title or lookup by tourId.
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
