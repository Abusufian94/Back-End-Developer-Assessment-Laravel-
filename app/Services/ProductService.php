<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Repositories\ProductRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ProductService
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    protected function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (Product::where('slug', $slug)
            ->when($excludeId, fn($query) => $query->where('id', '!=', $excludeId))
            ->exists()
        ) {
            $slug = $originalSlug . '-' . $counter++;
        }

        return $slug;
    }

    protected function storeImages(array $images): array
    {
        try {
            return collect($images)
                ->filter(fn($image) => $image instanceof UploadedFile)
                ->map(function (UploadedFile $image) {
                    $path = $image->store('products', 'public');
                    return asset('storage/' . $path);
                })
                ->values()
                ->toArray();
        } catch (\Exception $e) {
            throw new ApiException(
                message: 'Failed to store images.',
                error: 'image_upload_failed',
                code: 400,
                details: ['reason' => $e->getMessage()]
            );
        }
    }

    public function create(array $data): Product
    {
        $images = isset($data['images']) && is_array($data['images'])
            ? $this->storeImages($data['images'])
            : [];

        $productData = [
            'name' => $data['name'],
            'slug' => $this->generateUniqueSlug($data['name']),
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'stock_quantity' => $data['stock_quantity'],
            'images' => $images,
        ];

        if (isset($data['category_ids'])) {
            $productData['category_ids'] = $data['category_ids'];
        }
        Cache::flush();
        Log::info('Product cache invalidated after create');
        return $this->productRepository->create($productData);
    }

    public function update(int $id, array $data): Product
    {
        $product = $this->productRepository->findOrFail($id);

        \Log::info('Update images:', [
            'images' => isset($data['images']) ? array_map(fn($file) => $file->getClientOriginalName(), $data['images']) : 'none',
            'is_array' => isset($data['images']) && is_array($data['images']),
        ]);

        $images = isset($data['images']) && is_array($data['images'])
            ? collect($product->images ?? [])->merge($this->storeImages($data['images']))->values()->toArray()
            : ($product->images ?? []);

        $productData = [
            'name' => $data['name'] ?? $product->name,
            'slug' => isset($data['name']) ? $this->generateUniqueSlug($data['name'], $id) : $product->slug,
            'description' => $data['description'] ?? $product->description,
            'price' => $data['price'] ?? $product->price,
            'stock_quantity' => $data['stock_quantity'] ?? $product->stock_quantity,
            'images' => $images,
        ];

        if (isset($data['category_ids'])) {
            $productData['category_ids'] = $data['category_ids'];
        }
          // Invalidate product listing caches
          Cache::flush();
          Log::info('Product cache invalidated after update');
        return $this->productRepository->update($product, $productData);
    }

    public function delete(int $id): void
    {
        $product = $this->productRepository->findOrFail($id);
        collect($product->images ?? [])
            ->each(function ($image) {
                $relativePath = str_replace(asset('storage/'), '', $image);
                Storage::disk('public')->delete($relativePath);
            });
            Cache::flush();
        Log::info('Product cache invalidated after delete');
        $this->productRepository->delete($product);
    }

    public function find(int $id): Product
    {
        return $this->productRepository->findOrFail($id);
    }

    public function getPaginated(int $perPage = 10, ?int $categoryId = null): LengthAwarePaginator
    {
        return $this->productRepository->getPaginated($perPage, $categoryId);
    }

    public function getInventory( $perPage, $categoryId, $lowStockThreshold)
    {
        $cacheKey = 'inventory_' . md5("per_page_{$perPage}_category_{$categoryId}_low_stock_{$lowStockThreshold}_page_" . request()->query('page', 1));
        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($perPage, $categoryId, $lowStockThreshold) {
            Log::info('Cache miss for inventory', ['cache_key' => $cacheKey]);
            $query = Product::query()->select('id', 'name', 'slug', 'stock_quantity', 'price');

            if ($categoryId) {
                $query->whereHas('categories', fn($q) => $q->where('categories.id', $categoryId));
            }

            if ($lowStockThreshold !== null) {
                $query->where('stock_quantity', '<=', $lowStockThreshold);
            }

            return $query->paginate($perPage);
        });
    }

   
    public function adjustStock(int $productId, int $adjustment): Product
    {
        try {
            $product = $this->productRepository->findOrFailWithLock($productId);
            $newStock = $product->stock_quantity + $adjustment;

            if ($newStock < 0) {
                throw new ApiException(
                    message: "Stock cannot be negative for product: {$product->name}.",
                    error: 'negative_stock',
                    code: 400,
                    details: ['product_id' => $productId, 'current_stock' => $product->stock_quantity]
                );
            }

            $product->update(['stock_quantity' => $newStock]);

            Log::info('Stock adjusted:', [
                'product_id' => $productId,
                'old_stock' => $product->stock_quantity,
                'adjustment' => $adjustment,
                'new_stock' => $newStock,
            ]);
            Cache::flush();
            Log::info('Product cache invalidated after stock adjustment');

            return $product;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new ApiException(
                message: 'Product not found.',
                error: 'product_not_found',
                code: 404,
                details: []
            );
        } catch (\Exception $e) {
            throw new ApiException(
                message: 'Failed to adjust stock.',
                error: 'stock_adjustment_failed',
                code: 400,
                details: ['reason' => $e->getMessage()]
            );
        }
    }
}