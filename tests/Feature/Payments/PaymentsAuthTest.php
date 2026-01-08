<?php

namespace Tests\Feature\Payments;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PaymentsAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_create_payment(): void
    {
        $response = $this->postJson(route('payments.store'), [
            'amount'   => 1000,
            'currency' => 'BRL',
        ]);

        $response->assertStatus(401); 
    }

    public function test_guest_cannot_view_payment(): void
    {
        $response = $this->getJson(route('payments.show', ['id' => 1]));

        $response->assertStatus(401); 
    }

    public function test_authenticated_user_can_access_payments_routes(): void
    {
       $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->withHeader('Accept', 'application/json')
            ->postJson(route('payments.store'), [
                'amount'   => 1500,
                'currency' => 'BRL',
            ]);

        $response->assertCreated()
            ->assertJsonStructure([
                'id',
                'amount',
                'currency',
                'provider',
                'provider_payment_id',
                'status',
                'idempotency_key',
            ]);

      
        $this->assertDatabaseHas('payments', [
            'user_id' => $user->id,
            'amount'  => 1500,
            'currency'=> 'BRL', 
        ]);
    }
}
