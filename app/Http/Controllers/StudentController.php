<?php

namespace App\Http\Controllers;

use App\Services\AssignmentService;
use App\Services\AttemptService;
use App\Services\UserService;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    protected $assignments;
    protected $attemptService;
    protected $userService;

    public function __construct(
        AssignmentService $assignments,
        AttemptService $attemptService,
        UserService $userService
    ) {
        $this->assignments = $assignments;
        $this->attemptService = $attemptService;
        $this->userService = $userService;

        $this->middleware('auth:api');
        $this->middleware('role:student');
    }

    /**
     * View assigned assignments
     */
    public function listAssignments(Request $request)
    {
        $student = $request->user();
        $assignments = $student->assignments()->get();
        return response()->json($assignments);
    }

    /**
     * Submit answers for an assignment
     */
    public function submitAnswers(Request $request)
    {
        $data = $request->validate([
            'assignment_id' => 'required|integer|exists:assignments,id',
            'answers' => 'required|array',
            'answers.*' => 'required',
        ]);

        $studentId = $request->user()->id;
        $assignmentId = $data['assignment_id'];
        $answers = $data['answers'];

        $attempt = $this->attemptService->submitAnswers($studentId, $assignmentId, $answers);

        return response()->json($attempt, 201);
    }

    /**
     * View past attempts
     */
    public function viewResults(Request $request)
    {
        $studentId = $request->user()->id;
        $attempts = $this->attemptService->getStudentAttempts($studentId);
        return response()->json($attempts);
    }

    /**
     * View a specific attempt with details
     */
    public function getResultDetail($attemptId)
    {
        $attempt = $this->attemptService->getAttemptDetail((int) $attemptId);
        $attempt->load(['assignment', 'score', 'feedback']);
        return response()->json($attempt);
    }

    /**
     * View student profile
     */
    public function profile(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Update student profile
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
     * Change student password
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
