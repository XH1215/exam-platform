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

    public function getAllByStudent(int $studentId)
    {
        return Feedback::with(['assignment:id,title', 'teacher:id,name'])
            ->where('student_id', $studentId)
            ->orderByDesc('updated_at')
            ->get()
            ->map(function ($feedback) {
                return [
                    'assignment_title' => $feedback->assignment->title,
                    'teacher_name' => $feedback->teacher->name,
                    'grade' => $feedback->grade,
                    'comments' => $feedback->comments,
                    'updated_at' => $feedback->updated_at->toDateTimeString(),
                ];
            });
    }

    public function getFeedbacksWithStudentByAssignmentAndTeacher(int $assignmentId, int $teacherId)
    {
        return Feedback::with(['student:id,name,email'])
            ->where('assignment_id', $assignmentId)
            ->where('teacher_id', $teacherId)
            ->get();
    }
}