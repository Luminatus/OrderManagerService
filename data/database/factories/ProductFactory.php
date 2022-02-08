<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->company.' '.$this->faker->toUpper($this->faker->bothify('???-####')),
            'price' => $this->faker->biasedNumberBetween(1, 10000, 'Faker\Provider\Biased::linearLow'),
        ];
    }
}
