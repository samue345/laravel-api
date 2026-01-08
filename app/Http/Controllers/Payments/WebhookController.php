<?php

namespace App\Http\Controllers\Payments;

use App\Application\Payments\Services\Contracts\HandlePaymentWebhookServiceInterface;
use App\Http\Controllers\Controller;
use App\Data\Payments\PaymentWebhookData;

class WebhookController extends Controller
{
    public function __construct(
        private HandlePaymentWebhookServiceInterface $handlePaymentWebhookService,
    ) {}

    public function handle(string $provider, PaymentWebhookData $data)
    {
        $this->handlePaymentWebhookService->execute(
            provider: $provider,
            providerPaymentId: $data->provider_payment_id,
            status: $data->status,
            eventId: $data->event_id,
        );

        return response()->json(['ok' => true]);
    }
}
