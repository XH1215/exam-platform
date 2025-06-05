<?php

namespace App\Repositories;

use App\Models\Attempt;

class AttemptRepository
{
    public function create(array $data)
    {
        return Attempt::create($data);
    }

    public function getByStudent(int $studentId)
    {
        return Attempt::where('student_id', $studentId)->get();
    }

    public function find(int $attemptId)
    {
        return Attempt::with('assignment')->findOrFail($attemptId);
    }

    public function getAttemptsByStudentAndAssignments(int $studentId, array $assignmentIds)
    {
        return Attempt::where('student_id', $studentId)
            ->whereIn('assignment_id', $assignmentIds)
            ->get();
    }

    public function getAttemptsWithStudentByAssignment(int $assignmentId)
    {
        return Attempt::with(['student:id,name,email'])
            ->where('assignment_id', $assignmentId)
            ->get();
    }
}
