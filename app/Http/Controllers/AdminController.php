<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        $this->middleware(['auth:sanctum', 'role:admin']);
    }

    // Admin login moved to AuthController if needed

    /**
     * Register a new teacher
     */
    public function registerTeacher(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);
        $user = $this->userService->register($data, 'teacher');
        return response()->json($user, 201);
    }

    /**
     * Register a new student
     */
    public function registerStudent(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);
        $user = $this->userService->register($data, 'student');
        return response()->json($user, 201);
    }

    /**
     * List all users
     */
    public function listUsers()
    {
        $users = $this->userService->allUsers();
        return response()->json($users);
    }

    /**
     * Delete a user by ID
     */
    public function deleteUser($id)
    {
        $this->userService->deleteUser($id);
        return response()->json(['message' => 'User deleted'], 200);
    }

    /**
     * View admin profile
     */
    public function profile(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Update admin profile
     */
    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $request->user()->id,
        ]);
        $updated = $this->userService->updateProfile($request->user()->id, $data);
        return response()->json($updated);
    }

    /**
     * Change admin password
     */
    public function changePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);
        $this->userService->changePassword(
            $request->user()->id,
            $data['current_password'],
            $data['new_password']
        );
        return response()->json(['message' => 'Password changed'], 200);
    }
}