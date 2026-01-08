<?php

namespace App\Domain\Payments\Entities;

use App\Domain\Payments\Enums\PaymentStatus;

class Payment
{
    public function __construct(
        public ?int $id,
        public int $userId,
        public int $amount, 
        public string $currency,
        public string $provider,
        public ?string $providerPaymentId,
        public PaymentStatus $status,
        public ?string $idempotencyKey,
    ) {
    }

    public static function create(
        int $userId,
        int $amount,
        string $currency,
        string $provider,
        ?string $idempotencyKey = null
    ): self {
        return new self(
            id: null,
            userId: $userId,
            amount: $amount,
            currency: $currency,
            provider: $provider,
            providerPaymentId: null,
            status: PaymentStatus::PENDING,
            idempotencyKey: $idempotencyKey,
        );
    }

    public function markProcessing(string $providerPaymentId): void
    {
        $this->providerPaymentId = $providerPaymentId;
        $this->status = PaymentStatus::PROCESSING;
    }

    public function markPaid(): void
    {
        $this->status = PaymentStatus::PAID;
    }

    public function markFailed(): void
    {
        $this->status = PaymentStatus::FAILED;
    }
}
