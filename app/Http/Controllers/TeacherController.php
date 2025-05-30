<?php

namespace App\Http\Controllers;

use App\Services\AssignmentService;
use App\Services\QuestionService;
use App\Services\FeedbackService;
use App\Services\UserService;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    protected $assignments;
    protected $questions;
    protected $feedbacks;
    protected $userService;

    public function __construct(
        AssignmentService $assignments,
        QuestionService $questions,
        FeedbackService $feedbacks,
        UserService $userService
    ) {
        $this->assignments = $assignments;
        $this->questions   = $questions;
        $this->feedbacks   = $feedbacks;
        $this->userService = $userService;

        $this->middleware(['auth:sanctum', 'role:teacher']);
    }

    // ... other methods omitted for brevity ...

    /**
     * Update teacher profile.
     */
    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'name'  => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $request->user()->id,
        ]);

        $updated = $this->userService->updateProfile(
            $request->user()->id,
            $data
        );

        return response()->json($updated);
    }

    /**
     * Change teacher password.
     */
    public function changePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:6|confirmed',
        ]);

        $this->userService->changePassword(
            $request->user()->id,
            $data['current_password'],
            $data['new_password']
        );

        return response()->json(['message' => 'Password changed'], 200);
    }
}
