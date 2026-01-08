<?php

namespace Tests\Feature\Payments;

use App\Application\Payments\Services\Contracts\HandlePaymentWebhookServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_endpoint_calls_service_and_returns_ok(): void
    {
        $this->mock(HandlePaymentWebhookServiceInterface::class, function ($mock) {
            $mock->shouldReceive('execute')
                ->once()
                ->with(
                    'provider_a',
                    'prov_123',
                    'paid',
                    'evt_999',
                );
        });

        $response = $this->postJson('/api/providers/provider_a/webhook', [
            'provider_payment_id' => 'prov_123',
            'status' => 'paid',
            'event_id' => 'evt_999',
        ]);

        $response->assertOk()
            ->assertJson([
                'ok' => true,
            ]);
    }

    public function test_webhook_does_not_require_authentication(): void
    {
        $response = $this->postJson('/api/providers/provider_a/webhook', [
            'provider_payment_id' => 'prov_123',
            'status' => 'paid',
            'event_id' => 'evt_999',
        ]);

        $this->assertNotEquals(401, $response->status());
    }

    public function test_webhook_fails_validation_with_invalid_payload(): void
    {
        $response = $this->postJson('/api/providers/provider_a/webhook', []);

                $response->dump();

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'provider_payment_id',
                'status',
                'event_id',
            ]);
    }
}
