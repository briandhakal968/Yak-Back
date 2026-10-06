<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use App\Services\EmailNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Handles public contact form submissions.
 */
class ContactController extends Controller
{
    /**
     * Validate and persist a contact form submission.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:64',
            'country' => 'nullable|string|max:128',
            'city' => 'nullable|string|max:128',
            'subject' => 'required|string|min:2|max:255',
            'message' => 'nullable|string|max:5000',
        ]);

        $submission = ContactSubmission::create([
            ...$validated,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
        ]);

        // Send confirmation and admin notification emails
        EmailNotificationService::sendBoth(
            $validated['email'],
            'Message Received — Yakspirit Adventures',
            'emails.contact-confirmation',
            'New Contact Message — ' . $validated['subject'],
            'emails.contact-admin-notification',
            [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? '',
                'country' => $validated['country'] ?? '',
                'city' => $validated['city'] ?? '',
                'subject' => $validated['subject'],
                'message' => $validated['message'],
            ],
        );

        return response()->json([
            'success' => true,
            'message' => 'Thank you for contacting us. We will get back to you soon.',
            'data' => $this->serialize($submission),
        ], 201);
    }

    /**
     * Serialize a submission for the API.
     *
     * @return array<string, mixed>
     */
    private function serialize(ContactSubmission $submission): array
    {
        return [
            'id' => (string) $submission->id,
            'name' => $submission->name,
            'email' => $submission->email,
            'phone' => $submission->phone,
            'country' => $submission->country,
            'city' => $submission->city,
            'subject' => $submission->subject,
            'message' => $submission->message,
            'readAt' => $submission->read_at?->toIso8601String(),
            'createdAt' => $submission->created_at?->toIso8601String(),
            'updatedAt' => $submission->updated_at?->toIso8601String(),
        ];
    }
}
