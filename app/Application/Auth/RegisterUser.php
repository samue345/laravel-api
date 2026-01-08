<?php

namespace App\Application\Auth;

use App\Data\Auth\RegisterData;
use App\Domain\Auth\Contracts\TokenManagerInterface;
use App\Domain\Auth\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class RegisterUser
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
        private readonly TokenManagerInterface $tokens,
    ) {}

    public function execute(RegisterData $data): array
    {
        if ($this->users->findByEmail($data->email)) {
            throw ValidationException::withMessages([
                'email' => ['Este e-mail já está em uso.'],
            ]);
        }
        
        $user = $this->users->create(
            $data->name,
            $data->email,
            $data->password
        );

        $token = $this->tokens->createToken($user);

        return compact('user', 'token');
    }
}
