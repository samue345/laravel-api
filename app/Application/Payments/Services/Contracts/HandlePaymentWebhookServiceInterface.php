<?php

namespace App\Application\Payments\Services\Contracts;

interface HandlePaymentWebhookServiceInterface
{
    public function execute(
        string $provider,
        string $providerPaymentId,
        string $status,
        string $eventId,
    ): void;
}
