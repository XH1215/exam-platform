<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait HasProfile
{
    public function profile(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user(),
            'status' => 200,
        ], 200);
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

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $updated,
            'status' => 200,
        ], 200);
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

        try {
            $this->userService->changePassword(
                $request->user()->id,
                $data['current_password'],
                $data['new_password']
            );
        } catch (\Exception $e) {
            if ($e->getMessage() === 'Current password is incorrect') {
                return response()->json([
                    'success' => false,
                    'message' => 'Password Incorrect',
                    'status' => 400,
                ], 400);
            }
            throw $e;
        }

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully',
            'status' => 200,
        ], 200);
    }
}
