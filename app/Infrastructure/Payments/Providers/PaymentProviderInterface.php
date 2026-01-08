<?php

namespace App\Infrastructure\Payments\Providers;

class PaymentProviderResponse
{
    public function __construct(
        public string $providerPaymentId,
        public ?string $redirectUrl = null,
    ) {
    }
}

interface PaymentProviderInterface
{
    public function createCharge(array $data): PaymentProviderResponse;
}
