<?php

namespace Tests\Unit\Payments;

use App\Application\Payments\Services\HandlePaymentWebhookService;
use App\Domain\Payments\Enums\PaymentStatus;
use App\Models\Payment as PaymentModel;
use App\Models\PaymentEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HandlePaymentWebhookServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_marks_payment_as_paid_and_persists_on_webhook(): void
    {
        $payment = PaymentModel::factory()->create([
            'provider' => 'mock_a',
            'provider_payment_id' => 'prov_123',
            'status' => PaymentStatus::PROCESSING->value,
        ]);

        /** @var HandlePaymentWebhookService $service */
        $service = app(HandlePaymentWebhookService::class);

        $service->execute(
            provider: 'mock_a',
            providerPaymentId: 'prov_123',
            status: 'paid',
            eventId: 'evt_123',
        );

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => PaymentStatus::PAID->value,
        ]);

        $this->assertDatabaseHas('payment_events', [
            'payment_id' => $payment->id,
            'provider' => 'mock_a',
            'provider_event_id' => 'evt_123',
        ]);
    }

    public function test_ignores_already_processed_webhook_event(): void
    {
        $payment = PaymentModel::factory()->create([
            'provider' => 'mock_a',
            'provider_payment_id' => 'prov_123',
            'status' => PaymentStatus::PROCESSING->value,
        ]);

        PaymentEvent::create([
            'payment_id' => $payment->id,
            'provider' => 'mock_a',
            'provider_event_id' => 'evt_123',
            'payload' => [],
        ]);

        /** @var HandlePaymentWebhookService $service */
        $service = app(HandlePaymentWebhookService::class);

        $service->execute(
            provider: 'mock_a',
            providerPaymentId: 'prov_123',
            status: 'paid',
            eventId: 'evt_123',
        );

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => PaymentStatus::PROCESSING->value,
        ]);

        $this->assertDatabaseCount('payment_events', 1);
    }
}
