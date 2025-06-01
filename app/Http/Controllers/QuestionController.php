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
    }

    /**
     * Teacher/Admin - Create a new question
     */
    public function store(Request $request)
    {
        $this->middleware('role:teacher|admin');

        $data = $request->validate([
            'assignment_id' => 'required|integer|exists:assignments,id',
            'question_text' => 'required|string',
            'correct_answer' => 'required|string',
            'options' => 'required|array|min:2|max:4',
        ]);

        $question = $this->service->addQuestion($data);
        return response()->json($question, 201);
    }

    /**
     * Student - Get all questions for a specific assignment
     */
    public function listByAssignment(Request $request)
    {
        $data = $request->validate([
            'assignment_id' => 'required|integer|exists:assignments,id',
        ]);

        $questions = $this->service->getQuestions($data['assignment_id']);
        return response()->json($questions);
    }

    /**
     * Teacher/Admin - Update a question
     */
    public function update(Request $request, $id)
    {
        $this->middleware('role:teacher|admin');

        $data = $request->validate([
            'question_text' => 'sometimes|string',
            'correct_answer' => 'sometimes|string',
            'options' => 'sometimes|array|min:2|max:4',
        ]);

        $question = $this->service->updateQuestion((int) $id, $data);
        return response()->json($question);
    }

    /**
     * Teacher/Admin - Delete a question
     */
    public function destroy($id)
    {
        $this->middleware('role:teacher|admin');

        $this->service->removeQuestion((int) $id);
        return response()->json(['message' => 'Deleted'], 200);
    }
}
