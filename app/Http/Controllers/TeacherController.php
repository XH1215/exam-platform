<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TeacherService;

class TeacherController extends Controller
{
    protected $teacherService;

    /**
     * Teacher-specific operations. Protected by 'role:teacher' middleware.
     */
    public function __construct(TeacherService $teacherService)
    {
        $this->teacherService = $teacherService;
        $this->middleware('auth:api');
    }

    /**
     * List all assignments created by the teacher (or all).
     */
    public function indexAssignments()
    {
        $assignments = $this->teacherService->getAllAssignments();
        return $this->successResponse($assignments, 'Assignments retrieved.');
    }

    /**
     * Create a new assignment.
     */
    public function createAssignment(Request $request)
    {
        $data = $request->only('title', 'description', 'due_date', 'classroom_id');
        $assignment = $this->teacherService->createAssignment($data);
        return $this->successResponse($assignment, 'Assignment created.');
    }

    /**
     * List all games/quizzes created by the teacher.
     */
    public function indexGames()
    {
        $games = $this->teacherService->getAllGames();
        return $this->successResponse($games, 'Games retrieved.');
    }

    /**
     * Create a new game/quiz.
     */
    public function createGame(Request $request)
    {
        $data = $request->only('title', 'description');
        $game = $this->teacherService->createGame($data);
        return $this->successResponse($game, 'Game/Quiz created.');
    }
}
