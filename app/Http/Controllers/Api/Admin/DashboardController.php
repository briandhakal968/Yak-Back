<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BlogPost;
use App\Models\Page;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Http\JsonResponse;

/**
 * Admin dashboard statistics controller.
 */
class DashboardController extends Controller
{
    /**
     * Return aggregated counts for the admin dashboard.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Dashboard statistics retrieved successfully.',
            'data' => [
                'tours' => Tour::count(),
                'blogPosts' => BlogPost::count(),
                'pages' => Page::count(),
                'bookings' => Booking::count(),
                'users' => User::count(),
                'pendingBookings' => Booking::where('status', 'pending')->count(),
            ],
        ]);
    }
}
