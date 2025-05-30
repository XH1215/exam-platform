<?php

namespace App\Repositories;

use App\Models\Question;

class QuestionRepository
{
    /**
     * Create a new question
     *
     * @param array $data
     * @return Question
     */
    public function create(array $data)
    {
        return Question::create($data);
    }

    /**
     * Update an existing question
     *
     * @param Question $question
     * @param array $data
     * @return Question
     */
    public function update(Question $question, array $data)
    {
        $question->update($data);
        return $question;
    }

    /**
     * Delete a question
     *
     * @param Question $question
     * @return bool|null
     */
    public function delete(Question $question)
    {
        return $question->delete();
    }

    /**
     * Get all questions for a specific assignment
     *
     * @param int $assignmentId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByAssignmentId(int $assignmentId)
    {
        return Question::where('assignment_id', $assignmentId)->get();
    }

    /**
     * Find question by ID
     *
     * @param int $id
     * @return Question
     */
    public function find(int $id)
    {
        return Question::findOrFail($id);
    }

    public function getByAssignment(int $assignmentId)
    {
        return Question::where('assignment_id', $assignmentId)->get();
    }
}
