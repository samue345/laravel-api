<?php

namespace Tests\Unit\Payments;

use App\Application\Payments\DTOs\CreatePaymentDTO;
use App\Application\Payments\Routing\PaymentRoutingStrategyInterface;
use App\Application\Payments\Services\CreatePaymentAttemptService;
use App\Domain\Payments\Entities\Payment as DomainPayment;
use App\Domain\Payments\Contracts\PaymentRepositoryInterface;
use App\Infrastructure\Payments\Providers\PaymentProviderFactory;
use App\Infrastructure\Payments\Providers\PaymentProviderInterface;
use App\Infrastructure\Payments\Providers\PaymentProviderResponse;
use PHPUnit\Framework\TestCase;
use App\Domain\Payments\Enums\PaymentStatus;

class CreatePaymentAttemptServiceTest extends TestCase
{
    public function test_creates_new_payment_when_no_idempotent_match(): void
    {
        $paymentRepository = $this->createMock(PaymentRepositoryInterface::class);
        $routingStrategy = $this->createMock(PaymentRoutingStrategyInterface::class);
        $providerFactory = $this->createMock(PaymentProviderFactory::class);
        $providerClient = $this->createMock(PaymentProviderInterface::class);

        $dto = new CreatePaymentDTO(
            userId: 1,
            amount: 1500,
            currency: 'BRL',
            provider: 'mock_a',
            idempotencyKey: 'idem-123',
        );

        $paymentRepository->expects($this->once())
            ->method('findByIdempotencyKey')
            ->with('idem-123', 1)
            ->willReturn(null);

        $routingStrategy->expects($this->once())
            ->method('chooseProvider')
            ->with($dto)
            ->willReturn('mock_a');

        $providerFactory->expects($this->once())
            ->method('make')
            ->with('mock_a')
            ->willReturn($providerClient);

        $providerClient->expects($this->once())
            ->method('createCharge')
            ->with([
                'amount'   => 1500,
                'currency' => 'BRL',
            ])
            ->willReturn(new PaymentProviderResponse(
                providerPaymentId: 'prov_123',
                redirectUrl: null,
            ));

        $paymentRepository->expects($this->once())
            ->method('save')
            ->with($this->callback(function (DomainPayment $payment) {
                return $payment->amount === 1500
                    && $payment->currency === 'BRL'
                    && $payment->provider === 'mock_a'
                    && $payment->providerPaymentId === 'prov_123'
                    && $payment->status->value === 'processing';
            }))
            ->willReturnCallback(fn (DomainPayment $p) => $p);

        $service = new CreatePaymentAttemptService(
            paymentRepository: $paymentRepository,
            routingStrategy: $routingStrategy,
            providerFactory: $providerFactory,
        );

        $payment = $service->execute($dto);

        $this->assertSame(1500, $payment->amount);
        $this->assertSame('BRL', $payment->currency);
        $this->assertSame('mock_a', $payment->provider);
        $this->assertSame('prov_123', $payment->providerPaymentId);
        $this->assertSame('processing', $payment->status->value);
    }

    public function test_idempotency_returns_existing_payment_without_calling_provider(): void
    {
        $paymentRepository   = $this->createMock(PaymentRepositoryInterface::class);
        $routingStrategy     = $this->createMock(PaymentRoutingStrategyInterface::class);
        $providerFactory     = $this->createMock(PaymentProviderFactory::class);

        $existingPayment = new DomainPayment(
            id: 10,
            userId: 1,
            amount: 1500,
            currency: 'BRL',
            provider: 'mock_a',
            providerPaymentId: 'prov_123',
            status: PaymentStatus::PROCESSING,
            idempotencyKey: 'idem-123',
        );

        $dto = new CreatePaymentDTO(
            userId: 1,
            amount: 1500,
            currency: 'BRL',
            provider: 'mock_a',
            idempotencyKey: 'idem-123',
        );

        $paymentRepository->expects($this->once())
            ->method('findByIdempotencyKey')
            ->with('idem-123', 1)
            ->willReturn($existingPayment);

        $routingStrategy->expects($this->never())
            ->method('chooseProvider');

        $providerFactory->expects($this->never())
            ->method('make');

        $paymentRepository->expects($this->never())
            ->method('save');

        $service = new CreatePaymentAttemptService(
            paymentRepository: $paymentRepository,
            routingStrategy: $routingStrategy,
            providerFactory: $providerFactory,
        );

        $payment = $service->execute($dto);

        $this->assertSame($existingPayment, $payment);
    }
}
