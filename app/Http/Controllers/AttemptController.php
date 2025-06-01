<?php

namespace App\Http\Controllers;

use App\Services\AttemptService;
use Illuminate\Http\Request;

class AttemptController extends Controller
{
    protected AttemptService $attemptService;

    public function __construct(AttemptService $attemptService)
    {
        $this->attemptService = $attemptService;
        $this->middleware('auth:api');
    }

    /**
     * List all attempts of the authenticated student.
     */
    public function studentAttempts(Request $request)
    {
        $studentId = $request->user()->id;
        $attempts = $this->attemptService->getStudentAttempts($studentId);
        return response()->json($attempts);
    }

    /**
     * Get details of a specific attempt (any role).
     */
    public function attemptDetail($id)
    {
        $attempt = $this->attemptService->getAttemptDetail((int) $id);
        return response()->json($attempt);
    }

    /**
     * Student submits answers.
     */
    public function submit(Request $request)
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
}
