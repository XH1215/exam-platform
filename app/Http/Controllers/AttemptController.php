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
        $this->middleware('jwt.auth');
    }
    public function studentAttempts(Request $request)
    {
        try {
            $studentId = $request->user()->id;
            $attempts = $this->attemptService->getStudentAttempts($studentId);
            return $this->successResponse($attempts, 'Student attempts retrieved successfully.', 200);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve student attempts.', 500, [
                'error_code' => 'ATTEMPT_LIST_ERROR',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function attemptDetail($id)
    {
        try {
            $attempt = $this->attemptService->getAttemptDetail((int) $id);
            return $this->successResponse($attempt, 'Attempt detail retrieved successfully.', 200);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve attempt detail.', 500, [
                'error_code' => 'ATTEMPT_DETAIL_ERROR',
                'message' => $e->getMessage(),
            ]);
        }
    }
}
