<?php

namespace App\Application\Payments\DTOs;

class CreatePaymentDTO
{
    public function __construct(
        public int $userId,
        public int $amount,
        public string $currency,
        public ?string $provider,
        public ?string $idempotencyKey,
    ) {
    }
}
