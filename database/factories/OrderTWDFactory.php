<?php

namespace Database\Factories;

use App\Enums\Currency;
use App\Models\OrderTWD;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderTWD>
 */
class OrderTWDFactory extends Factory
{
    protected $model = OrderTWD::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'id'       => 'A0000001' . $this->faker->randomNumber(7, true),
            'name'     => 'test order',
            'address'  => [
                "city"     => 'taichung',
                'district' => 'a-district',
                "street"   => 'street'
            ],
            'currency' => array_rand(Currency::values()),
            'price'    => $this->faker->randomNumber(3)
        ];
    }
}
