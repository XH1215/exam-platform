<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\ClassroomRepository;

class AdminService
{
    protected $userRepo;
    protected $classroomRepo;

    public function __construct(
        UserRepository $userRepo,
        ClassroomRepository $classroomRepo
    ) {
        $this->userRepo = $userRepo;
        $this->classroomRepo = $classroomRepo;
    }

    /**
     * Create a new user (teacher or student).
     */
    public function createUser($data)
    {
        return $this->userRepo->create($data);
    }

    /**
     * Get all users.
     */
    public function getAllUsers()
    {
        return $this->userRepo->getAll();
    }

    /**
     * Create a new classroom.
     */
    public function createClassroom($data)
    {
        return $this->classroomRepo->create($data);
    }

    /**
     * Get all classrooms.
     */
    public function getAllClassrooms()
    {
        return $this->classroomRepo->getAll();
    }
}
