<?php

namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'amount' => $this->faker->numberBetween(100, 10000),
            'currency' => 'BRL',
            'provider' => 'mock_a',
            'provider_payment_id' => 'prov_' . $this->faker->unique()->randomNumber(5),
            'status' => 'processing',
            'idempotency_key' => $this->faker->uuid(),
        ];
    }
}
