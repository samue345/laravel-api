<?php

namespace App\Application\Auth;

use App\Data\Auth\LoginData;
use App\Domain\Auth\Contracts\TokenManagerInterface;
use App\Domain\Auth\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginUser
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
        private readonly TokenManagerInterface $tokens,
    ) {}

    public function execute(LoginData $data): array
    {
      $user = $this->users->findByCredentials($data->email, $data->password);

      if (!$user) {
          throw ValidationException::withMessages([
              'email' => ['Credenciais inválidas.'],
          ]);
      }


        $token = $this->tokens->createToken($user);

        return compact('user', 'token');
    }
}
