<?php

namespace App\Infrastructure\Payments\Providers;

use Illuminate\Support\Str;

class ProviderAClient implements PaymentProviderInterface
{
    public function createCharge(array $data): PaymentProviderResponse
    {
        $providerPaymentId = 'provA_' . Str::uuid()->toString();

        return new PaymentProviderResponse(
            providerPaymentId: $providerPaymentId,
            redirectUrl: null,
        );
    }
}
