<?php

namespace App\Http\Controllers\API\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\AdjustStockRequest;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;

    /**
     * ProductController constructor.
     *
     * @param ProductService $productService
     */
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
   
    }

    /**
     * List all products with pagination and optional category filter.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 10);
        $categoryId = $request->query('category_id');
        $products = $this->productService->getPaginated($perPage, $categoryId);
        return new JsonResponse([
            'data' => ProductResource::collection($products)->toArray($request),
            'status' => 'success',
            'message' => 'Operation successful',
            'meta' => [
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ], 200);
    }

    /**
     * Get details of a single product.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $product = $this->productService->find($id);
       
        return new JsonResponse([
            'data' => $product,
            'status' => 'success',
            'message' => 'Operation successful',
        ], 200);
    }

    /**
     * Create a new product.
     *
     * @param StoreProductRequest $request
     * @return JsonResponse
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        // Merge validated data with files
        $data = $request->validated();
        if ($request->hasFile('images')) {
            $data['images'] = $request->file('images');
        }

        $product = $this->productService->create($data);
        return new JsonResponse([
            'data' => (new ProductResource($product))->toArray($request),
            'status' => 'success',
            'message' => 'Product created successfully',
        ], 201);
    }

    /**
     * Update an existing product.
     *
     * @param UpdateProductRequest $request
     * @param int $id
     * @return JsonResponse
     */
    
    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();
        if ($request->hasFile('images')) {
            $data['images'] = $request->file('images');
        }

        $product = $this->productService->update($id, $data);
        return new JsonResponse([
            'data' => (new ProductResource($product))->toArray($request),
            'status' => 'success',
            'message' => 'Product updated successfully',
        ], 200);
    }

    /**
     * Delete a product.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id)
    { 
        $this->productService->delete($id);
        return new JsonResponse([
            'status' => 'success',
            'message' => 'Product deleted successfully',
        ], 200);
    }

    public function inventory(Request $request)
    {
        $perPage = $request->query('per_page')??10;
        $categoryId = $request->query('category_id');
        $lowStockThreshold = $request->query('low_stock_threshold');


        $inventory = $this->productService->getInventory( $perPage,  $categoryId = null,  $lowStockThreshold = null);
        return new JsonResponse([
            'data' => $inventory->items(),
            'pagination' => [
                'total' => $inventory->total(),
                'per_page' => $inventory->perPage(),
                'current_page' => $inventory->currentPage(),
                'last_page' => $inventory->lastPage(),
            ],
            'status' => 'success',
            'message' => 'Inventory retrieved successfully',
        ], 200);
    }

    public function adjustStock(AdjustStockRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();
        $product = $this->productService->adjustStock($id, $data['adjustment']);
        return new JsonResponse([
            'data' => new ProductResource($product),
            'status' => 'success',
            'message' => 'Stock adjusted successfully',
        ], 200);
    }
}