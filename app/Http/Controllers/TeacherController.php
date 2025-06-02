<?php

namespace App\Http\Controllers;

use App\Services\{AssignmentService, FeedbackService, UserService};
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class TeacherController extends Controller
{
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

        $this->middleware('jwt.auth');
        $this->middleware('role:teacher');
    }

    public function indexAssignments(Request $request)
    {
        try {
            $teacherId = $request->user()->id;
            $assignments = $this->assignmentService->listByTeacher($teacherId);

            return $this->successResponse(
                $assignments,
                'Assignments retrieved successfully.',
                200
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve assignments.',
                500,
                [
                    'error_code' => 'LIST_ASSIGNMENTS_FAILED',
                    'message' => $e->getMessage()
                ]
            );
        }
    }

    public function createAssignment(Request $request)
    {
        try {
            $data = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'due_date' => 'required|date',
            ]);
        } catch (ValidationException $e) {
            return $this->errorResponse(
                'Validation failed.',
                400,
                $e->errors()
            );
        }

        $data['teacher_id'] = $request->user()->id;

        try {
            $assignment = $this->assignmentService->createAssignment($data);

            return $this->successResponse(
                $assignment,
                'Assignment created successfully.',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to create assignment.',
                400,
                [
                    'error_code' => 'CREATE_ASSIGNMENT_FAILED',
                ]
            );
        }
    }

    public function showAssignment($id, Request $request)
    {
        try {
            $data = $this->assignmentService->getAssignmentWithStudentDetails((int) $id);
            $assignment = $data['assignment'];
            $students = $data['students'];

            if ($assignment->teacher_id !== $request->user()->id) {
                return $this->errorResponse('Unauthorized', 401);
            }

            return $this->successResponse(
                [
                    'id' => $assignment->id,
                    'title' => $assignment->title,
                    'description' => $assignment->description,
                    'due_date' => $assignment->due_date,
                    'teacher_id' => $assignment->teacher_id,
                    'created_at' => $assignment->created_at,
                    'updated_at' => $assignment->updated_at,
                    'students' => $students,
                ],
                'Assignment retrieved successfully.',
                200
            );
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Assignment not found.', 400, [
                'error_code' => 'SHOW_ASSIGNMENT_NOT_FOUND',
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve assignment.', 500, [
                'error_code' => 'SHOW_ASSIGNMENT_FAILED',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function updateAssignment($id, Request $request)
    {
        try {
            $data = $request->validate([
                'title' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'due_date' => 'sometimes|date',
            ]);
        } catch (ValidationException $e) {
            return $this->errorResponse('Validation failed.', 400, $e->errors());
        }

        try {
            $assignment = $this->assignmentService->getAssignment((int) $id);

            if ($assignment->teacher_id !== $request->user()->id) {
                return $this->errorResponse('Unauthorized', 401);
            }

            $updated = $this->assignmentService->updateAssignment((int) $id, $data);

            return $this->successResponse(
                $updated,
                'Assignment updated successfully.',
                200
            );
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Assignment not found.', 400, [
                'error_code' => 'UPDATE_ASSIGNMENT_NOT_FOUND',
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update assignment.', 500, [
                'error_code' => 'UPDATE_ASSIGNMENT_FAILED',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function deleteAssignment($id, Request $request)
    {
        try {
            $assignment = $this->assignmentService->getAssignment((int) $id);

            if ($assignment->teacher_id !== $request->user()->id) {
                return $this->errorResponse('Unauthorized', 401);
            }

            $this->assignmentService->deleteAssignment((int) $id);

            return $this->successResponse(
                null,
                'Assignment deleted successfully.',
                200
            );
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Assignment not found.', 400, [
                'error_code' => 'DELETE_ASSIGNMENT_NOT_FOUND',
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete assignment.', 500, [
                'error_code' => 'DELETE_ASSIGNMENT_FAILED',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function assignStudent($assignmentId, Request $request)
    {
        try {
            $data = $request->validate([
                'student_emails' => 'required|array|min:1',
                'student_emails.*' => 'email',
            ]);
        } catch (ValidationException $e) {
            return $this->errorResponse('Validation failed.', 400, $e->errors());
        }

        try {
            $assignment = $this->assignmentService->getAssignment((int) $assignmentId);

            if ($assignment->teacher_id !== $request->user()->id) {
                return $this->errorResponse('Unauthorized', 401, [
                    'error_code' => 'UNAUTHORIZED_ACCESS',
                    'message' => 'You are not allowed to assign students to this assignment.'
                ]);
            }

            [$assignedIds, $invalidEmails] = $this->assignmentService->assignStudentsByEmail(
                (int) $assignmentId,
                $data['student_emails']
            );

            $statusCode = empty($invalidEmails) ? 200 : 201;
            $message = empty($invalidEmails)
                ? 'All students assigned successfully.'
                : 'Some students could not be assigned.';

            return $this->successResponse(
                [
                    'assigned_student_ids' => $assignedIds,
                    'invalid_emails' => $invalidEmails
                ],
                $message,
                $statusCode
            );
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Assignment not found.', 400, [
                'error_code' => 'ASSIGN_STUDENTS_NOT_FOUND',
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to assign students.', 500, [
                'error_code' => 'ASSIGN_STUDENTS_FAILED',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function submitFeedback(Request $request)
    {
        try {
            $data = $request->validate([
                'assignment_id' => 'required|integer|exists:assignments,id',
                'student_id' => 'required|integer|exists:users,id',
                'grade' => 'required|numeric|min:0|max:100',
                'comments' => 'nullable|string|max:2000',
            ]);
        } catch (ValidationException $e) {
            return $this->errorResponse('Validation failed.', 400, $e->errors());
        }

        try {
            $feedback = $this->feedbackService->submitOrUpdateFeedback(
                $data['assignment_id'],
                $data['student_id'],
                $request->user()->id,
                $data['grade'],
                $data['comments'] ?? ''
            );

            return $this->successResponse(
                $feedback,
                $feedback->wasRecentlyCreated
                    ? 'Feedback created successfully.'
                    : 'Feedback updated successfully.',
                $feedback->wasRecentlyCreated ? 201 : 200
            );
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Assignment or student not found.', 400, [
                'error_code' => 'SUBMIT_FEEDBACK_NOT_FOUND',
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to submit feedback.', 500, [
                'error_code' => 'SUBMIT_FEEDBACK_FAILED',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getFeedbackByAssignment($assignmentId, Request $request)
    {
        try {
            $assignment = $this->assignmentService->getAssignment((int) $assignmentId);

            if ($assignment->teacher_id !== $request->user()->id) {
                return $this->errorResponse('Unauthorized', 401);
            }

            $feedbacks = $this->feedbackService->getAllFeedbackForAssignment((int) $assignmentId);

            return $this->successResponse(
                $feedbacks,
                'Feedback list retrieved successfully.',
                200
            );
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Assignment not found.', 400, [
                'error_code' => 'GET_FEEDBACK_NOT_FOUND',
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve feedback list.', 500, [
                'error_code' => 'GET_FEEDBACK_FAILED',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getStudentAssignmentStatus($assignmentId, Request $request)
    {
        try {
            $assignment = $this->assignmentService->getAssignment((int) $assignmentId);

            if ($assignment->teacher_id !== $request->user()->id) {
                return $this->errorResponse('Unauthorized', 401);
            }

            $statusList = $this->assignmentService->getAssignmentCompletionStatus((int) $assignmentId);

            return $this->successResponse(
                $statusList,
                'Assignment status list retrieved successfully.',
                200
            );
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Assignment not found.', 400, [
                'error_code' => 'GET_STATUS_NOT_FOUND',
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve assignment status list.', 500, [
                'error_code' => 'GET_STATUS_FAILED',
                'message' => $e->getMessage()
            ]);
        }
    }
}
