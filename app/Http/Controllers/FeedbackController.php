<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FeedbackService;

class FeedbackController extends Controller
{
    protected $feedbackService;

    public function __construct(FeedbackService $feedbackService)
    {
        $this->feedbackService = $feedbackService;
        $this->middleware(['auth:api', 'role:teacher']);
    }

    /**
     * POST /teacher/feedback
     * { attempt_id, grade, comments }
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'attempt_id' => 'required|integer|exists:attempts,id',
            'grade'      => 'required|numeric|min:0|max:100',
            'comments'   => 'nullable|string|max:2000',
        ]);

        $teacherId = $request->user()->id;
        $feedback = $this->feedbackService->submitFeedback(
            $data['attempt_id'],
            $teacherId,
            $data['grade'],
            $data['comments'] ?? ''
        );

        return response()->json($feedback, $feedback->wasRecentlyCreated ? 201 : 200);
    }

    /**
     * GET /teacher/feedback/{assignmentId}
     */
    public function indexByAssignment($assignmentId)
    {
        $feedbacks = $this->feedbackService->getAllFeedbackForAssignment((int)$assignmentId);
        return response()->json($feedbacks);
    }

    /**
     * GET /student/feedback/{assignmentId}
     */
    public function studentFeedback($assignmentId)
    {
        $studentId = auth()->id();
        $feedback = $this->feedbackService->getFeedbackByAssignmentAndStudent(
            (int)$assignmentId,
            $studentId
        );
        return response()->json($feedback);
    }
}
