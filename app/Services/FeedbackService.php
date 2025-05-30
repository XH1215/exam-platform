<?php

namespace App\Services;

use App\Repositories\FeedbackRepository;

class FeedbackService
{
    protected $feedbacks;

    public function __construct(FeedbackRepository $feedbacks)
    {
        $this->feedbacks = $feedbacks;
    }

    /**
     * Create or update feedback for an attempt.
     */
    public function submitFeedback(int $attemptId, int $teacherId, float $grade, string $comments)
    {
        return $this->feedbacks->createOrUpdate([
            'attempt_id' => $attemptId,
            'teacher_id' => $teacherId,
            'grade'      => $grade,
            'comments'   => $comments,
        ]);
    }

    /**
     * Get feedback by assignment and student.
     */
    public function getFeedbackByAssignmentAndStudent(int $assignmentId, int $studentId)
    {
        return $this->feedbacks->getByAssignmentAndStudent($assignmentId, $studentId);
    }

    /**
     * Get all feedback for a particular assignment.
     */
    public function getAllFeedbackForAssignment(int $assignmentId)
    {
        return $this->feedbacks->getAllByAssignment($assignmentId);
    }
}
