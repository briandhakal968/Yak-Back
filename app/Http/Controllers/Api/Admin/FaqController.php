<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

/**
 * Admin FAQ controller.
 */
class FaqController extends Controller
{
    /**
     * Display a paginated listing of FAQs.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Faq::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search): void {
                $q->where('question', 'like', "%{$search}%")
                  ->orWhere('answer', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && in_array($request->input('status'), ['active', 'inactive'], true)) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $paginator = $query
            ->orderBy('display_order')
            ->orderBy('id')
            ->paginate($request->integer('limit', 40));

        return response()->json([
            'success' => true,
            'message' => 'FAQs retrieved successfully.',
            'data' => [
                'data' => $paginator->map(fn (Faq $f) => $this->serialize($f))->values(),
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
     * Store a newly created FAQ.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->faqRules());
        $faq = Faq::create([
            'external_id' => Str::random(16),
            'question' => $data['question'],
            'answer' => $data['answer'],
            'category' => $data['category'] ?? null,
            'display_order' => $data['order'] ?? 0,
            'is_active' => $data['isActive'] ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'FAQ created successfully.',
            'data' => $this->serialize($faq),
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified FAQ (looked up by external_id).
     */
    public function show(string $externalId): JsonResponse
    {
        $faq = $this->findByExternalId($externalId);
        if (!$faq) {
            return $this->notFound();
        }

        return response()->json([
            'success' => true,
            'message' => 'FAQ retrieved successfully.',
            'data' => $this->serialize($faq),
        ]);
    }

    /**
     * Update the specified FAQ (looked up by external_id).
     */
    public function update(Request $request, string $externalId): JsonResponse
    {
        $faq = $this->findByExternalId($externalId);
        if (!$faq) {
            return $this->notFound();
        }

        $data = $request->validate($this->faqRules());
        $faq->update([
            'question' => $data['question'],
            'answer' => $data['answer'],
            'category' => $data['category'] ?? null,
            'display_order' => $data['order'] ?? 0,
            'is_active' => $data['isActive'] ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'FAQ updated successfully.',
            'data' => $this->serialize($faq->fresh()),
        ]);
    }

    /**
     * Remove the specified FAQ (looked up by external_id).
     */
    public function destroy(string $externalId): JsonResponse
    {
        $faq = $this->findByExternalId($externalId);
        if (!$faq) {
            return $this->notFound();
        }

        $faq->delete();

        return response()->json([
            'success' => true,
            'message' => 'FAQ deleted successfully.',
            'data' => null,
        ]);
    }

    /**
     * Look up a FAQ by its external_id.
     */
    private function findByExternalId(string $externalId): ?Faq
    {
        return Faq::where('external_id', $externalId)->first();
    }

    /**
     * Return a 404 JSON response.
     */
    private function notFound(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'FAQ not found.',
            'data' => null,
        ], Response::HTTP_NOT_FOUND);
    }

    /**
     * Validation rules for FAQ create/update.
     *
     * @return array<string, mixed>
     */
    private function faqRules(): array
    {
        return [
            'question' => ['required', 'string', 'max:500'],
            'answer' => ['required', 'string'],
            'category' => ['nullable', 'string', 'max:64'],
            'order' => ['nullable', 'integer', 'min:0'],
            'isActive' => ['nullable', 'boolean'],
        ];
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
            'createdAt' => $faq->created_at?->toIso8601String(),
            'updatedAt' => $faq->updated_at?->toIso8601String(),
        ];
    }
}
