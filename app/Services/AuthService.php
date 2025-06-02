<?php

namespace App\Services;

use App\Services\UserService;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function login(array $credentials): array
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new \RuntimeException('Invalid credentials');
        }

        $token = JWTAuth::fromUser($user);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function register(array $data): array
    {
        $data['role'] = 'student';

        $user = $this->userService->register($data);

        $token = JWTAuth::fromUser($user);

        return [
            'user' => $user,
            'token' => $token
        ];
    }
    public function me(): User
    {
        $user = JWTAuth::user();
        if (!$user) {
            throw new \Exception('User not authenticated');
        }
        return $user;
    }

    public function logout(): void
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
        } catch (\Exception $e) {
            throw new \Exception('Failed to logout: ' . $e->getMessage());
        }
    }

    public function refresh(): string
    {
        try {
            return JWTAuth::refresh(JWTAuth::getToken());
        } catch (\Exception $e) {
            throw new \Exception('Token refresh failed: ' . $e->getMessage());
        }
    }
}
