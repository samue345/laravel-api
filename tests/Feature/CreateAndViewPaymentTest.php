<?php

namespace Tests\Feature\Payments;

use App\Models\Payment as PaymentModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateAndViewPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_idempotency_key_returns_same_payment(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $payload = [
            'amount' => 3000,
            'currency' => 'BRL',
            'idempotency_key' => 'idem-xyz-123',
        ];

        $first = $this->postJson(route('payments.store'), $payload);
        $first->assertStatus(201);

        $second = $this->postJson(route('payments.store'), $payload);
        $second->assertStatus(201);

        $firstId  = $first->json('id');
        $secondId = $second->json('id');

        $this->assertSame($firstId, $secondId, 'Idempotência deve retornar o mesmo pagamento');

        $this->assertSame(1, PaymentModel::count());
    }

    public function test_authenticated_user_can_view_existing_payment(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $payment = PaymentModel::create([
            'user_id' => $user->id,
            'amount' => 1500,
            'currency' => 'BRL',
            'provider' => 'provider_a',
            'provider_payment_id' => 'prov_123',
            'status' => 'processing',
            'idempotency_key'=> 'idem-123',
        ]);

        $response = $this->getJson("/api/payments/{$payment->id}");

        $response->assertOk()
            ->assertJson([
                'id' => $payment->id,
                'amount' => 1500,
                'currency' => 'BRL',
                'provider' => 'provider_a',
                'provider_payment_id' => 'prov_123',
                'status' => 'processing',
                'idempotency_key'     => 'idem-123',
            ]);
    }

    public function test_view_non_existing_payment_returns_404(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson(route('payments.show', ['id' => 999]));

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Payment not found',
            ]);
    }
}
