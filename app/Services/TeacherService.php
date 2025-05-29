<?php

namespace App\Services;

use App\Repositories\AssignmentRepository;
use App\Repositories\GameRepository;

class TeacherService
{
    protected $assignmentRepo;
    protected $gameRepo;

    public function __construct(
        AssignmentRepository $assignmentRepo,
        GameRepository $gameRepo
    ) {
        $this->assignmentRepo = $assignmentRepo;
        $this->gameRepo = $gameRepo;
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

    /**
     * Create a new game/quiz.
     */
    public function createGame($data)
    {
        return $this->gameRepo->create($data);
    }

    /**
     * Get all games/quizzes.
     */
    public function getAllGames()
    {
        return $this->gameRepo->getAll();
    }
}
