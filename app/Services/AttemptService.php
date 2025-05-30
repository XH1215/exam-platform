<?php

namespace App\Services;

use App\Repositories\AttemptRepository;
use App\Repositories\ScoreRepository;
use App\Repositories\AssignmentRepository;

class AttemptService
{
    protected $attempts;
    protected $scores;
    protected $assignments;

    public function __construct(
        AttemptRepository $attempts,
        ScoreRepository $scores,
        AssignmentRepository $assignments
    ) {
        $this->attempts = $attempts;
        $this->scores = $scores;
        $this->assignments = $assignments;
    }

    public function submitAnswers($studentId, $assignmentId, array $answers)
    {
        $assignment = $this->assignments->find($assignmentId);
        $questions = $assignment->questions;
        $correct = 0;
        foreach ($questions as $question) {
            if (isset($answers[$question->id]) && $answers[$question->id] == $question->correct_answer) {
                $correct++;
            }
        }
        $scoreValue = count($questions) > 0 ? round(($correct / count($questions)) * 100, 2) : 0;

        // Create attempt
        $attempt = $this->attempts->create([
            'assignment_id' => $assignmentId,
            'student_id' => $studentId,
            'answers' => $answers,
        ]);

        // Create score record
        $this->scores->create([
            'attempt_id' => $attempt->id,
            'score' => $scoreValue,
        ]);

        return $attempt;
    }

    public function getStudentAttempts($studentId)
    {
        return $this->attempts->allByStudent($studentId);
    }
}
