<?php

namespace App\Domain\Payments\Contracts;
use App\Domain\Payments\Entities\Payment;


interface PaymentRepositoryInterface
{
    public function findById(int $id): ?Payment;
    public function findByIdempotencyKey(string $key, int $userId): ?Payment;
    public function save(Payment $payment): Payment;
    public function findByProviderPaymentId(string $provider, string $providerPaymentId): ?Payment;
}