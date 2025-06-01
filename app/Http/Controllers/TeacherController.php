<?php

namespace App\Http\Controllers;

use App\Services\{AssignmentService, FeedbackService, UserService};
use Illuminate\Http\Request;
use App\Traits\HasProfile;

class TeacherController extends Controller
{
    use HasProfile;

    protected AssignmentService $assignmentService;
    protected UserService $userService;
    protected FeedbackService $feedbackService;

    public function __construct(
        AssignmentService $assignmentService,
        UserService $userService,
        FeedbackService $feedbackService
    ) {
        $this->assignmentService = $assignmentService;
        $this->userService = $userService;
        $this->feedbackService = $feedbackService;
        $this->middleware(['jwt.auth', 'role:teacher']);
    }

    public function indexAssignments(Request $request)
    {
        $teacherId = $request->user()->id;
        $assignments = $this->assignmentService->listByTeacher($teacherId);
        return response()->json($assignments);
    }

    public function createAssignment(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
        ]);
        $data['teacher_id'] = $request->user()->id;

        $assignment = $this->assignmentService->createAssignment($data);
        return response()->json($assignment, 201);
    }

    public function showAssignment($id, Request $request)
    {
        $assignment = $this->assignmentService->getAssignment((int) $id);

        if ($assignment->teacher_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($assignment);
    }

    public function updateAssignment($id, Request $request)
    {
        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'due_date' => 'sometimes|date',
        ]);

        $assignment = $this->assignmentService->getAssignment((int) $id);

        if ($assignment->teacher_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $updated = $this->assignmentService->updateAssignment((int) $id, $data);
        return response()->json($updated);
    }

    public function deleteAssignment($id, Request $request)
    {
        $assignment = $this->assignmentService->getAssignment((int) $id);

        if ($assignment->teacher_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $this->assignmentService->deleteAssignment((int) $id);
        return response()->json(['message' => 'Assignment deleted'], 200);
    }

    public function assignStudent($assignmentId, Request $request)
    {
        $assignment = $this->assignmentService->getAssignment((int) $assignmentId);

        if ($assignment->teacher_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'integer|exists:users,id'
        ]);

        $results = $this->assignmentService->assignStudents((int) $assignmentId, $data['student_ids']);
        return response()->json(['assigned' => $results]);
    }

    public function submitFeedback(Request $request)
    {
        $data = $request->validate([
            'assignment_id' => 'required|integer|exists:assignments,id',
            'student_id' => 'required|integer|exists:users,id',
            'grade' => 'required|numeric|min:0|max:100',
            'comments' => 'nullable|string|max:2000',
        ]);

        $feedback = $this->feedbackService->submitOrUpdateFeedback(
            $data['assignment_id'],
            $data['student_id'],
            $request->user()->id,
            $data['grade'],
            $data['comments'] ?? ''
        );

        return response()->json($feedback, $feedback->wasRecentlyCreated ? 201 : 200);
    }

    public function getFeedbackByAssignment($assignmentId, Request $request)
    {
        $assignment = $this->assignmentService->getAssignment((int) $assignmentId);
        if ($assignment->teacher_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $feedbacks = $this->feedbackService->getAllFeedbackForAssignment((int) $assignmentId);
        return response()->json($feedbacks);
    }

    public function getStudentAssignmentStatus($assignmentId, Request $request)
    {
        $assignment = $this->assignmentService->getAssignment((int) $assignmentId);

        if ($assignment->teacher_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $statusList = $this->assignmentService->getAssignmentCompletionStatus((int) $assignmentId);
        return response()->json($statusList);
    }
}
