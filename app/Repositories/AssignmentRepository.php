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
        return Assignment::findOrFail($id);
    }

    public function create(array $data)
    {
        return Assignment::create($data);
    }

    public function update(Assignment $assignment, array $data)
    {
        $assignment->update($data);
        return $assignment;
    }

    public function delete(Assignment $assignment)
    {
        return $assignment->delete();
    }

    public function assignStudent($assignmentId, $studentId)
    {
        $assignment = Assignment::findOrFail($assignmentId);
        $assignment->students()->attach($studentId);
        return $assignment;
    }
}
