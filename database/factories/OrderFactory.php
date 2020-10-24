<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Shipping;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'type' => $this->faker->randomElement(['pendingPayment', 'pendingReview', 'completed', 'canceled']),
            'status' => $this->faker->numberBetween(0, 5),
            'customer_id' => Customer::factory(),
            'payment_id' => Payment::factory(),
            'shipping_id' => Shipping::factory()
        ];
    }
}
