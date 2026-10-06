<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

/**
 * Public FAQ controller.
 */
class FaqController extends Controller
{
    /**
     * Display a listing of active FAQs.
     */
    public function index(): JsonResponse
    {
        $faqs = Faq::query()
            ->where('is_active', true)
            ->orderBy('display_order')
            ->orderBy('id')
            ->get()
            ->map(fn (Faq $faq) => $this->serialize($faq))
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'FAQs retrieved successfully.',
            'data' => [
                'faqs' => $faqs,
                'categories' => [],
            ],
        ]);
    }

    /**
     * Serialize a FAQ for the API.
     *
     * @return array<string, mixed>
     */
    private function serialize(Faq $faq): array
    {
        return [
            'id' => $faq->external_id,
            'externalId' => $faq->external_id,
            'question' => $faq->question,
            'answer' => $faq->answer,
            'category' => $faq->category ?? '',
            'order' => (int) $faq->display_order,
            'isActive' => (bool) $faq->is_active,
            'createdAt' => $faq->created_at?->toIso8601String() ?? '',
            'updatedAt' => $faq->updated_at?->toIso8601String() ?? '',
        ];
    }
}
