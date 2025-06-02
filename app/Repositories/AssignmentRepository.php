<?php

namespace App\Repositories;

use App\Models\Assignment;
use \App\Models\Feedback;

class AssignmentRepository
{
    public function allByTeacher($teacherId): \Illuminate\Support\Collection
    {
        return Assignment::where('teacher_id', $teacherId)->get();
    }

    public function find(int $id): Assignment
    {
        return Assignment::where('id', $id)->firstOrFail();
    }

    public function create(array $data): Assignment
    {
        return Assignment::create($data);
    }

    public function update(int $id, array $data): Assignment
    {
        $assignment = Assignment::findOrFail($id);
        $assignment->update($data);
        return $assignment->fresh();
    }

    public function delete(int $id): bool
    {
        $assignment = Assignment::findOrFail($id);
        return $assignment->delete();
    }

    public function assignStudent(int $assignmentId, int $studentId): Assignment
    {
        $assignment = Assignment::findOrFail($assignmentId);
        $assignment->students()->syncWithoutDetaching([$studentId]);
        return $assignment->fresh();
    }

    public function getAll(): \Illuminate\Support\Collection
    {
        return Assignment::all();
    }

    public function getById(int $id): Assignment
    {
        return Assignment::findOrFail($id);
    }

    public function getByTeacher(int $teacherId): \Illuminate\Support\Collection
    {
        return Assignment::where('teacher_id', $teacherId)->get();
    }

    public function findByStudentAndAssignment(int $studentId, int $assignmentId)
    {
        return Feedback::whereHas('attempt', function ($query) use ($studentId, $assignmentId) {
            $query->where('student_id', $studentId)
                ->where('assignment_id', $assignmentId);
        })->first();
    }

    public function getByIdWithStudentDetails(int $id): array
    {
        $assignment = Assignment::with([
            'students' => function ($query) {
                $query->select('users.id', 'name', 'email');
            }
        ])->findOrFail($id);

        return [
            'id' => $assignment->id,
            'title' => $assignment->title,
            'description' => $assignment->description,
            'due_date' => $assignment->due_date,
            'teacher_id' => $assignment->teacher_id,
            'created_at' => $assignment->created_at,
            'updated_at' => $assignment->updated_at,
            'assigned_students' => $assignment->students->map(fn($student) => [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
            ])->toArray(),
        ];
    }
}
