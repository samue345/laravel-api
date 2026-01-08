<?php

namespace App\Application\Payments\Services\Contracts;

use App\Application\Payments\DTOs\CreatePaymentDTO;
use App\Domain\Payments\Entities\Payment;

interface CreatePaymentAttemptServiceInterface
{
    public function execute(CreatePaymentDTO $dto): Payment;
}
