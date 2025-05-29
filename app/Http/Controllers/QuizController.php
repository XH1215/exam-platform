<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StudentService;

class QuizController extends Controller
{
    protected $studentService;

    /**
     * Handles quiz submissions by students.
     */
    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
        $this->middleware('auth:api');
    }

    /**
     * Submit quiz answers. Returns a score.
     */
    public function submit(Request $request)
    {
        $data = $request->all();
        $result = $this->studentService->submitQuiz($data);
        return $this->successResponse($result, 'Quiz submitted.');
    }
}
