<?php

namespace App\Repositories;

use App\Models\Classroom;

class ClassroomRepository
{
    public function getAll()
    {
        return Classroom::all();
    }

    public function getById($id)
    {
        return Classroom::findOrFail($id);
    }

    public function create($data)
    {
        return Classroom::create($data);
    }

    public function update($id, $data)
    {
        $classroom = Classroom::findOrFail($id);
        $classroom->update($data);
        return $classroom;
    }

    public function delete($id)
    {
        $classroom = Classroom::findOrFail($id);
        return $classroom->delete();
    }
}
