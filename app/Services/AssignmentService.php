<?php

namespace App\Services;

use App\Repositories\AssignmentRepository;
use App\Repositories\UserRepository;

class AssignmentService
{
    protected $assignments;
    protected $users;

    public function __construct(AssignmentRepository $assignments, UserRepository $users)
    {
        $this->assignments = $assignments;
        $this->users = $users;
    }

    public function createAssignment(array $data)
    {
        return $this->assignments->create($data);
    }

    public function listByTeacher($teacherId)
    {
        return $this->assignments->allByTeacher($teacherId);
    }

    public function assignStudentByEmail($assignmentId, $studentEmail)
    {
        $student = $this->users->findByEmail($studentEmail);
        if (!$student || $student->role !== 'student') {
            throw new \Exception('Student not found');
        }
        return $this->assignments->assignStudent($assignmentId, $student->id);
    }

    public function getStats($assignmentId)
    {
        $assignment = $this->assignments->find($assignmentId);
        $totalAssigned = $assignment->students()->count();
        $completed = $assignment->attempts()->count();
        $averageScore = $assignment->attempts()->with('score')->get()
            ->pluck('score.score')
            ->average();
        return [
            'assigned' => $totalAssigned,
            'completed' => $completed,
            'average_score' => $averageScore,
        ];
    }

    public function getScoreDetails($assignmentId)
    {
        $assignment = $this->assignments->find($assignmentId);
        return $assignment->attempts()->with(['student', 'score', 'feedback'])->get();
    }
}
