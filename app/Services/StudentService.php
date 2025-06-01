<?php

namespace App\Services;

use App\Repositories\AssignmentRepository;
use App\Repositories\FeedbackRepository;
use App\Repositories\ScoreRepository;
use App\Models\User;

class StudentService
{
    protected $assignmentRepo;
    protected $feedbackRepo;
    protected $scoreRepo;

    public function __construct(
        AssignmentRepository $assignmentRepo,
        FeedbackRepository $feedbackRepo,
    ) {
        $this->assignmentRepo = $assignmentRepo;
        $this->feedbackRepo = $feedbackRepo;
    }

    /**
     * Get assignments available to the student.
     * Here you might filter by class or student enrollment.
     */
    public function getAssignmentsForStudent(int $studentId)
    {
        // TODO: filter assignments for this student
        return $this->assignmentRepo->getAll();
    }

    /**
     * Submit an assignment: calculate score and store submission.
     * Delegates to ScoreRepository.
     *
     * @param int   $studentId
     * @param array $data ['assignment_id' => int, 'answers' => array]
     */
    public function submitAssignment(int $studentId, array $data)
    {
        // Data validation assumed upstream
        $data['student_id'] = $studentId;
        // ScoreRepository->create will handle persistence
        return $this->scoreRepo->create($data);
    }

    /**
     * Get the student's profile.
     */
    public function getProfile(int $studentId): User
    {
        return auth()->user();
    }

    /**
     * List scores for this student.
     */
    public function getScores(int $studentId)
    {
        return $this->scoreRepo->findByStudent($studentId);
    }

    /**
     * Get feedback for a specific assignment.
     */
    public function getFeedback(int $studentId, int $assignmentId)
    {
        return $this->feedbackRepo->getByAssignmentAndStudent($studentId, $assignmentId);
    }
}
