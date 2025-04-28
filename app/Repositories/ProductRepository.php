<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductRepository
{
    /**
     * Create a new product.
     *
     * @param array $data
     * @return Product
     * @throws ApiException
     */
    public function create(array $data): Product
    {
        try {
            $product = Product::create($data);
            if (isset($data['category_ids'])) {
                $product->categories()->sync($data['category_ids']);
            }
            return $product;
        } catch (\Exception $e) {
            throw new ApiException(
                message: 'Failed to create product.',
                error: 'product_creation_failed',
                code: 400,
                details: ['reason' => $e->getMessage()]
            );
        }
    }

    /**
     * Update an existing product.
     *
     * @param Product $product
     * @param array $data
     * @return Product
     * @throws ApiException
     */
    public function update(Product $product, array $data): Product
    {
        try {
            $product->update($data);
            if (isset($data['category_ids'])) {
                $product->categories()->sync($data['category_ids']);
            }
            return $product;
        } catch (\Exception $e) {
            throw new ApiException(
                message: 'Failed to update product.',
                error: 'product_update_failed',
                code: 400,
                details: ['reason' => $e->getMessage()]
            );
        }
    }

    /**
     * Delete a product.
     *
     * @param Product $product
     * @return void
     * @throws ApiException
     */
    public function delete(Product $product): void
    {
        try {
            $product->delete();
        } catch (\Exception $e) {
            throw new ApiException(
                message: 'Failed to delete product.',
                error: 'product_deletion_failed',
                code: 400,
                details: ['reason' => $e->getMessage()]
            );
        }
    }

    /**
     * Find a product by ID.
     *
     * @param int $id
     * @return Product
     * @throws ApiException
     */
    public function findOrFail(int $id): Product
    {
        try {
            return Product::findOrFail($id);
        } catch (\Exception $e) {
            throw new ApiException(
                message: 'Product not found.',
                error: 'product_not_found',
                code: 404,
                details: ['reason' => $e->getMessage()]
            );
        }
    }

    /**
     * Get paginated products, optionally filtered by category.
     *
     * @param int $perPage
     * @param int|null $categoryId
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getPaginated(int $perPage = 10, ?int $categoryId = null): LengthAwarePaginator
    {
        try {
            $query = Product::query();
            if ($categoryId) {
                $query->whereHas('categories', fn($q) => $q->where('categories.id', $categoryId));
            }
            return $query->paginate($perPage);
        } catch (\Exception $e) {
            throw new ApiException(
                message: 'Failed to retrieve products.',
                error: 'product_retrieval_failed',
                code: 400,
                details: ['reason' => $e->getMessage()]
            );
        }
    }

    public function findOrFailWithLock(int $id): Product
    {

        try {
            return Product::lockForUpdate()->findOrFail($id);

            return $query->paginate($perPage);
        } catch (\Exception $e) {
            throw new ApiException(
                message: 'Failed to retrieve products.',
                error: 'product_retrieval_failed',
                code: 400,
                details: ['reason' => $e->getMessage()]
            );
        }
    }
 
}