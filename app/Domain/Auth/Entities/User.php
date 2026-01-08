<?php

namespace App\Domain\Auth\Entities;

class User
{
    public function __construct(
        public readonly int $id,
        public string $name,
        public string $email,
    ) {}
}
