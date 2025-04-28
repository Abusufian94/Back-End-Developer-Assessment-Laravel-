<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        // Create Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('Pass123!'),
        ]);
        $admin->assignRole('admin');

        // Create Customer User
        $customer = User::create([
            'name' => 'Customer User',
            'email' => 'customer@example.com',
            'password' => bcrypt('Pass123!'),
        ]);
        $customer->assignRole('customer');

        // Create Categories
        $electronics = Category::create(['name' => 'Electronics', 'slug' => 'electronics']);
        $clothing = Category::create(['name' => 'Clothing', 'slug' => 'clothing']);

        // Create Products
        $product1 = Product::create([
            'name' => 'Smartphone',
            'slug' => 'smartphone',
            'description' => 'Latest model smartphone',
            'price' => 599.99,
            'stock_quantity' => 100,
            'images' => json_encode(['smartphone1.jpg', 'smartphone2.jpg']),
        ]);
        $product1->categories()->attach($electronics);

        $product2 = Product::create([
            'name' => 'T-Shirt',
            'slug' => 't-shirt',
            'description' => 'Cotton t-shirt',
            'price' => 19.99,
            'stock_quantity' => 200,
            'images' => json_encode(['tshirt1.jpg']),
        ]);
        $product2->categories()->attach($clothing);
    }
}
