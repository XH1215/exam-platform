<?php

namespace App\Repositories;

use App\Models\Question;

class QuestionRepository
{
    public function create(array $data)
    {
        return Question::create($data);
    }

    public function update(Question $question, array $data)
    {
        $question->update($data);
        return $question;
    }

    public function delete(Question $question)
    {
        return $question->delete();
    }

    public function getByAssignmentId(int $assignmentId)
    {
        return Question::where('assignment_id', $assignmentId)->get();
    }

    public function find(int $id)
    {
        return Question::findOrFail($id);
    }

    public function getByAssignment(int $assignmentId)
    {
        return Question::where('assignment_id', $assignmentId)->get();
    }

    public function batchStore(array $questions)
    {
        return Question::insert($questions);
    }
}
