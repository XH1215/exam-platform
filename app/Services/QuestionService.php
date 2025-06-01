<?php

namespace App\Services;

use App\Repositories\QuestionRepository;

class QuestionService
{
    protected $repo;

    public function __construct(QuestionRepository $repo)
    {
        $this->repo = $repo;
    }

    public function addQuestion(array $data)
    {
        return $this->repo->create($data);
    }

    public function removeQuestion($id)
    {
        $question = $this->repo->find($id);
        return $this->repo->delete($question);
    }

    public function getQuestionsByAssignment(int $assignmentId)
    {
        return $this->repo->getByAssignment($assignmentId);
    }

    public function updateQuestion(int $id, array $data)
    {
        $question = $this->repo->find($id);
        return $this->repo->update($question, $data);
    }

    public function deleteQuestion(int $id)
    {
        $question = $this->repo->find($id);
        $this->repo->delete($question);
    }
}
