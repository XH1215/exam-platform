<?php

namespace App\Repositories;

use App\Models\Assignment;

class AssignmentRepository
{
    public function allByTeacher($teacherId)
    {
        return Assignment::where('teacher_id', $teacherId)->get();
    }

    public function find($id)
    {
        return Assignment::where('id', $id)->firstOrFail();
    }

    public function create(array $data)
    {
        return Assignment::create($data);
    }
    public function update($id, array $data)
    {
        $assignment = Assignment::findOrFail($id);
        $assignment->update($data);
        return $assignment;
    }
    public function delete($id)
    {
        $assignment = Assignment::findOrFail($id);
        return $assignment->delete();
    }

    public function assignStudent($assignmentId, $studentId)
    {
        $assignment = Assignment::findOrFail($assignmentId);
        $assignment->students()->attach($studentId);
        return $assignment;
    }

    public function getAll()
    {
        return Assignment::all();
    }

    public function getById($id)
    {
        return Assignment::findOrFail($id);
    }

    public function getByTeacher($teacherId)
    {
        return Assignment::where('teacher_id', $teacherId)->get();
    }

    public function findByStudentAndAssignment(int $studentId, int $assignmentId)
    {
        return \App\Models\Feedback::whereHas('attempt', function ($query) use ($studentId, $assignmentId) {
            $query->where('student_id', $studentId)
                ->where('assignment_id', $assignmentId);
        })->first();
    }

}
