<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SiteSettingController extends Controller
{
    /** Cache lifetime in seconds. */
    private const CACHE_TTL = 300; // 5 minutes

    /**
     * Public read of settings, optionally filtered by group.
     * Returns a flat key-value map ready to consume on the frontend.
     * Cached for performance.
     */
    public function index(Request $request): JsonResponse
    {
        $group = $request->input('group');
        $cacheKey = 'site_settings:' . ($group ?? 'all');

        $flat = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($group) {
            $query = SiteSetting::query();
            if ($group) {
                $query->where('group', $group);
            }
            $settings = $query->orderBy('sort_order')->get();

            return $settings->mapWithKeys(function (SiteSetting $s) {
                return [$s->key => $s->decodedValue()];
            })->toArray();
        });

        return response()->json([
            'success' => true,
            'message' => 'Settings retrieved successfully.',
            'data' => $flat,
        ]);
    }
}
