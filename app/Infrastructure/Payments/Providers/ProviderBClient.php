<?php

namespace App\Infrastructure\Payments\Providers;

use Illuminate\Support\Str;

class ProviderBClient implements PaymentProviderInterface
{
    public function createCharge(array $data): PaymentProviderResponse
    {
        $providerPaymentId = 'provB_' . Str::uuid()->toString();

        return new PaymentProviderResponse(
            providerPaymentId: $providerPaymentId,
            redirectUrl: 'https://samuel.com/mock/redirect/' . $providerPaymentId,
        );
    }
}
