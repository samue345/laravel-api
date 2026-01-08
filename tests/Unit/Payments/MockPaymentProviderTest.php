<?php

namespace Tests\Unit\Payments;


use App\Infrastructure\Payments\Providers\ProviderAClient;
use App\Infrastructure\Payments\Providers\ProviderBClient;
use PHPUnit\Framework\TestCase;

class MockPaymentProviderTest extends TestCase
{
    public function test_create_charge_returns_valid_response_a(): void
    {
        $provider = new ProviderAClient();

        $response = $provider->createCharge([
            'amount'   => 1500,
            'currency' => 'BRL',
        ]);

        $this->assertNotEmpty($response->providerPaymentId);
        $this->assertStringStartsWith('provA_', $response->providerPaymentId);
        $this->assertNull($response->redirectUrl);
    }

      public function test_create_charge_returns_valid_response_b(): void
    {
        $provider = new ProviderBClient();

        $response = $provider->createCharge([
            'amount'   => 1500,
            'currency' => 'BRL',
        ]);

        $this->assertNotEmpty($response->providerPaymentId);
        $this->assertStringStartsWith('provB_', $response->providerPaymentId);

        $this->assertNotNull($response->redirectUrl);

        $this->assertTrue(filter_var($response->redirectUrl, FILTER_VALIDATE_URL) !== false);
    }
}
