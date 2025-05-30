<?php

namespace App\Http\Controllers;

use App\Services\AssignmentService;
use App\Services\AttemptService;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    protected $service;
    protected $attemptService;

    public function __construct(AssignmentService $service, AttemptService $attemptService)
    {
        $this->service = $service;
        $this->attemptService = $attemptService;
        $this->middleware(['auth:api']);
        $this->middleware('role:admin');
        $this->middleware('role:teacher'); 
        $this->middleware('role:student');
    }

    public function index(Request $request)
    {
        $teacherId = $request->user()->id;
        return response()->json($this->service->listByTeacher($teacherId));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
        ]);
        $data['teacher_id'] = $request->user()->id;
        return response()->json($this->service->createAssignment($data), 201);
    }

    public function show($id)
    {
        return response()->json($this->service->getAssignment((int) $id));
    }

    public function update($id, Request $request)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'due_date' => 'sometimes|date',
        ]);
        return response()->json($this->service->updateAssignment((int) $id, $data));
    }

    public function destroy($id)
    {
        $this->service->deleteAssignment((int) $id);
        return response()->json(null, 204);
    }

    public function assignStudent($assignmentId, Request $request)
    {
        $studentId = $request->validate(['student_id' => 'required|integer|exists:users,id'])['student_id'];
        return response()->json($this->service->assignStudent((int) $assignmentId, $studentId));
    }

    public function submitAnswers($assignmentId, Request $request)
    {
        $data = $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required'
        ]);
        $studentId = $request->user()->id;
        $attempt = $this->attemptService->submitAnswers($studentId, (int) $assignmentId, $data['answers']);
        return response()->json($attempt, 201);
    }

    public function viewResults(Request $request)
    {
        $studentId = $request->user()->id;
        $attempts = $this->attemptService->getStudentAttempts($studentId);
        return response()->json($attempts);
    }

    public function getResultDetail($attemptId)
    {
        $attempt = $this->attemptService->getAttemptDetail((int) $attemptId);
        return response()->json($attempt);
    }
}