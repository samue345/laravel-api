<?php

namespace App\Infrastructure\Auth;

use App\Domain\Auth\Contracts\UserRepositoryInterface;
use App\Domain\Auth\Entities\User as DomainUser;
use App\Models\User as EloquentUser;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    public function findByEmail(string $email): ?DomainUser
    {
        $user = EloquentUser::where('email', $email)->first();

        if (!$user) {
            return null;
        }

        return new DomainUser(
            id: $user->id,
            name: $user->name,
            email: $user->email,
        );
    }
    
    public function findByCredentials(string $email, string $password): ?DomainUser
    {
        /** @var EloquentUser|null $user */
        $user = EloquentUser::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            return null;
        }

        return new DomainUser(
            id: $user->id,
            name: $user->name,
            email: $user->email,
        );
    }

    public function create(string $name, string $email, string $hashedPassword): DomainUser
    {
        $user = EloquentUser::create(['name' => $name, 'email' => $email, 'password' => $hashedPassword]);

        return new DomainUser(
            id: $user->id,
            name: $user->name,
            email: $user->email,
        );
    }
}