<?php

namespace App\Services;

use App\Repositories\AttemptRepository;
use App\Repositories\QuestionRepository;

class AttemptService
{
    protected $repo;
    protected $questionRepo;

    public function __construct(AttemptRepository $repo, QuestionRepository $questionRepo)
    {
        $this->repo = $repo;
        $this->questionRepo = $questionRepo;
    }

    public function submitAnswers(int $studentId, int $assignmentId, array $answers)
    {
        $questions = $this->questionRepo->getByAssignment($assignmentId);
        $total = count($questions);
        $correct = 0;
        foreach ($questions as $q) {
            if (isset($answers[$q->id]) && $answers[$q->id] == $q->correct_answer) {
                $correct++;
            }
        }
        $score = $total > 0 ? round(($correct / $total) * 100) : 0;

        return $this->repo->create([
            'assignment_id' => $assignmentId,
            'student_id'    => $studentId,
            'answers'       => $answers,
            'score'         => $score,
        ]);
    }

    public function getStudentAttempts(int $studentId)
    {
        return $this->repo->getByStudent($studentId);
    }

    public function getAttemptDetail(int $id)
    {
        return $this->repo->find($id);
    }
}
