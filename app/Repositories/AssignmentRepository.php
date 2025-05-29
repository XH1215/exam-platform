<?php

namespace App\Repositories;

use App\Models\Assignment;

class AssignmentRepository
{
    public function getAll()
    {
        return Assignment::all();
    }

    public function getById($id)
    {
        return Assignment::findOrFail($id);
    }

    public function create($data)
    {
        return Assignment::create($data);
    }

    public function update($id, $data)
    {
        $assignment = Assignment::findOrFail($id);
        $assignment->update($data);
        return $assignment;
    }

    public function delete($id)
    {
        $assignment = Assignment::findOrFail($id);
        return $assignment->delete();
    }
}
