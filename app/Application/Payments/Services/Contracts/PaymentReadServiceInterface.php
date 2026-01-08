<?php

namespace App\Application\Payments\Services\Contracts;

use App\Domain\Payments\Entities\Payment;

interface PaymentReadServiceInterface
{
    public function findById(int $id): ?Payment;
}
