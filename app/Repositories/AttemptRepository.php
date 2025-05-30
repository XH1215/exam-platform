<?php

namespace App\Repositories;

use App\Models\Attempt;

class AttemptRepository
{
    public function create(array $data)
    {
        return Attempt::create($data);
    }

    public function getByStudent(int $studentId)
    {
        return Attempt::where('student_id', $studentId)->get();
    }

    public function find(int $id)
    {
        return Attempt::findOrFail($id);
    }
}