<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Admin controller for managing contact form submissions.
 */
class ContactSubmissionController extends Controller
{
    /**
     * Display a paginated listing of contact submissions.
     */
    public function index(Request $request): JsonResponse
    {
        $query = ContactSubmission::query();

        if ($request->filled('status')) {
            if ($request->input('status') === 'read') {
                $query->whereNotNull('read_at');
            } elseif ($request->input('status') === 'unread') {
                $query->whereNull('read_at');
            }
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $paginator = $query->latest()->paginate($request->integer('limit', 12));

        return response()->json([
            'success' => true,
            'message' => 'Contact submissions retrieved successfully.',
            'data' => [
                'data' => $paginator->map(fn (ContactSubmission $s) => $this->serialize($s))->values(),
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
     * Display the specified contact submission.
     */
    public function show(ContactSubmission $contact): JsonResponse
    {
        // Mark as read on first view.
        if ($contact->read_at === null) {
            $contact->update(['read_at' => now()]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Contact submission retrieved successfully.',
            'data' => $this->serialize($contact->fresh()),
        ]);
    }

    /**
     * Remove the specified contact submission.
     */
    public function destroy(ContactSubmission $contact): JsonResponse
    {
        $contact->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contact submission deleted successfully.',
            'data' => null,
        ]);
    }

    /**
     * Serialize a contact submission for the API.
     *
     * @return array<string, mixed>
     */
    private function serialize(ContactSubmission $s): array
    {
        return [
            'id' => (string) $s->id,
            'name' => $s->name,
            'email' => $s->email,
            'phone' => $s->phone ?? '',
            'country' => $s->country ?? '',
            'city' => $s->city ?? '',
            'subject' => $s->subject,
            'message' => $s->message,
            'ipAddress' => $s->ip_address,
            'userAgent' => $s->user_agent,
            'readAt' => $s->read_at?->toIso8601String(),
            'createdAt' => $s->created_at?->toIso8601String(),
            'updatedAt' => $s->updated_at?->toIso8601String(),
        ];
    }
}
