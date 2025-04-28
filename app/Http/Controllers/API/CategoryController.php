<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getPaginated();
        return new JsonResponse([
            'data' => CategoryResource::collection($categories),
            'status' => 'success',
            'message' => 'Categories retrieved successfully',
        ], 200);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $data = $request->validated();
        $category = $this->categoryService->create($data);
        return new JsonResponse([
            'data' => new CategoryResource($category),
            'status' => 'success',
            'message' => 'Category created successfully',
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $category = $this->categoryService->find($id);
        return new JsonResponse([
            'data' => new CategoryResource($category),
            'status' => 'success',
            'message' => 'Category retrieved successfully',
        ], 200);
    }

    public function update(UpdateCategoryRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();
        $category = $this->categoryService->update($id, $data);
        return new JsonResponse([
            'data' => new CategoryResource($category),
            'status' => 'success',
            'message' => 'Category updated successfully',
        ], 200);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->categoryService->delete($id);
        return new JsonResponse([
            'data' => null,
            'status' => 'success',
            'message' => 'Category deleted successfully',
        ], 200);
    }

}
