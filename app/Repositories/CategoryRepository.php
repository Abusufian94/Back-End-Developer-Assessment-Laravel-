<?php

namespace App\Repositories;

use App\Models\Category;
use App\Exceptions\ApiException;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoryRepository
{
    public function create(array $data): Category
    {
        
        try {
            return Category::create($data);
        } catch (\Exception $e) {
            throw new ApiException(
                message: 'Failed to assign role.',
                error: 'role_assignment_failed',
                code: 400,
                details: ['reason' => $e->getMessage()]
            );
        }

    }

    public function update(Category $category, array $data): Category
    {
        
        try {
            $category->update($data);
            return $category->fresh();

        } catch (\Exception $e) {
            throw new ApiException(
                message: 'Failed to assign role.',
                error: 'role_assignment_failed',
                code: 400,
                details: ['reason' => $e->getMessage()]
            );
        }
    }

    public function delete(Category $category): void
    {
        
        
        $category->delete();
    }

    public function findOrFail(int $id): Category
    {
        return Category::findOrFail($id);
    }

    public function getPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return Category::query()->paginate($perPage);
    }
}