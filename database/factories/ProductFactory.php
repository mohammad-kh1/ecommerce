<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "category_id" => Category::inRandomOrder()->first()->id,
            "brand_id" => Brand::inRandomOrder()->first()->id,
            "name" => collect(["apple laptop2021" , "asus 2020" , "dell 2021" ])->random(),
            "slug" => fake()->slug,
            'images' => json_encode([
                'url1' => $this->faker->imageUrl(),
                'url2' => $this->faker->imageUrl(),
                'url3' => $this->faker->imageUrl(),
            ]),
            "description" => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 0, 9999.99),
            'is_active' => $this->faker->boolean,
            'is_featured' => $this->faker->boolean,
            'in_stock' => $this->faker->boolean,
            'on_sale' => $this->faker->boolean,
            ];
    }
}
