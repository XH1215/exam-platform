<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use App\Traits\HasProfile;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    use HasProfile;

    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        $this->middleware(middleware: 'jwt.auth');
        $this->middleware('role:admin', ['except' => ['registerUser']]);
    }

    public function registerUser(Request $request)
    {
        try {
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
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
                'status' => 400,
            ], 400);
        }

        try {
            $user = $this->userService->register($data);

            return $this->successResponse([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ], 'User registered successfully.', 201);

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to register user.', 422, [
                'error_code' => 'REGISTER_USER_FAILED',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function listUsers()
    {
        try {
            $users = $this->userService->allUsers();

            return $this->successResponse($users, 'User list retrieved successfully.', 200);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve users.', 500, [
                'error_code' => 'LIST_USERS_FAILED',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function deleteUser($id)
    {
        try {
            $this->userService->deleteUser($id);
            return $this->successResponse(null, 'User deleted successfully.', 200);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete user.', 500, [
                'error_code' => 'DELETE_USER_FAILED',
                'message' => $e->getMessage()
            ]);
        }
    }
}
