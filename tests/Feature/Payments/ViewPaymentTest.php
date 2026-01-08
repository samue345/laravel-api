<?php

namespace Tests\Feature\Payments;

use App\Models\Payment as PaymentModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Domain\Payments\Enums\PaymentStatus;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class ViewPaymentTest extends TestCase
{
    use RefreshDatabase;

   public function test_can_view_existing_payment(): void
   {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $payment = PaymentModel::factory()->create();

        $response = $this->getJson(route('payments.show', ['id' => $payment->id]));

        $response->assertOk()
            ->assertJson([
                'id' => $payment->id,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'provider' => $payment->provider,
                'provider_payment_id' => $payment->provider_payment_id,
                'status' => PaymentStatus::PROCESSING->value,
                'idempotency_key' => $payment->idempotency_key,
         ]);
    }

    public function test_returns_404_when_payment_not_found(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson(route('payments.show', ['id' => 999]));

        $response->assertNotFound()
            ->assertJson([
                'message' => 'Payment not found',
            ]);
    }
}
