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
        return Feedback::select(
            'feedbacks.id',
            'feedbacks.grade',
            'feedbacks.comments',
            'feedbacks.created_at',
            'feedbacks.updated_at',
            'users.id as student_id',
            'users.name as student_name',
            'users.email as student_email'
        )
            ->join('users', 'feedbacks.student_id', '=', 'users.id')
            ->where('feedbacks.assignment_id', $assignmentId)
            ->get();
    }
}