<?php

namespace App\Application\Payments\Routing;

use App\Application\Payments\DTOs\CreatePaymentDTO;

interface PaymentRoutingStrategyInterface
{
    public function chooseProvider(CreatePaymentDTO $dto): string;
}
