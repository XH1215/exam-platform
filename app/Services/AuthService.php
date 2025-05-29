<?php

namespace App\Services;

class AuthService
{
    /**
     * Attempt to authenticate with given credentials.
     * Returns a JWT token or throws an exception if invalid.
     */
    public function login($credentials)
    {
        if (! $token = auth()->attempt($credentials)) {
            throw new \Exception('Invalid credentials.');
        }
        return $token;
    }

    /**
     * Invalidate the current user's token (logout).
     */
    public function logout()
    {
        auth()->logout();
    }

    /**
     * Return the currently authenticated user.
     */
    public function me()
    {
        return auth()->user();
    }
}
