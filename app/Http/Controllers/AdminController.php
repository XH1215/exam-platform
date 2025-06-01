<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use App\Traits\HasProfile;

class AdminController extends Controller
{
    use HasProfile;

    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        $this->middleware('auth:api', ['except' => ['registerUser']]);
        $this->middleware('role:admin');
    }

    public function registerUser(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[@$!%*?&])[A-Za-z\\d@$!%*?&]+$/'
            ],
            'role' => 'required|in:admin,teacher,student'
        ]);

        $user = $this->userService->register($data, $data['role']);
        return response()->json($user, 201);
    }

    public function listUsers()
    {
        return response()->json($this->userService->allUsers());
    }

    public function deleteUser($id)
    {
        $this->userService->deleteUser($id);
        return response()->json(['message' => 'User deleted'], 200);
    }
}
