<?php

namespace App\Services;

use App\Jobs\SendEmailJob;
use Illuminate\Support\Facades\Log;

class EmailNotificationService
{
    /** Admin emails that receive all notification emails. */
    private const ADMIN_EMAILS = [
        'info@yakspiritadventures.com',
        'sanjib.machang@gmail.com',
    ];

    /**
     * Send a confirmation email to the customer via the queue.
     */
    public static function sendCustomerConfirmation(
        string $email,
        string $subject,
        string $view,
        array $data = [],
    ): void {
        try {
            SendEmailJob::dispatch($email, $subject, $view, $data);
        } catch (\Throwable $e) {
            Log::warning('Failed to queue customer confirmation email', [
                'email' => $email,
                'subject' => $subject,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send a notification email to all admin addresses via the queue.
     */
    public static function sendAdminNotification(
        string $subject,
        string $view,
        array $data = [],
    ): void {
        try {
            foreach (self::ADMIN_EMAILS as $adminEmail) {
                SendEmailJob::dispatch($adminEmail, $subject, $view, $data);
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to queue admin notification email', [
                'subject' => $subject,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send both customer confirmation and admin notification emails.
     */
    public static function sendBoth(
        string $customerEmail,
        string $customerSubject,
        string $customerView,
        string $adminSubject,
        string $adminView,
        array $data = [],
    ): void {
        self::sendCustomerConfirmation($customerEmail, $customerSubject, $customerView, $data);
        self::sendAdminNotification($adminSubject, $adminView, $data);
    }
}
