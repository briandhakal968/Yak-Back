<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PasswordResetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Authentication controller for customers and admins.
 */
class AuthController extends Controller
{
    public function __construct(private readonly PasswordResetService $passwordResets)
    {
    }

    /**
     * Authenticate a user and issue a Sanctum token.
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()->where('email', $credentials['email'])->first();

        if ($user === null || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'data' => [
                'user' => $this->serializeUser($user),
                'token' => $token,
                'refreshToken' => $token,
            ],
        ]);
    }

    /**
     * Revoke the current user's token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful.',
            'data' => null,
        ]);
    }

    /**
     * Return the authenticated user's profile.
     */
    public function profile(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Profile retrieved successfully.',
            'data' => $this->serializeUser($request->user()),
        ]);
    }

    /**
     * Update the authenticated user's profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['sometimes', 'string', 'min:8'],
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data' => $this->serializeUser($user->fresh()),
        ]);
    }

    /**
     * Start the password-reset flow.
     * Always returns the same message regardless of whether the email exists.
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $this->passwordResets->createResetRequest($data['email']);

        return response()->json([
            'success' => true,
            'message' => 'If an account exists for that email, a verification code has been sent.',
            'data' => null,
        ]);
    }

    /**
     * Verify the 6-digit code sent to the user.
     */
    public function verifyCode(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'code' => ['required', 'string', 'size:6'],
        ]);

        $reset = $this->passwordResets->verifyCode($data['email'], $data['code']);

        if ($reset === null) {
            throw ValidationException::withMessages([
                'code' => ['Invalid or expired verification code.'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Code verified successfully.',
            'data' => null,
        ]);
    }

    /**
     * Reset the user's password using a previously verified code.
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'code' => ['required', 'string', 'size:6'],
            'password' => ['required', 'string', 'min:8'],
            'password_confirmation' => ['required', 'string', 'same:password'],
        ]);

        $ok = $this->passwordResets->resetPassword(
            $data['email'],
            $data['code'],
            $data['password'],
        );

        if (! $ok) {
            throw ValidationException::withMessages([
                'code' => ['Invalid or expired verification code. Please start over.'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Password has been reset successfully.',
            'data' => null,
        ]);
    }

    /**
     * Serialize a user for the auth response.
     *
     * @return array<string, mixed>
     */
    private function serializeUser(User $user): array
    {
        $nameParts = explode(' ', $user->name, 2);

        return [
            'id' => (string) $user->id,
            'name' => $user->name,
            'firstName' => $nameParts[0] ?? '',
            'lastName' => $nameParts[1] ?? '',
            'email' => $user->email,
            'role' => $user->role,
            'createdAt' => $user->created_at?->toIso8601String(),
            'updatedAt' => $user->updated_at?->toIso8601String(),
        ];
    }
}
