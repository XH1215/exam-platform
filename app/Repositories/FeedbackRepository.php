<?php

namespace App\Repositories;

use App\Models\Feedback;

class FeedbackRepository
{
    public function createOrUpdateByAssignment(array $data)
    {
        return Feedback::updateOrCreate(
            [
                'assignment_id' => $data['assignment_id'],
                'student_id' => $data['student_id'],
            ],
            [
                'teacher_id' => $data['teacher_id'],
                'grade' => $data['grade'],
                'comments' => $data['comments'],
            ]
        );
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