<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StudentService;

class StudentController extends Controller
{
    protected $studentService;

    /**
     * Student-specific operations. Protected by 'role:student' middleware.
     */
    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
        $this->middleware('auth:api');
    }

    /**
     * List assignments available to the student.
     */
    public function indexAssignments()
    {
        $assignments = $this->studentService->getAssignmentsForStudent();
        return $this->successResponse($assignments, 'Assignments retrieved.');
    }

    /**
     * Submit an assignment. Creates feedback (e.g. teacher review).
     */
    public function submitAssignment(Request $request)
    {
        $data = $request->all();
        $feedback = $this->studentService->submitAssignment($data);
        return $this->successResponse($feedback, 'Assignment submitted; feedback created.');
    }

    /**
     * Get the student's profile.
     */
    public function profile()
    {
        $user = $this->studentService->getProfile();
        return $this->successResponse($user, 'Student profile retrieved.');
    }
}
