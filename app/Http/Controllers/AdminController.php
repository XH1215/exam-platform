<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AdminService;

class AdminController extends Controller
{
    protected $adminService;

    /**
     * Admin-specific operations. Protected by 'role:admin' middleware.
     */
    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
        $this->middleware('auth:api');
    }

    /**
     * List all users (teachers and students).
     */
    public function listUsers()
    {
        $users = $this->adminService->getAllUsers();
        return $this->successResponse($users, 'User list retrieved.');
    }

    /**
     * Create a new user (teacher or student).
     * Expects 'name', 'email', 'password', and 'role' in $request.
     */
    public function createUser(Request $request)
    {
        $data = $request->only('name', 'email', 'password', 'role');
        $user = $this->adminService->createUser($data);
        return $this->successResponse($user, 'User created successfully.');
    }

    /**
     * List all classrooms.
     */
    public function listClasses()
    {
        $classes = $this->adminService->getAllClassrooms();
        return $this->successResponse($classes, 'Classroom list retrieved.');
    }

    /**
     * Create a new classroom.
     * Expects 'name' and 'teacher_id' in $request.
     */
    public function createClass(Request $request)
    {
        $data = $request->only('name', 'teacher_id');
        $class = $this->adminService->createClassroom($data);
        return $this->successResponse($class, 'Classroom created successfully.');
    }
}
