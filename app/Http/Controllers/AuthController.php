<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
        $this->middleware('jwt.auth', ['except' => ['login', 'register', 'checkStatus']]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).+$/'
            ],
            'password_confirmation' => 'required|string',

        ]);

        try {

            $data['role'] = 'student';

            $result = $this->authService->register($data);

            return $this->successResponse([
                'token' => $result['token'],
                'user' => [
                    'id' => $result['user']->id,
                    'name' => $result['user']->name,
                    'email' => $result['user']->email,
                    'role' => $result['user']->role,
                ]
            ], 'Student registration successful.', 201);

        } catch (\Exception $e) {
            return $this->errorResponse('Registration failed: ' . $e->getMessage(), 422);
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        try {
            $result = $this->authService->login($credentials);

            return $this->successResponse([
                'token' => $result['token'],
                'user' => [
                    'id' => $result['user']->id,
                    'name' => $result['user']->name,
                    'email' => $result['user']->email,
                    'role' => $result['user']->role,
                ]
            ], 'Login successful.', 200);

        } catch (\Exception $e) {
            return $this->errorResponse('Unauthorized: ' . $e->getMessage(), 401);
        }
    }
    public function me()
    {
        try {
            $user = $this->authService->me();
            return $this->successResponse([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ], 'User profile retrieved.', 200);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve user profile', 401);
        }
    }
    public function logout()
    {
        try {
            $this->authService->logout();
            return $this->successResponse(null, 'Logged out successfully.', 200);
        } catch (TokenExpiredException $e) {
            return $this->errorResponse('Token has already expired', 401, [
                'error_code' => 'TOKEN_EXPIRED'
            ]);
        } catch (JWTException $e) {
            return $this->errorResponse('Token is invalid: ' . $e->getMessage(), 401, [
                'error_code' => 'TOKEN_INVALID'
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Logout failed: ' . $e->getMessage(), 400, [
                'error_code' => 'LOGOUT_ERROR'
            ]);
        }
    }
    public function refresh()
    {
        try {
            $newToken = $this->authService->refresh();
            $user = JWTAuth::setToken($newToken)->toUser();

            return $this->successResponse([
                'token' => $newToken,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ]
            ], 'Token refreshed successfully.', 200);
        } catch (TokenExpiredException $e) {
            return $this->errorResponse('Token has expired and cannot be refreshed', 401, [
                'error_code' => 'TOKEN_EXPIRED_CANNOT_REFRESH'
            ]);
        } catch (JWTException $e) {
            return $this->errorResponse('Token refresh failed', 401, [
                'error_code' => 'TOKEN_REFRESH_FAILED',
                'message' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Token refresh failed', 500, [
                'error_code' => 'REFRESH_ERROR',
                'message' => $e->getMessage()
            ]);
        }
    }
    public function checkStatus(Request $request)
    {
        try {
            $token = $request->bearerToken();
            if (!$token) {
                return $this->errorResponse('Token not provided', 401, [
                    'authenticated' => false,
                    'token_valid' => false,
                    'error_code' => 'TOKEN_MISSING'
                ]);
            }

            $user = JWTAuth::setToken($token)->toUser();
            if (!$user) {
                return $this->errorResponse('User not found', 404, [
                    'authenticated' => false,
                    'token_valid' => false,
                    'error_code' => 'USER_NOT_FOUND'
                ]);
            }

            return $this->successResponse([
                'authenticated' => true,
                'token_valid' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ]
            ], 'Authentication status retrieved successfully.', 200);

        } catch (TokenExpiredException $e) {
            return $this->errorResponse('Token has expired', 401, [
                'authenticated' => false,
                'token_valid' => false,
                'error_code' => 'TOKEN_EXPIRED'
            ]);
        } catch (JWTException $e) {
            return $this->errorResponse('Token is invalid', 401, [
                'authenticated' => false,
                'token_valid' => false,
                'error_code' => 'TOKEN_INVALID'
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Authentication check failed', 500, [
                'authenticated' => false,
                'token_valid' => false,
                'error_code' => 'CHECK_FAILED',
                'message' => $e->getMessage()
            ]);
        }
    }
    public function checkPermission(Request $request)
    {
        $data = $request->validate([
            'required_roles' => 'required|array',
            'required_roles.*' => 'string|in:admin,teacher,student'
        ]);

        try {
            $user = $this->authService->me();
            $hasPermission = in_array($user->role, $data['required_roles']);

            return $this->successResponse([
                'has_permission' => $hasPermission,
                'user_role' => $user->role,
                'required_roles' => $data['required_roles']
            ], $hasPermission ? 'Permission granted.' : 'Permission denied.', 200);

        } catch (\Exception $e) {
            return $this->errorResponse('Permission check failed', 401);
        }
    }
}
