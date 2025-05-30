<?php

namespace App\Repositories;

use App\Models\Feedback;

class FeedbackRepository
{
    public function create(array $data)
    {
        return Feedback::create($data);
    }

    public function update(Feedback $feedback, array $data)
    {
        $feedback->update($data);
        return $feedback;
    }

    public function delete(Feedback $feedback)
    {
        return $feedback->delete();
    }

    public function getByAssignmentAndStudent(int $assignmentId, int $studentId)
    {
        return Feedback::where('assignment_id', $assignmentId)
            ->where('student_id', $studentId)
            ->first();
    }

    public function getAllByAssignment(int $assignmentId)
    {
        return Feedback::where('assignment_id', $assignmentId)->get();
    }
}