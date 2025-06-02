<?php

namespace App\Http\Controllers;

use App\Services\AssignmentService;
use App\Services\AttemptService;
use App\Services\FeedbackService;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Traits\HasProfile;
use Illuminate\Validation\ValidationException;

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
        $this->middleware(middleware: 'jwt.auth');
        $this->middleware(middleware: 'role:student');
    }

    public function listAssignments(Request $request)
    {
        try {
            $student = $request->user();
            $assignments = $student->assignments()->get();

            return $this->successResponse($assignments, 'Assignments retrieved successfully.', 200);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve assignments.', 500, [
                'error_code' => 'ASSIGNMENT_LIST_ERROR',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function submitAnswers(Request $request)
    {
        try {
            $data = $request->validate([
                'assignment_id' => 'required|integer|exists:assignments,id',
                'answers' => 'required|array',
                'answers.*' => 'required',
            ]);
        } catch (ValidationException $e) {
            return $this->errorResponse('Validation failed.', 422, [
                'errors' => $e->errors()
            ]);
        }

        try {
            $studentId = $request->user()->id;
            $assignmentId = $data['assignment_id'];
            $answers = $data['answers'];

            $attempt = $this->attemptService->submitAnswers($studentId, $assignmentId, $answers);

            return $this->successResponse($attempt, 'Answers submitted successfully.', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to submit answers.', 500, [
                'error_code' => 'SUBMIT_ANSWER_ERROR',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getMyFeedbackByAssignment($assignmentId)
    {
        try {
            $studentId = auth()->id();
            $feedback = $this->feedbackService->getFeedbackByAssignmentAndStudent((int) $assignmentId, $studentId);

            if ($feedback === null) {
                $feedback = [];
            }
            return $this->successResponse($feedback, 'Feedback retrieved successfully.', 200);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve feedback.', 500, [
                'error_code' => 'FEEDBACK_RETRIEVAL_ERROR',
                'message' => $e->getMessage()
            ]);
        }
    }
}
