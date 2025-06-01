<?php

namespace App\Services;

use App\Repositories\AssignmentRepository;
use App\Models\Assignment;
use App\Models\Feedback;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AssignmentService
{
    protected AssignmentRepository $assignmentRepo;

    public function __construct(AssignmentRepository $assignmentRepo)
    {
        $this->assignmentRepo = $assignmentRepo;
    }

    public function listByTeacher(int $teacherId)
    {
        return $this->assignmentRepo->getByTeacher($teacherId);
    }

    public function createAssignment(array $data): Assignment
    {
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

    public function assignStudents(int $assignmentId, array $studentIds): array
    {
        $assignment = $this->getAssignment($assignmentId);
        $assignment->students()->syncWithoutDetaching($studentIds);
        return $assignment->students()->pluck('id')->toArray();
    }

    public function getAssignmentCompletionStatus(int $assignmentId): array
    {
        $assignment = $this->getAssignment($assignmentId);

        $assignedStudents = $assignment->students()->get();
        $completedStudentIds = Feedback::where('assignment_id', $assignmentId)
            ->pluck('student_id')
            ->toArray();

        return $assignedStudents->map(function ($student) use ($completedStudentIds) {
            return [
                'student_id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'completed' => in_array($student->id, $completedStudentIds)
            ];
        })->toArray();
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
}
