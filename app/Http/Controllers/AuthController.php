<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            $token = $this->authService->login($credentials);
        } catch (\Exception $e) {
            return $this->errorResponse('Unauthorized - ' . $e->getMessage(), 401);
        }

        return $this->successResponse(['token' => $token], 'Login successful.');
    }

    public function me()
    {
        $user = $this->authService->me();
        return $this->successResponse($user, 'User profile retrieved.');
    }

    public function logout()
    {
        $this->authService->logout();
        return $this->successResponse(null, 'Logged out successfully.');
    }
}
