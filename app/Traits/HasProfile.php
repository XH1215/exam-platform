<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait HasProfile
{
    public function profile(Request $request)
    {
        return response()->json($request->user());
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $request->user()->id,
        ]);

        $updated = $this->userService->updateProfile(
            $request->user()->id,
            $data
        );

        return response()->json($updated);
    }

    public function changePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => 'required|string',
            'new_password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\w\d]).+$/'
            ],
        ], [
            'new_password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.'
        ]);

        $this->userService->changePassword(
            $request->user()->id,
            $data['current_password'],
            $data['new_password']
        );

        return response()->json(['message' => 'Password changed'], 200);
    }
}