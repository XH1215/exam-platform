<?php

namespace App\Services;

use App\Repositories\QuestionRepository;

class QuestionService
{
    protected $questions;

    public function __construct(QuestionRepository $questions)
    {
        $this->questions = $questions;
    }

    public function createQuestion(array $data)
    {
        return $this->questions->create($data);
    }

    public function updateQuestion($id, array $data)
    {
        $question = $this->questions->find($id);
        return $this->questions->update($question, $data);
    }

    public function deleteQuestion($id)
    {
        $question = $this->questions->find($id);
        return $this->questions->delete($question);
    }
}
