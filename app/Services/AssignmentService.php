<?php

namespace App\Services;

use App\Repositories\AssignmentRepository;
use App\Models\Assignment;
use App\Repositories\FeedbackRepository;
use Illuminate\Validation\ValidationException;
use app\Models\User;
use App\Repositories\AttemptRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Crypt;

class AssignmentService
{
    protected AssignmentRepository $assignmentRepo;
    protected AttemptRepository $attemptRepo;
    protected FeedbackRepository $feedbackRepo;

    public function __construct(
        AssignmentRepository $assignmentRepo,
        AttemptRepository $attemptRepo,
        FeedbackRepository $feedbackRepo

    ) {
        $this->assignmentRepo = $assignmentRepo;
        $this->attemptRepo = $attemptRepo;
        $this->feedbackRepo = $feedbackRepo;
    }

    public function listByTeacher(int $teacherId)
    {
        return $this->assignmentRepo->getByTeacher($teacherId);
    }

    public function createAssignment(array $data): Assignment
    {
        $teacherId = $data['teacher_id'];
        $title = strtolower(trim($data['title']));

        $exists = Assignment::where('teacher_id', $teacherId)
            ->whereRaw('LOWER(title) = ?', [$title])
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'title' => ['You have already created an assignment with this title.']
            ]);
        }

        return $this->assignmentRepo->create($data);
    }

    public function getAssignment(int $id): Assignment
    {
        return $this->assignmentRepo->getById($id);
    }

    public function updateAssignment(int $id, array $data): Assignment
    {
        return $this->assignmentRepo->update($id, $data);
    }

    public function deleteAssignment(int $id): void
    {
        $this->assignmentRepo->delete($id);
    }

    public function assignStudentsByEmail(int $assignmentId, array $studentEmails): array
    {
        $assignment = $this->getAssignment($assignmentId);

        $validUsers = User::whereIn('email', $studentEmails)
            ->where('role', 'student')
            ->get();

        $validUserIds = $validUsers->pluck('id')->toArray();
        $validEmails = $validUsers->pluck('email')->toArray();

        $invalidEmails = array_diff($studentEmails, $validEmails);

        $assignment->students()->syncWithoutDetaching($validUserIds);

        return [$validUserIds, $invalidEmails];
    }

    public function getAssignmentCompletionStatus(int $assignmentId): array
    {
        $assignment = $this->assignmentRepo->getById($assignmentId);

        $assignedStudents = $assignment->students;

        $results = $assignedStudents->map(function ($student) use ($assignmentId) {
            $hasAttempt = $this->attemptRepo->getByStudent($student->id)
                ->where('assignment_id', $assignmentId)
                ->isNotEmpty();

            return [
                'student_id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'completed' => $hasAttempt,
            ];
        });

        return [
            'total_students' => $assignedStudents->count(),
            'total_done' => $results->where('completed', true)->count(),
            'data' => $results->values()->toArray(),
        ];
    }

    public function unassignStudent(int $assignmentId, int $studentId): void
    {
        $assignment = $this->getAssignment($assignmentId);
        $assignment->students()->detach($studentId);
    }

    public function getAssignedStudents(int $assignmentId): array
    {
        $assignment = $this->getAssignment($assignmentId);
        return $assignment->students()->get(['id', 'name', 'email'])->toArray();
    }
    public function getAssignmentWithStudentDetails(int $id): array
    {
        $raw = $this->assignmentRepo->getByIdWithStudentDetails($id);

        $assignment = [
            'id' => $raw['id'],
            'title' => $raw['title'],
            'description' => $raw['description'],
            'due_date' => $raw['due_date'],
            'teacher_id' => $raw['teacher_id'],
            'created_at' => $raw['created_at'],
            'updated_at' => $raw['updated_at'],
        ];

        $students = $raw['assigned_students'] ?? [];

        return [
            'assignment' => (object) $assignment,
            'students' => $students,
        ];
    }

    public function getAssignmentResultsForTeacher(int $teacherId): array
    {
        $rawAssignments = $this->assignmentRepo->getByTeacher($teacherId);

        $results = $rawAssignments->map(function ($assignment) use ($teacherId) {
            $count = $assignment->questions()->count();
            $hasQuestions = $count > 0;

            $rawAttempts = $this->attemptRepo->getAttemptsWithStudentByAssignment($assignment->id);
            $attempts = $rawAttempts->map(function ($attempt) {
                return [
                    'attempt_id' => $attempt->attempt_id,
                    'assignment_id' => $attempt->assignment_id,
                    'student_id' => $attempt->student_id,
                    'score' => Crypt::decryptString($attempt->encrypted_score),
                    'student' => [
                        'id' => $attempt->student->id,
                        'name' => $attempt->student->name,
                        'email' => $attempt->student->email,
                    ],
                    'created_at' => $attempt->created_at,
                    'updated_at' => $attempt->updated_at,
                ];
            })->toArray();

            $feedbacks = $this->feedbackRepo->getFeedbacksWithStudentByAssignmentAndTeacher(
                $assignment->id,
                $teacherId
            );

            return [
                'assignment' => [
                    'id' => $assignment->id,
                    'title' => $assignment->title,
                    'description' => $assignment->description,
                    'due_date' => $assignment->due_date,
                    'teacher_id' => $assignment->teacher_id,
                    'created_at' => $assignment->created_at,
                    'updated_at' => $assignment->updated_at,
                    'has_questions' => $hasQuestions,
                    'questions_count' => $count,
                ],
                'attempts' => $attempts,
                'feedbacks' => $feedbacks,
            ];
        })->toArray();

        return $results;
    }
}
