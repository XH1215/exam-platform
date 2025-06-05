<?php

namespace App\Services;

use App\Repositories\FeedbackRepository;

class FeedbackService
{
    private FeedbackRepository $feedbacksRepo;

    public function __construct(FeedbackRepository $feedbacksRepo)
    {
        $this->feedbacksRepo = $feedbacksRepo;
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

    public function getAllFeedbackByStudent(int $studentId)
    {
        return $this->feedbacksRepo->getAllByStudent($studentId);
    }

}
