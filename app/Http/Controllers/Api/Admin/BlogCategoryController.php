<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

/**
 * Admin CRUD controller for blog categories.
 */
class BlogCategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(): JsonResponse
    {
        $categories = BlogCategory::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'message' => 'Blog categories retrieved successfully.',
            'data' => $categories->map(fn (BlogCategory $category) => $this->serializeCategory($category))->values(),
        ]);
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->categoryRules());
        $category = BlogCategory::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully.',
            'data' => $this->serializeCategory($category),
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified category.
     */
    public function show(BlogCategory $category): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Category retrieved successfully.',
            'data' => $this->serializeCategory($category),
        ]);
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, BlogCategory $category): JsonResponse
    {
        $data = $request->validate($this->categoryRules($category));
        $category->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully.',
            'data' => $this->serializeCategory($category->fresh()),
        ]);
    }

    /**
     * Remove the specified category.
     */
    public function destroy(BlogCategory $category): JsonResponse
    {
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully.',
            'data' => null,
        ]);
    }

    /**
     * Validation rules for categories.
     *
     * @return array<string, mixed>
     */
    private function categoryRules(?BlogCategory $category = null): array
    {
        $slugRule = Rule::unique('blog_categories', 'slug');
        if ($category !== null) {
            $slugRule->ignore($category->id);
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', $slugRule],
            'description' => ['nullable', 'string'],
        ];
    }

    /**
     * Serialize a category for the API.
     *
     * @return array<string, mixed>
     */
    private function serializeCategory(BlogCategory $category): array
    {
        return [
            'id' => (string) $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'description' => $category->description,
            'postCount' => $category->posts()->count(),
        ];
    }
}
