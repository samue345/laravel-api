<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_receive_token(): void
    {
       $response = $this->postJson(route('register'), [
        'name'     => 'Samuel',
        'email'    => 'samuel@example.com',
        'password' => 'secret123',
    ]);


     $response->assertCreated()
      ->assertJsonStructure([
         'user' => [ 'id','name', 'email'],
         'token',
      ]);

    $this->assertDatabaseHas('users', [  'email' => 'samuel@example.com']);
    }


    public function test_cannot_register_with_existing_email(): void
    {
        User::factory()->create([
            'email' => 'samuel@example.com',
        ]);

        $response = $this->postJson(route('register'), [
            'name'     => 'Outro',
            'email'    => 'samuel@example.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_cannot_register_with_invalid_email(): void
    {
        $response = $this->postJson(route('register'), [
            'name'     => 'Samuel',
            'email'    => 'isso-nao-e-email',
            'password' => 'secret123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_can_login_and_receive_token(): void
    {
        $user = User::factory()->create([
            'email'    => 'samuel@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->postJson(route('login'), [
            'email'    => 'samuel@example.com',
            'password' => 'secret123',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email'],
                'token',
            ]);
    }

    public function test_cannot_login_with_wrong_password(): void
    {
        $user = User::factory()->create([
            'email'    => 'samuel@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->postJson(route('login'), [
            'email'    => 'samuel@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_cannot_login_with_unknown_email(): void
    {
        $response = $this->postJson(route('login'), [
            'email'    => 'naoexiste@example.com',
            'password' => 'whatever',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_me_requires_authentication(): void
    {
        $response = $this->getJson(route('me'));

        $response->assertStatus(401);
    }

    public function test_me_returns_authenticated_user(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('api')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson(route('me'));

        $response->assertOk()
            ->assertJson([
                'id' => $user->id,
                'email' => $user->email,
            ]);
    }
    
}
