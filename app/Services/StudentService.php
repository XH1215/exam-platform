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

    public function getAssignmentsForStudent(int $studentId)
    {
        return $this->assignmentRepo->getAll();
    }
    public function submitAssignment(int $studentId, array $data)
    {
        $data['student_id'] = $studentId;
        return $this->scoreRepo->create($data);
    }
    public function getProfile(int $studentId): User
    {
        return auth()->user();
    }
    public function getScores(int $studentId)
    {
        return $this->scoreRepo->findByStudent($studentId);
    }
    public function getFeedback(int $studentId, int $assignmentId)
    {
        return $this->feedbackRepo->getByAssignmentAndStudent($studentId, $assignmentId);
    }
}
