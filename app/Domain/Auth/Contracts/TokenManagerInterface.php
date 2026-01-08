<?php

namespace App\Domain\Auth\Contracts;

use App\Domain\Auth\Entities\User;

interface TokenManagerInterface
{
    public function createToken(User $user): string;
}
