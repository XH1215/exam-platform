<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\QuestionService;
use Illuminate\Validation\ValidationException;

class QuestionController extends Controller
{
    protected $service;

    public function __construct(QuestionService $service)
    {
        $this->service = $service;
        $this->middleware(middleware: 'jwt.auth');
        $this->middleware('role:teacher|admin');
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'questions' => 'required|array|min:1',
                'questions.*.assignment_id' => 'required|integer|exists:assignments,id',
                'questions.*.question_text' => 'required|string',
                'questions.*.correct_answer' => 'required|string',
                'questions.*.options' => 'required|array|min:2|max:4',
            ]);
        } catch (ValidationException $e) {
            return $this->errorResponse('Validation failed.', 400, [
                'errors' => $e->errors(),
            ]);
        }

        $result = $this->service->addQuestions($data['questions']);

        if (isset($result['errors'])) {
            return $this->errorResponse('Some questions failed to store.', 422, [
                'failed_indexes' => $result['errors']
            ]);
        }

        return $this->successResponse($result['data'], 'Questions created successfully.', 201);
    }

    public function listByAssignment(Request $request)
    {
        try {
            $data = $request->validate([
                'assignment_id' => 'required|integer|exists:assignments,id',
            ]);
        } catch (ValidationException $e) {
            return $this->errorResponse('Validation failed.', 400, [
                'errors' => $e->errors(),
            ]);
        }
        $questions = $this->service->getQuestionsByAssignment($data['assignment_id']);
        return $this->successResponse($questions, 'Questions retrieved successfully.', 200);
    }

    public function update(Request $request, $id)
    {
        try {
            $data = $request->validate([
                'question_text' => 'sometimes|string',
                'correct_answer' => 'sometimes|string',
                'options' => 'sometimes|array|min:2|max:4',
            ]);
        } catch (ValidationException $e) {
            return $this->errorResponse('Validation failed.', 400, [
                'errors' => $e->errors(),
            ]);
        }

        $question = $this->service->updateQuestion((int) $id, $data);
        return $this->successResponse($question, 'Question updated successfully.', 200);
    }

    public function destroy($id)
    {
        $this->service->removeQuestion((int) $id);
        return $this->successResponse(null, 'Question deleted successfully.', 200);
    }
}
