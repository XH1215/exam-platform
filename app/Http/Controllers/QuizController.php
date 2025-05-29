<?php

namespace App\Http\Controllers;

use App\Services\QuizService;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    protected $quizService;

    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'quizNumber' => 'required|integer',
            'questions' => 'required|array|min:1|max:30',
            'questions.*.question' => 'required|string',
            'questions.*.answer' => 'required|string',
            'questions.*.option' => 'required|array|min:2|max:4',
        ]);

        $quiz = $this->quizService->createQuizWithQuestions($validated);

        return response()->json(['message' => 'Quiz created successfully', 'quiz' => $quiz], 201);
    }
}
