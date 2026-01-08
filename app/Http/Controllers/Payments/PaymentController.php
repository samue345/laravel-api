<?php

namespace App\Http\Controllers\Payments;

use App\Application\Payments\Services\Contracts\CreatePaymentAttemptServiceInterface;
use App\Application\Payments\Services\Contracts\PaymentReadServiceInterface;
use App\Domain\Payments\Contracts\PaymentRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Data\Payments\CreatePaymentData;

class PaymentController extends Controller
{
    public function __construct(
        private CreatePaymentAttemptServiceInterface $createPaymentAttemptService,
        private PaymentReadServiceInterface $paymentReadService,
    ) {}

    public function store(CreatePaymentData $data)
    {
        $userId = auth()->id(); 

        $dto = $data->toDTO($userId);

        $payment = $this->createPaymentAttemptService->execute($dto);

        return response()->json([
            'id' => $payment->id,
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'provider' => $payment->provider,
            'provider_payment_id' => $payment->providerPaymentId,
            'status' => $payment->status->value,
            'idempotency_key' => $payment->idempotencyKey,
        ], 201);
    }

    public function show(int $id)
    {
        $payment = $this->paymentReadService->findById($id);

        if (! $payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        return response()->json([
            'id' => $payment->id,
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'provider' => $payment->provider,
            'provider_payment_id' => $payment->providerPaymentId,
            'status' => $payment->status->value,
            'idempotency_key' => $payment->idempotencyKey,
        ]);
    }
}
