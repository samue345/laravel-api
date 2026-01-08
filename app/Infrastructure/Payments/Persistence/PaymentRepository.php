<?php

namespace App\Infrastructure\Payments\Persistence;

use App\Domain\Payments\Entities\Payment as PaymentEntity;
use App\Domain\Payments\Enums\PaymentStatus;
use App\Domain\Payments\Contracts\PaymentRepositoryInterface;
use App\Models\Payment as PaymentModel;

class PaymentRepository implements PaymentRepositoryInterface
{
    private function toEntity(PaymentModel $model): PaymentEntity
    {
        return new PaymentEntity(
            id: $model->id,
            userId: $model->user_id,
            amount: $model->amount,
            currency: $model->currency,
            provider: $model->provider,
            providerPaymentId: $model->provider_payment_id,
            status: PaymentStatus::from($model->status),
            idempotencyKey: $model->idempotency_key,
        );
    }

    private function toModel(PaymentEntity $entity): PaymentModel
    {
        $model = $entity->id
            ? PaymentModel::findOrFail($entity->id)
            : new PaymentModel();

        $model->user_id = $entity->userId;
        $model->amount = $entity->amount;
        $model->currency = $entity->currency;
        $model->provider = $entity->provider;
        $model->provider_payment_id = $entity->providerPaymentId;
        $model->status = $entity->status->value;
        $model->idempotency_key = $entity->idempotencyKey;

        return $model;
    }

    public function findById(int $id): ?PaymentEntity
    {
        $model = PaymentModel::find($id);

        return $model ? $this->toEntity($model) : null;
    }

    public function findByIdempotencyKey(string $key, int $userId): ?PaymentEntity
    {
        $model = PaymentModel::where('idempotency_key', $key)
            ->where('user_id', $userId)
            ->first();

        return $model ? $this->toEntity($model) : null;
    }

    public function save(PaymentEntity $payment): PaymentEntity
    {
        $model = $this->toModel($payment);
        $model->save();

        $payment->id = $model->id;

        return $payment;
    }

    public function findByProviderPaymentId(string $provider, string $providerPaymentId): ?PaymentEntity
    {
        $model = PaymentModel::where('provider', $provider)
        ->where('provider_payment_id', $providerPaymentId)
        ->first();

        return $model ? $this->toEntity($model) : null;
    }
}