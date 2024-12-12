<?php

namespace Database\Factories;

use App\Enums\Currency;
use App\Models\OrderUSD;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderUSD>
 */
class OrderUSDFactory extends Factory
{
    protected $model = OrderUSD::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'id'       => 'A' . $this->faker->randomNumber(7, true),
            'name'     => 'test order',
            'address'  => [
                "city"     => 'taichung',
                'district' => 'a-district',
                "street"   => 'street'
            ],
            'currency' => $this->faker->randomElement(Currency::values()),
            'price'    => $this->faker->randomNumber(3)
        ];
    }
}
