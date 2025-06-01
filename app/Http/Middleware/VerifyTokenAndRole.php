<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class VerifyTokenAndRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is invalid',
                    'error_code' => 'INVALID_TOKEN'
                ], 401);
            }
        } catch (TokenExpiredException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token has expired',
                'error_code' => 'TOKEN_EXPIRED'
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token not provided',
                'error_code' => 'TOKEN_MISSING'
            ], 401);
        }

        // 如果指定了角色要求，验证用户角色
        if (!empty($roles) && !in_array($user->role, $roles)) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient permissions',
                'error_code' => 'INSUFFICIENT_PERMISSIONS',
                'required_roles' => $roles,
                'user_role' => $user->role
            ], 403);
        }

        // 将用户信息添加到请求中
        $request->merge(['authenticated_user' => $user]);
        
        return $next($request);
    }
}