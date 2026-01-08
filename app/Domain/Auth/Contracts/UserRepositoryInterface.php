<?php

namespace App\Domain\Auth\Contracts;

use App\Domain\Auth\Entities\User;

interface UserRepositoryInterface
{
    public function create(string $name, string $email, string $hashedPassword): User;

    public function findByCredentials(string $email, string $password): ?User;
}
