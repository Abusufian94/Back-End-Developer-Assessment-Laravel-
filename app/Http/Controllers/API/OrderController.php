<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;


class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(): JsonResponse
    {
        $orders = $this->orderService->getPaginated();
        if(count($orders)>0)
        {
            return new JsonResponse([
                'data' => OrderResource::collection($orders),
                'status' => 'success',
                'message' => 'Order history retrieved successfully',
            ], 200);
        }   
        else
        {
            return new JsonResponse([
                'data' => OrderResource::collection($orders),
                'status' => 'success',
                'message' => 'No order has been created yet!',
            ], 200);
        }
        
    }

    public function store(StoreOrderRequest $request)
    { 
        $data = $request->validated();
        $order = $this->orderService->create($data);
        return new JsonResponse([
            'data' => new OrderResource($order),
            'status' => 'success',
            'message' => 'Order created successfully',
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $order = $this->orderService->find($id);
        return new JsonResponse([
            'data' => new OrderResource($order),
            'status' => 'success',
            'message' => 'Order details retrieved successfully',
        ], 200);
    }
}
