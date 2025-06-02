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
    public function createAssignment($data)
    {
        return $this->assignmentRepo->create($data);
    }
    public function getAllAssignments()
    {
        return $this->assignmentRepo->getAll();
    }
}
