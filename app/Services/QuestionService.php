<?php

namespace App\Services;

use App\Repositories\QuestionRepository;
use Illuminate\Support\Facades\DB;
use Exception;

class QuestionService
{
    protected $repo;

    public function __construct(QuestionRepository $repo)
    {
        $this->repo = $repo;
    }

    public function addQuestions(array $questions)
    {
        DB::beginTransaction();
        try {
            foreach ($questions as &$question) {
                if (isset($question['options']) && is_array($question['options'])) {
                    $question['options'] = json_encode($question['options']);
                }
            }

            $this->repo->batchStore($questions);
            DB::commit();

            return [
                'data' => $questions
            ];
        } catch (Exception $e) {
            DB::rollBack();
            return [
                'errors' => range(0, count($questions) - 1),
                'message' => $e->getMessage(),
            ];
        }
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
