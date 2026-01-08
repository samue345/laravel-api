<?php

namespace Tests\Unit\Payments;

use App\Application\Payments\DTOs\CreatePaymentDTO;
use App\Application\Payments\Routing\DefaultPaymentRoutingStrategy;
use PHPUnit\Framework\TestCase;

class DefaultPaymentRoutingStrategyTest extends TestCase
{
    public function test_uses_explicit_provider_if_present(): void
    {
        $dto = new CreatePaymentDTO(
            userId: 1,
            amount: 1000,
            currency: 'BRL',
            provider: 'custom_provider',
            idempotencyKey: null,
        );

        $strategy = new DefaultPaymentRoutingStrategy();

        $this->assertSame('custom_provider', $strategy->chooseProvider($dto));
    }

    public function test_amount_less_than_5000_uses_provider_a(): void
    {
        $dto = new CreatePaymentDTO(
            userId: 1,
            amount: 4999,
            currency: 'BRL',
            provider: null,
            idempotencyKey: null,
        );

        $strategy = new DefaultPaymentRoutingStrategy();

        $this->assertSame('provider_a', $strategy->chooseProvider($dto));
    }

    public function test_amount_equal_to_5000_uses_provider_b(): void
    {
        $dto = new CreatePaymentDTO(
            userId: 1,
            amount: 5000,
            currency: 'BRL',
            provider: null,
            idempotencyKey: null,
        );

        $strategy = new DefaultPaymentRoutingStrategy();

        $this->assertSame('provider_b', $strategy->chooseProvider($dto));
    }

    public function test_amount_greater_than_5000_uses_provider_b(): void
    {
        $dto = new CreatePaymentDTO(
            userId: 1,
            amount: 10000,
            currency: 'BRL',
            provider: null,
            idempotencyKey: null,
        );

        $strategy = new DefaultPaymentRoutingStrategy();

        $this->assertSame('provider_b', $strategy->chooseProvider($dto));
    }

    public function test_zero_amount_falls_back_to_provider_a(): void
    {
        $dto = new CreatePaymentDTO(
            userId: 1,
            amount: 0,
            currency: 'BRL',
            provider: null,
            idempotencyKey: null,
        );

        $strategy = new DefaultPaymentRoutingStrategy();

        $this->assertSame('provider_a', $strategy->chooseProvider($dto));
    }

    public function test_negative_amount_still_uses_provider_a_by_current_rule(): void
    {
        $dto = new CreatePaymentDTO(
            userId: 1,
            amount: -100,
            currency: 'BRL',
            provider: null,
            idempotencyKey: null,
        );

        $strategy = new DefaultPaymentRoutingStrategy();

        $this->assertSame('provider_a', $strategy->chooseProvider($dto));
    }

    public function test_empty_string_provider_is_ignored_and_falls_back_to_amount_rule(): void
    {
        $dto = new CreatePaymentDTO(
            userId: 1,
            amount: 3000,
            currency: 'BRL',
            provider: '', 
            idempotencyKey: null,
        );

        $strategy = new DefaultPaymentRoutingStrategy();

        $this->assertSame('provider_a', $strategy->chooseProvider($dto));
    }

    public function test_string_zero_provider_is_also_ignored_because_its_falsy(): void
    {
        $dto = new CreatePaymentDTO(
            userId: 1,
            amount: 7000,
            currency: 'BRL',
            provider: '0', 
            idempotencyKey: null,
        );

        $strategy = new DefaultPaymentRoutingStrategy();

        $this->assertSame('provider_b', $strategy->chooseProvider($dto));
    }

    public function test_currency_does_not_affect_routing_with_current_implementation(): void
    {
        $dtoBrl = new CreatePaymentDTO(
            userId: 1,
            amount: 4000,
            currency: 'BRL',
            provider: null,
            idempotencyKey: null,
        );

        $dtoUsd = new CreatePaymentDTO(
            userId: 1,
            amount: 4000,
            currency: 'USD',
            provider: null,
            idempotencyKey: null,
        );

        $strategy = new DefaultPaymentRoutingStrategy();

        $this->assertSame('provider_a', $strategy->chooseProvider($dtoBrl));
        $this->assertSame('provider_a', $strategy->chooseProvider($dtoUsd));
    }
}
