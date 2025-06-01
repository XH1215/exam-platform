<?php

namespace App\Http\Controllers;

use App\Services\AssignmentService;
use App\Services\AttemptService;
use App\Services\FeedbackService;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Traits\HasProfile;

class StudentController extends Controller
{
    use HasProfile;

    protected AssignmentService $assignmentService;
    protected AttemptService $attemptService;
    protected FeedbackService $feedbackService;
    protected UserService $userService;

    public function __construct(
        AssignmentService $assignmentService,
        AttemptService $attemptService,
        FeedbackService $feedbackService,
        UserService $userService
    ) {
        $this->assignmentService = $assignmentService;
        $this->attemptService = $attemptService;
        $this->feedbackService = $feedbackService;
        $this->userService = $userService;

        $this->middleware('role:student');
    }

    /**
     * List all assignments assigned to the student.
     */
    public function listAssignments(Request $request)
    {
        $student = $request->user();
        $assignments = $student->assignments()->get();
        return response()->json($assignments);
    }

    /**
     * Submit answers for a specific assignment.
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
     * Get feedback for a specific assignment.
     */
    public function getMyFeedbackByAssignment($assignmentId)
    {
        $studentId = auth()->id();
        $feedback = $this->feedbackService->getFeedbackByAssignmentAndStudent((int) $assignmentId, $studentId);
        return response()->json($feedback);
    }
}
