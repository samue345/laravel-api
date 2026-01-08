<?php

namespace App\Infrastructure\Payments\Providers;

use InvalidArgumentException;

class PaymentProviderFactory
{
    public function __construct(
        private ProviderAClient $providerA,
        private ProviderBClient $providerB,
    ) {
    }

    public function make(string $provider): PaymentProviderInterface
    {
        return match ($provider) {
            'provider_a' => $this->providerA,
            'provider_b' => $this->providerB,
            default      => throw new InvalidArgumentException("Provider [$provider] não suportado."),
        };
    }
}
