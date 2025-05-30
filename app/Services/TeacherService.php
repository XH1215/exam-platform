<?php

namespace App\Services;

use App\Repositories\AssignmentRepository;

class TeacherService
{
    protected $assignmentRepo;

    public function __construct(AssignmentRepository $assignmentRepo)
    {
        $this->assignmentRepo = $assignmentRepo;
    }

    /**
     * Create a new assignment.
     */
    public function createAssignment($data)
    {
        return $this->assignmentRepo->create($data);
    }

    /**
     * Get all assignments (optionally filter by teacher).
     */
    public function getAllAssignments()
    {
        return $this->assignmentRepo->getAll();
    }
}
