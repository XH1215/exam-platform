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
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'question' => 'required|string',
            'answer' => 'required|string',
            'options' => 'required|array|min:1',
        ]);

        $question = $this->service->addQuestion(
            $data['quiz_id'],
            $data['question'],
            $data['answer'],
            $data['options']
        );

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