<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\QuestionService;

class QuestionController extends Controller
{
    protected $service;

    public function __construct(QuestionService $service)
    {
        $this->service = $service;
        $this->middleware('auth:api');
        $this->middleware('role:admin');
        $this->middleware('role:teacher');
        $this->middleware('role:student');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'assignment_id' => 'required|string',
            'question_text' => 'required|string',
            'correct_answer' => 'required|string',
            'options' => 'required|array|min:1',
        ]);

        // QuestionController.php 中的调用
        $question = $this->service->addQuestion([
            'assignment_id' => $data['assignment_id'],
            'question_text' => $data['question_text'],
            'correct_answer' => $data['correct_answer'],
            'options' => $data['options'],
        ]);

        return response()->json($question, 201);
    }

    public function index($quizId)
    {
        $questions = $this->service->getQuestions($quizId);
        return response()->json($questions);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'question' => 'sometimes|string',
            'answer' => 'sometimes|string',
            'options' => 'sometimes|array',
        ]);

        $question = $this->service->updateQuestion($id, $data);
        return response()->json($question);
    }

    public function destroy($id)
    {
        $this->service->removeQuestion($id);
        return response()->json(['message' => 'Deleted'], 200);
    }
}