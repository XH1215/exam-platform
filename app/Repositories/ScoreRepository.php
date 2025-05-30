<?php

namespace App\Repositories;

use App\Models\Score;

class ScoreRepository
{
    protected $model;

    public function __construct(Score $model)
    {
        $this->model = $model;
    }

    /**
     * Persist a new score record.
     *
     * @param array $data
     * @return Score
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Find an existing submission by student and assignment.
     */
    public function findByStudentAndAssignment(int $studentId, int $assignmentId)
    {
        return $this->model
            ->where('student_id', $studentId)
            ->where('assignment_id', $assignmentId)
            ->first();
    }

    public function findByStudent(int $studentId)
    {
        return $this->model->where('student_id', $studentId)->get();
    }

}