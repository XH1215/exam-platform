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
}
