<?php

namespace App\Repositories;

use App\Models\Feedback;

class FeedbackRepository
{
    public function getAll()
    {
        return Feedback::all();
    }

    public function getById($id)
    {
        return Feedback::findOrFail($id);
    }

    public function create($data)
    {
        return Feedback::create($data);
    }

    public function update($id, $data)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->update($data);
        return $feedback;
    }

    public function delete($id)
    {
        $feedback = Feedback::findOrFail($id);
        return $feedback->delete();
    }
}
