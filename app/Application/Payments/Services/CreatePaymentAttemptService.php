<?php

namespace App\Application\Payments\Services;

use App\Application\Payments\DTOs\CreatePaymentDTO;
use App\Application\Payments\Routing\PaymentRoutingStrategyInterface;
use App\Application\Payments\Services\Contracts\CreatePaymentAttemptServiceInterface;
use App\Domain\Payments\Entities\Payment;
use App\Domain\Payments\Contracts\PaymentRepositoryInterface;
use App\Infrastructure\Payments\Providers\PaymentProviderFactory;
use Illuminate\Support\Str;

class CreatePaymentAttemptService implements CreatePaymentAttemptServiceInterface
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository,
        private PaymentRoutingStrategyInterface $routingStrategy,
        private PaymentProviderFactory $providerFactory,
    ) {
    }

    public function execute(CreatePaymentDTO $dto): Payment
    {
        $idempotencyKey = $dto->idempotencyKey ?? Str::uuid()->toString();

        if ($existing = $this->paymentRepository->findByIdempotencyKey($idempotencyKey, $dto->userId)) {
            return $existing;
        }

        $providerName = $this->routingStrategy->chooseProvider($dto);

        $payment = Payment::create(
            userId: $dto->userId,
            amount: $dto->amount,
            currency: $dto->currency,
            provider: $providerName,
            idempotencyKey: $idempotencyKey
        );

        $providerClient = $this->providerFactory->make($providerName);

        $response = $providerClient->createCharge([
            'amount'   => $payment->amount,
            'currency' => $payment->currency,
        ]);

        $payment->markProcessing($response->providerPaymentId);

        return $this->paymentRepository->save($payment);
    }
}
