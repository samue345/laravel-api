<?php

namespace Tests\Integration\Payments;

use App\Domain\Payments\Entities\Payment as DomainPayment;
use App\Infrastructure\Payments\Persistence\PaymentRepository;
use App\Models\Payment as PaymentModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Domain\Payments\Enums\PaymentStatus;
use App\Models\User;
use Tests\TestCase;

class PaymentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_saves_and_reads_payment(): void
    {
        $user = User::factory()->create();

        $repo = new PaymentRepository();

        $payment = new DomainPayment(
            id: null,
            userId: $user->id,             
            amount: 1500,
            currency: 'BRL',
            provider: 'mock_a',
            providerPaymentId: null,
            status: PaymentStatus::PAID,
            idempotencyKey: 'idem-123',
        );

        $saved = $repo->save($payment);

        $this->assertNotNull($saved->id);

        $found = $repo->findById($saved->id);

        $this->assertNotNull($found);
        $this->assertSame($user->id, $found->userId);
        $this->assertSame(1500, $found->amount);
        $this->assertSame('BRL', $found->currency);
        $this->assertSame('mock_a', $found->provider);
        $this->assertSame('idem-123', $found->idempotencyKey);
        $this->assertSame(PaymentStatus::PAID->value, $found->status->value);
    }

    public function test_find_by_idempotency_key_returns_correct_payment(): void
    {
        $user = User::factory()->create();

        PaymentModel::factory()->create([
            'user_id' => $user->id,  
            'amount' => 1500,
            'currency' => 'BRL',
            'provider' => 'mock_a',
            'provider_payment_id' => 'prov_123',
            'status' => PaymentStatus::PROCESSING->value,
            'idempotency_key' => 'idem-123',
        ]);

        $repo = new PaymentRepository();

        $payment = $repo->findByIdempotencyKey('idem-123', $user->id);

        $this->assertNotNull($payment);
        $this->assertSame($user->id, $payment->userId);
        $this->assertSame('prov_123', $payment->providerPaymentId);
        $this->assertSame(PaymentStatus::PROCESSING->value, $payment->status->value);
    }
}