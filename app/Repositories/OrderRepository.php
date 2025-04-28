<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class OrderRepository
{
    public function create(array $data): Order
    {
        return Order::create($data);
    }

    public function findOrFail(int $id): Order
    {
        return Order::where('user_id', Auth::id())->findOrFail($id);
    }

    public function getPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return Order::where('user_id', Auth::id())->paginate($perPage);
    }

    public function createOrderItem(Order $order, array $data): void
    {
        $order->orderItems()->create($data);
    }
}