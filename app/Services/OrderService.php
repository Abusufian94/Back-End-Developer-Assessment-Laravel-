<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\Product;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderService
{
    protected $orderRepository;
    protected $productRepository;

    public function __construct(OrderRepository $orderRepository, ProductRepository $productRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
    }

    public function create(array $data): \App\Models\Order
    {
        try {
            return DB::transaction(function () use ($data) {
                $items = collect($data['items'])->map(function ($item) {
                    $product = $this->productRepository->findOrFailWithLock($item['product_id']);
                    if ($product->stock_quantity <= 0) {
                        throw new ApiException(
                            message: "Product {$product->name} is out of stock.",
                            error: 'out_of_stock',
                            code: 400,
                            details: ['product_id' => $product->id]
                        );
                    }
                    if ($product->stock_quantity < $item['quantity']) {
                        throw new ApiException(
                            message: "Insufficient stock for product: {$product->name}. Available: {$product->stock_quantity}.",
                            error: 'insufficient_stock',
                            code: 400,
                            details: ['product_id' => $product->id, 'available' => $product->stock_quantity]
                        );
                    }
                    $item['price'] = $product->price;
                    $subtotal = $product->price * $item['quantity'];
                    return [
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price' => $product->price,
                        'subtotal' => $subtotal,
                    ];
                });

                $totalAmount = $items->sum('subtotal');

                if ($totalAmount > 9999999999.99) {
                    throw new ApiException(
                        message: 'Order total exceeds maximum allowed amount.',
                        error: 'total_amount_exceeded',
                        code: 400,
                        details: ['total_amount' => $totalAmount]
                    );
                }

                Log::info('Creating order with stock updates:', [
                    'user_id' => Auth::id(),
                    'items' => $items->toArray(),
                    'total_amount' => $totalAmount,
                ]);

                $order = $this->orderRepository->create([
                    'user_id' => Auth::id(),
                    'total_amount' => $totalAmount,
                    'status' => 'pending',
                ]);

                $items->each(function ($item) use ($order) {
                    $this->orderRepository->createOrderItem($order, [
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ]);
                    $product = $this->productRepository->findOrFailWithLock($item['product_id']);
                    $newStock = $product->stock_quantity - $item['quantity'];
                    if ($newStock < 0) {
                        throw new ApiException(
                            message: "Stock cannot be negative for product: {$product->name}.",
                            error: 'negative_stock',
                            code: 400,
                            details: ['product_id' => $product->id]
                        );
                    }
                    $product->update(['stock_quantity' => $newStock]);
                    Log::info('Stock updated:', [
                        'product_id' => $item['product_id'],
                        'old_stock' => $product->stock_quantity,
                        'new_stock' => $newStock,
                        'quantity_ordered' => $item['quantity'],
                    ]);
                });

                return $order;
            });
        } catch (\Exception $e) {
            Log::error('Order creation failed:', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
            throw new ApiException(
                message: 'Failed to create order.',
                error: 'order_creation_failed',
                code: 400,
                details: ['reason' => $e->getMessage()]
            );
        }
    }

    public function find(int $id): \App\Models\Order
    {
        try {
            return $this->orderRepository->findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new ApiException(
                message: 'Order not found.',
                error: 'order_not_found',
                code: 404,
                details: []
            );
        }
    }

    public function getPaginated(int $perPage = 10): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->orderRepository->getPaginated($perPage);
    }
}