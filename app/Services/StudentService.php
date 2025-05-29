<?php

namespace App\Services;

use App\Repositories\AssignmentRepository;
use App\Repositories\FeedbackRepository;

class StudentService
{
    protected $assignmentRepo;
    protected $feedbackRepo;

    public function __construct(
        AssignmentRepository $assignmentRepo,
        FeedbackRepository $feedbackRepo
    ) {
        $this->assignmentRepo = $assignmentRepo;
        $this->feedbackRepo = $feedbackRepo;
    }

    /**
     * Get assignments available to the student.
     */
    public function getAssignmentsForStudent()
    {
        // In a real app, filter assignments by the student's classes or ID
        return $this->assignmentRepo->getAll();
    }

    /**
     * Submit an assignment (create feedback).
     */
    public function submitAssignment($data)
    {
        return $this->feedbackRepo->create($data);
    }

    /**
     * Get the student's profile.
     */
    public function getProfile()
    {
        return auth()->user();
    }

    /**
     * Submit a quiz and return a score (stubbed implementation).
     */
    public function submitQuiz($data)
    {
        // Stub: calculate or retrieve quiz score
        $score = rand(0, 100);
        return ['score' => $score];
    }
}
