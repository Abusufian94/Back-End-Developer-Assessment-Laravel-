<?php

namespace Tests\Feature;

use App\Exceptions\TestHandler;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Clear permission cache
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        // Seed roles and categories
        $this->seed(RoleSeeder::class);
        Category::factory()->create(['name' => 'Electronics', 'slug' => 'electronics']);
        // Register custom test exception handler
        $this->app->singleton(Handler::class, TestHandler::class);
    }

    public function test_list_products_with_pagination()
    {
        Product::factory()->count(15)->create();
        $response = $this->getJson('/api/v1/products?per_page=10');
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [['id', 'name', 'slug', 'price', 'images', 'categories', 'created_at']],
                     'status',
                     'message',
                     'meta' => ['current_page', 'per_page', 'total'],
                 ])
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Operation successful',
                 ]);
    }

    public function test_filter_products_by_category()
    {
        $category = Category::where('slug', 'electronics')->first();
        $product = Product::factory()->create();
        $product->categories()->attach($category);
        $response = $this->getJson("/api/v1/products?category_id={$category->id}");
        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data')
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Operation successful',
                 ]);
    }

    public function test_create_product_with_images_as_admin()
    {
        $user = User::factory()->create()->assignRole('admin');
        $token = $user->createToken('API Token')->plainTextToken;
        $category = Category::where('slug', 'electronics')->first();
        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
                         ->postJson('/api/v1/products', [
                             'name' => 'Laptop',
                             'description' => 'High-end laptop',
                             'price' => 999.99,
                             'stock_quantity' => 10,
                             'images' => [
                                 UploadedFile::fake()->image('laptop.jpg'),
                                 UploadedFile::fake()->image('laptop2.png'),
                             ],
                             'category_ids' => [$category->id],
                         ]);
        $response->assertStatus(201)
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Product created successfully',
                     'data' => [
                         'name' => 'Laptop',
                         'slug' => 'laptop',
                         'images' => [
                             'products/laptop.jpg',
                             'products/laptop2.png',
                         ],
                     ],
                 ])
                 ->assertJsonStructure([
                     'data' => ['id', 'name', 'slug', 'price', 'images', 'categories', 'created_at'],
                     'status',
                     'message',
                 ]);
        Storage::disk('public')->assertExists('products/laptop.jpg');
    }

    public function test_create_product_with_duplicate_name_fails()
    {
        $user = User::factory()->create()->assignRole('admin');
        $token = $user->createToken('API Token')->plainTextToken;
        Product::factory()->create(['name' => 'Laptop']);
        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
                         ->postJson('/api/v1/products', [
                             'name' => 'Laptop',
                             'description' => 'High-end laptop',
                             'price' => 999.99,
                             'stock_quantity' => 10,
                         ]);
        $response->assertStatus(422)
                 ->assertJson([
                     'status' => 'error',
                     'error' => 'validation_failed',
                     'code' => 422,
                     'details' => [
                         'name' => ['A product with this name already exists.'],
                     ],
                 ])
                 ->assertJsonStructure([
                     'status',
                     'error',
                     'message',
                     'code',
                     'details',
                 ]);
    }

    public function test_unauthorized_create_product()
    {
        $response = $this->postJson('/api/v1/products', [
            'name' => 'Laptop',
            'price' => 999.99,
            'stock_quantity' => 10,
        ]);
        $response->assertStatus(401)
                 ->assertJson([
                     'status' => 'error',
                     'error' => 'unauthenticated',
                     'code' => 401,
                 ])
                 ->assertJsonStructure([
                     'status',
                     'error',
                     'message',
                     'code',
                     'details',
                 ]);
    }

    public function test_update_product_as_admin()
    {
        $user = User::factory()->create()->assignRole('admin');
        $token = $user->createToken('API Token')->plainTextToken;
        $product = Product::factory()->create();
        $category = Category::where('slug', 'electronics')->first();
        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
                         ->putJson("/api/v1/products/{$product->id}", [
                             'name' => 'Updated Laptop',
                             'price' => 1099.99,
                             'category_ids' => [$category->id],
                         ]);
        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Product updated successfully',
                     'data' => [
                         'name' => 'Updated Laptop',
                         'slug' => 'updated-laptop',
                         'price' => 1099.99,
                     ],
                 ])
                 ->assertJsonStructure([
                     'data' => ['id', 'name', 'slug', 'price', 'images', 'categories', 'created_at'],
                     'status',
                     'message',
                 ]);
    }

    public function test_update_product_with_duplicate_name_fails()
    {
        $user = User::factory()->create()->assignRole('admin');
        $token = $user->createToken('API Token')->plainTextToken;
        Product::factory()->create(['name' => 'Existing Laptop']);
        $product = Product::factory()->create(['name' => 'Original Laptop']);
        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
                         ->putJson("/api/v1/products/{$product->id}", [
                             'name' => 'Existing Laptop',
                             'price' => 1099.99,
                         ]);
        $response->assertStatus(422)
                 ->assertJson([
                     'status' => 'error',
                     'error' => 'validation_failed',
                     'code' => 422,
                     'details' => [
                         'name' => ['A product with this name already exists.'],
                     ],
                 ])
                 ->assertJsonStructure([
                     'status',
                     'error',
                     'message',
                     'code',
                     'details',
                 ]);
    }

    public function test_delete_product_as_admin()
    {
        $user = User::factory()->create()->assignRole('admin');
        $token = $user->createToken('API Token')->plainTextToken;
        $product = Product::factory()->create();
        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
                         ->deleteJson("/api/v1/products/{$product->id}");
        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Product deleted successfully',
                 ])
                 ->assertJsonStructure([
                     'status',
                     'message',
                 ]);
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_access_non_existent_product()
    {
        $user = User::factory()->create()->assignRole('admin');
        $token = $user->createToken('API Token')->plainTextToken;
        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
                         ->getJson('/api/v1/products/999');
        $response->assertStatus(404)
                 ->assertJson([
                     'status' => 'error',
                     'error' => 'product_not_found',
                     'code' => 404,
                 ])
                 ->assertJsonStructure([
                     'status',
                     'error',
                     'message',
                     'code',
                     'details',
                 ]);
    }
}