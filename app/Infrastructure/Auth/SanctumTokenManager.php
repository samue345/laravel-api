<?php

namespace App\Infrastructure\Auth;

use App\Domain\Auth\Contracts\TokenManagerInterface;
use App\Domain\Auth\Entities\User as DomainUser;
use App\Models\User as EloquentUser;

class SanctumTokenManager implements TokenManagerInterface
{
    public function createToken(DomainUser $user): string
    {
        /** @var EloquentUser $eloquentUser */
        $eloquentUser = EloquentUser::findOrFail($user->id);

        return $eloquentUser->createToken('api')->plainTextToken;
    }
}
