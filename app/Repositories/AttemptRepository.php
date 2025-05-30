<?php

namespace App\Repositories;

use App\Models\Attempt;

class AttemptRepository
{
    public function create(array $data)
    {
        return Attempt::create($data);
    }

    public function find($id)
    {
        return Attempt::findOrFail($id);
    }

    public function allByStudent($studentId)
    {
        return Attempt::where('student_id', $studentId)->orderBy('created_at', 'desc')->get();
    }
}
