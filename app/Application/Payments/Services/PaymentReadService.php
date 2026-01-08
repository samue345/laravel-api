<?php

namespace App\Application\Payments\Services;

use App\Application\Payments\Services\Contracts\PaymentReadServiceInterface;
use App\Domain\Payments\Entities\Payment;
use App\Domain\Payments\Contracts\PaymentRepositoryInterface;

class PaymentReadService implements PaymentReadServiceInterface
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository,
    ) {}

    public function findById(int $id): ?Payment
    {
        return $this->paymentRepository->findById($id);
    }
}
