<?php

namespace App\Services;

use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetService
{
    /** Default token/code lifetime in minutes. */
    public const LIFETIME_MINUTES = 10;

    /**
     * Create a fresh reset code/token for the given email.
     * Always returns success to avoid leaking which emails are registered.
     */
    public function createResetRequest(string $email): ?PasswordReset
    {
        $user = User::where('email', $email)->first();
        if (!$user) {
            return null;
        }

        // Invalidate any prior unused codes for this email
        PasswordReset::where('email', $email)
            ->whereNull('used_at')
            ->update(['used_at' => now()]);

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $token = Str::random(48);

        $reset = PasswordReset::create([
            'email' => $email,
            'code' => $code,
            'token' => hash('sha256', $token),
            'expires_at' => Carbon::now()->addMinutes(self::LIFETIME_MINUTES),
        ]);

        $this->dispatchEmail($user, $code);

        return $reset;
    }

    /**
     * Verify a 6-digit code for the given email.
     * Marks the reset as verified and returns the PasswordReset row if valid.
     */
    public function verifyCode(string $email, string $code): ?PasswordReset
    {
        $reset = PasswordReset::where('email', $email)
            ->where('code', $code)
            ->whereNull('used_at')
            ->latest('id')
            ->first();

        if (!$reset || !$reset->isUsable()) {
            return null;
        }

        $reset->verified_at = now();
        $reset->save();

        return $reset;
    }

    /**
     * Reset the password using the verified code.
     * Returns true on success, false on failure.
     */
    public function resetPassword(string $email, string $code, string $newPassword): bool
    {
        $reset = PasswordReset::where('email', $email)
            ->where('code', $code)
            ->whereNotNull('verified_at')
            ->whereNull('used_at')
            ->latest('id')
            ->first();

        if (!$reset || $reset->isExpired()) {
            return false;
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            return false;
        }

        $user->password = Hash::make($newPassword);
        $user->save();

        $reset->used_at = now();
        $reset->save();

        // Revoke all access tokens for safety
        $user->tokens()->delete();

        return true;
    }

    /**
     * Send the verification code via email.
     * Falls back to logging the code in non-production environments.
     */
    protected function dispatchEmail(User $user, string $code): void
    {
        $html = view('emails.password-reset-code', [
            'name' => $user->name ?? 'Admin',
            'code' => $code,
            'minutes' => self::LIFETIME_MINUTES,
        ])->render();

        try {
            Mail::html($html, function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Your Yakspirit Adventures verification code');
            });
        } catch (\Throwable $e) {
            Log::warning('Failed to send password reset email', [
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);
        }

        if (!app()->environment('production')) {
            Log::info('[PasswordReset] Verification code', [
                'email' => $user->email,
                'code' => $code,
            ]);
        }
    }
}
