<?php

namespace App\Data\Payments;

use App\Application\Payments\DTOs\CreatePaymentDTO;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class CreatePaymentData extends Data
{
    public function __construct(
        public int $amount,
        public string $currency,
        public ?string $provider,
        #[MapInputName('idempotency_key')]
        public ?string $idempotencyKey,
    ) {}

    public static function rules(): array
    {
        return [
            'amount' => ['required', 'integer', 'min:100'],
            'currency' => ['required', 'string', 'size:3'],
            'provider' => ['nullable', 'string'],
            'idempotency_key' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function toDTO(int $userId): CreatePaymentDTO
    {
        return new CreatePaymentDTO(
            userId: $userId,
            amount: $this->amount,
            currency: strtoupper($this->currency),
            provider: $this->provider,
            idempotencyKey: $this->idempotencyKey,
        );
    }
}
