<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $conditions = ['良好', '目立った傷や汚れなし', 'やや傷や汚れあり', '状態が悪い'];

        return [
            'item_name' => $this->faker->word(),
            'item_detail' => $this->faker->sentence(),
            'price' => $this->faker->numberBetween(1000, 100000),
            'item_img' => 'default.jpg',
            'category_id' => Category::first()->id ?? Category::factory(),
            'condition' => $this->faker->randomElement($conditions),
        ];
    }
}
