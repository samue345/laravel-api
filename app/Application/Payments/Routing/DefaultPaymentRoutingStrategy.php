<?php

namespace App\Application\Payments\Routing;

use App\Application\Payments\DTOs\CreatePaymentDTO;

class DefaultPaymentRoutingStrategy implements PaymentRoutingStrategyInterface
{
    public function chooseProvider(CreatePaymentDTO $dto): string
    {
        if ($dto->provider) {
            return $dto->provider;
        }

        return $dto->amount < 5000 ? 'provider_a' : 'provider_b';
    }
}
