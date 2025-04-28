<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->paragraph,
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'stock_quantity' => $this->faker->numberBetween(0, 100),
            'images' => [
                'products/' . $this->faker->uuid . '.jpg',
            ],
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}