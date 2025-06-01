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
        $this->middleware('role:teacher|admin')->only(['store', 'update', 'destroy']);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'questions' => 'required|array|min:1',
            'questions.*.assignment_id' => 'required|integer|exists:assignments,id',
            'questions.*.question_text' => 'required|string',
            'questions.*.correct_answer' => 'required|string',
            'questions.*.options' => 'required|array|min:2|max:4',
        ]);

        $created = [];
        foreach ($data['questions'] as $q) {
            $created[] = $this->service->addQuestion($q);
        }

        return response()->json($created, 201);
    }

    public function listByAssignment(Request $request)
    {
        $data = $request->validate([
            'assignment_id' => 'required|integer|exists:assignments,id',
        ]);

        $questions = $this->service->getQuestionsByAssignment($data['assignment_id']);
        return response()->json($questions);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'question_text' => 'sometimes|string',
            'correct_answer' => 'sometimes|string',
            'options' => 'sometimes|array|min:2|max:4',
        ]);

        $question = $this->service->updateQuestion((int) $id, $data);
        return response()->json($question);
    }

    public function destroy($id)
    {
        $this->service->removeQuestion((int) $id);
        return response()->json(['message' => 'Deleted'], 200);
    }
}
