<?php

namespace App\Http\Controllers\Auth;

use App\Application\Auth\LoginUser;
use App\Application\Auth\RegisterUser;
use App\Data\Auth\LoginData;
use App\Data\Auth\RegisterData;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function __construct(
        private readonly RegisterUser $registerUser,
        private readonly LoginUser $loginUser,
    ) {}

    public function register(RegisterData $data)
    {
        $result = $this->registerUser->execute($data);

        return response()->json($result, 201);
    }

    public function login(LoginData $data)
    {
        $result = $this->loginUser->execute($data);

        return response()->json($result);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }
}
