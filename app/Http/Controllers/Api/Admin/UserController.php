<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Admin controller for user management.
 */
class UserController extends Controller
{
    /**
     * Display a paginated listing of users.
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        $paginator = $query->latest()->paginate($request->integer('limit', 12));

        return response()->json([
            'success' => true,
            'message' => 'Users retrieved successfully.',
            'data' => [
                'data' => $paginator->map(fn (User $user) => $this->serializeUser($user))->values(),
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
     * Store a newly created user.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->userRules());
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'customer',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully.',
            'data' => $this->serializeUser($user),
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'User retrieved successfully.',
            'data' => $this->serializeUser($user),
        ]);
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $data = $request->validate($this->userRules($user));
        $attributes = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'] ?? $user->role,
        ];

        if (! empty($data['password'])) {
            $attributes['password'] = Hash::make($data['password']);
        }

        $user->update($attributes);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully.',
            'data' => $this->serializeUser($user->fresh()),
        ]);
    }

    /**
     * Remove the specified user.
     */
    public function destroy(Request $request, User $user): JsonResponse
    {
        // Don't let an admin delete themselves.
        if ($request->user() && $request->user()->id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.',
            ], 403);
        }

        // Don't delete the last remaining admin (would lock everyone out).
        if ($user->isAdmin()) {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete the last remaining admin.',
                ], 403);
            }
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.',
            'data' => null,
        ]);
    }

    /**
     * Validation rules for users.
     *
     * @return array<string, mixed>
     */
    private function userRules(?User $user = null): array
    {
        $emailRule = Rule::unique('users', 'email');
        if ($user !== null) {
            $emailRule->ignore($user->id);
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', $emailRule],
            'password' => [$user === null ? 'required' : 'nullable', 'string', 'min:8'],
            'role' => ['required', 'string', 'in:admin'],
        ];
    }

    /**
     * Serialize a user for the API.
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
