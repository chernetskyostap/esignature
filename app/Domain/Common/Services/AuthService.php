<?php

namespace App\Domain\Common\Services;

use App\Domain\Common\DTO\RegisterDto;
use App\Domain\User\Models\User;

class AuthService
{
    public function register(RegisterDto $registerDto): User
    {
        return User::create([
            'name' => $registerDto->name,
            'email' => $registerDto->email,
            'password' => $registerDto->password,
        ]);
    }
}
