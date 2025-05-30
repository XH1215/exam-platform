<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;

class AuthController extends Controller
{
    protected $authService;

    /**
     * Attach the AuthService. The 'auth:api' middleware protects all routes except login.
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Handle user login. Returns a JWT on success.
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        try {
            $token = $this->authService->login($credentials);
            // (Optional) Encrypt the token using AES-256 for demonstration
            // $token = Crypt::encryptString($token);
        } catch (\Exception $e) {
            return $this->errorResponse('Unauthorized - ' . $e->getMessage(), 401);
        }
        return $this->successResponse(['token' => $token], 'Login successful.');
    }

    /**
     * Get the authenticated user's information.
     */
    public function me()
    {
        $user = $this->authService->me();
        return $this->successResponse($user, 'User profile retrieved.');
    }

    /**
     * Logout the user (invalidate the token).
     */
    public function logout()
    {
        $this->authService->logout();
        return $this->successResponse(null, 'Logged out successfully.');
    }
}
