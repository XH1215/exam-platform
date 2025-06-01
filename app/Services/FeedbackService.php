<?php

namespace App\Services;

use App\Repositories\FeedbackRepository;

class FeedbackService
{
    protected FeedbackRepository $feedbacksRepo;

    public function __construct(FeedbackRepository $feedbacks)
    {
        $this->feedbacks = $feedbacks;
    }

    public function submitOrUpdateFeedback(int $assignmentId, int $studentId, int $teacherId, float $grade, string $comments)
    {
        return $this->feedbacksRepo->createOrUpdateByAssignment([
            'assignment_id' => $assignmentId,
            'student_id' => $studentId,
            'teacher_id' => $teacherId,
            'grade' => $grade,
            'comments' => $comments,
        ]);
    }

    public function getFeedbackByAssignmentAndStudent(int $assignmentId, int $studentId)
    {
        return $this->feedbacksRepo->getByAssignmentAndStudent($assignmentId, $studentId);
    }

    public function getAllFeedbackForAssignment(int $assignmentId)
    {
        return $this->feedbacksRepo->getAllByAssignment($assignmentId);
    }
}
