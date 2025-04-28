<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Repositories\CategoryRepository;
use Illuminate\Support\Str;
use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoryService
{
    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    protected function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (Category::where('slug', $slug)
            ->when($excludeId, fn($query) => $query->where('id', '!=', $excludeId))
            ->exists()
        ) {
            $slug = $originalSlug . '-' . $counter++;
        }

        return $slug;
    }

    public function create(array $data): Category
    {
        $categoryData = [
            'name' => $data['name'],
            'slug' => $this->generateUniqueSlug($data['name']),
        ];

        try {
            return $this->categoryRepository->create($categoryData);
        } catch (\Exception $e) {
            throw new ApiException(
                message: 'Failed to create category.',
                error: 'category_creation_failed',
                code: 400,
                details: ['reason' => $e->getMessage()]
            );
        }
    }

    public function update(int $id, array $data): Category
    {
        $category = $this->categoryRepository->findOrFail($id);

        $categoryData = [
            'name' => $data['name'] ?? $category->name,
            'slug' => isset($data['name']) ? $this->generateUniqueSlug($data['name'], $id) : $category->slug,
        ];

        try {
            return $this->categoryRepository->update($category, $categoryData);
        } catch (\Exception $e) {
            throw new ApiException(
                message: 'Failed to update category.',
                error: 'category_update_failed',
                code: 400,
                details: ['reason' => $e->getMessage()]
            );
        }
    }

    public function delete(int $id): void
    {
        $category = $this->categoryRepository->findOrFail($id);

        try {
            $this->categoryRepository->delete($category);
        } catch (\Exception $e) {
            throw new ApiException(
                message: 'Failed to delete category.',
                error: 'category_deletion_failed',
                code: 400,
                details: ['reason' => $e->getMessage()]
            );
        }
    }

    public function find(int $id): Category
    {
        return $this->categoryRepository->findOrFail($id);
    }

    public function getPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return $this->categoryRepository->getPaginated($perPage);
    }
}