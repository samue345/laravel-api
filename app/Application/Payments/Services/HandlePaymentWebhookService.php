<?php

namespace App\Application\Payments\Services;

use App\Domain\Payments\Contracts\PaymentRepositoryInterface;
use App\Models\PaymentEvent;
use Illuminate\Support\Facades\DB;
use App\Application\Payments\Services\Contracts\HandlePaymentWebhookServiceInterface;

class HandlePaymentWebhookService implements HandlePaymentWebhookServiceInterface
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository,
    ) {
    }

    public function execute(string $provider, string $providerPaymentId, string $status, string $eventId): void
    {
        DB::transaction(function () use ($provider, $providerPaymentId, $status, $eventId) {
            $alreadyProcessed = PaymentEvent::query()
                ->where('provider', $provider)
                ->where('provider_event_id', $eventId)
                ->exists();

            if ($alreadyProcessed) {
                return;
            }

            $payment = $this->paymentRepository->findByProviderPaymentId($provider, $providerPaymentId);

            if (! $payment) {
                return;
            }

            if ($status === 'paid') {
                $payment->markPaid();
            } elseif ($status === 'failed') {
                $payment->markFailed();
            }

            $this->paymentRepository->save($payment);

            PaymentEvent::create([
                'payment_id'        => $payment->id,
                'provider'          => $provider,
                'provider_event_id' => $eventId,
                'payload'           => [], 
            ]);
        });
    }
}
