<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SiteSettingController extends Controller
{
    /** Allowed setting groups. */
    private const GROUPS = ['home', 'about', 'contact', 'team', 'faq'];

    /**
     * List every setting, grouped by `group` key.
     */
    public function index(Request $request): JsonResponse
    {
        $query = SiteSetting::query();
        if ($group = $request->input('group')) {
            $query->where('group', $group);
        }
        $settings = $query->orderBy('group')->orderBy('sort_order')->get()
            ->groupBy('group')
            ->map(function ($items) {
                return $items->mapWithKeys(function (SiteSetting $s) {
                    return [$s->key => [
                        'value' => $s->decodedValue(),
                        'type' => $s->type,
                        'group' => $s->group,
                        'label' => $s->label,
                        'description' => $s->description,
                        'sort_order' => $s->sort_order,
                    ]];
                })->toArray();
            });

        return response()->json([
            'success' => true,
            'message' => 'Settings retrieved successfully.',
            'data' => $settings,
        ]);
    }

    /**
     * Show a single setting by its key.
     */
    public function show(string $key): JsonResponse
    {
        $setting = SiteSetting::where('key', $key)->first();
        if (! $setting) {
            return response()->json([
                'success' => false,
                'message' => 'Setting not found.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Setting retrieved successfully.',
            'data' => $this->serialize($setting),
        ]);
    }

    /**
     * Bulk upsert multiple settings at once.
     * Accepts an array of { key, value, type?, group?, label?, description? }.
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'settings' => ['required', 'array', 'min:1'],
            'settings.*.key' => ['required', 'string', 'max:100'],
            'settings.*.value' => ['nullable'],
            'settings.*.type' => ['nullable', 'string', 'in:text,html,json,image'],
            'settings.*.group' => ['nullable', 'string', 'in:' . implode(',', self::GROUPS)],
            'settings.*.label' => ['nullable', 'string', 'max:191'],
            'settings.*.description' => ['nullable', 'string'],
        ]);

        $updated = [];
        foreach ($data['settings'] as $payload) {
            $value = $payload['value'] ?? null;
            if (is_array($value) || is_object($value)) {
                $value = json_encode($value);
            }
            $setting = SiteSetting::updateOrCreate(
                ['key' => $payload['key']],
                [
                    'value' => $value,
                    'type' => $payload['type'] ?? 'text',
                    'group' => $payload['group'] ?? 'general',
                    'label' => $payload['label'] ?? null,
                    'description' => $payload['description'] ?? null,
                ],
            );
            $updated[] = $setting->key;
        }

        // Bust the public cache so changes show up immediately
        Cache::forget('site_settings:all');
        Cache::forget('site_settings:' . ($payload['group'] ?? 'general'));

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully.',
            'data' => ['updated' => $updated],
        ]);
    }

    /**
     * Update a single setting by its key.
     */
    public function update(Request $request, string $key): JsonResponse
    {
        $data = $request->validate([
            'value' => ['nullable'],
            'type' => ['nullable', 'string', 'in:text,html,json,image'],
            'label' => ['nullable', 'string', 'max:191'],
            'description' => ['nullable', 'string'],
        ]);

        $value = $data['value'] ?? null;
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);
        }

        $setting = SiteSetting::updateOrCreate(
            ['key' => $key],
            array_merge(
                ['value' => $value, 'type' => $data['type'] ?? 'text'],
                array_filter([
                    'label' => $data['label'] ?? null,
                    'description' => $data['description'] ?? null,
                ], fn ($v) => $v !== null),
            ),
        );

        return response()->json([
            'success' => true,
            'message' => 'Setting updated successfully.',
            'data' => $this->serialize($setting->fresh()),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function serialize(SiteSetting $setting): array
    {
        return [
            'key' => $setting->key,
            'value' => $setting->decodedValue(),
            'type' => $setting->type,
            'group' => $setting->group,
            'label' => $setting->label,
            'description' => $setting->description,
            'sort_order' => $setting->sort_order,
        ];
    }
}
