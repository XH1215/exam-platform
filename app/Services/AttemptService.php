<?php

namespace App\Services;

use App\Repositories\AttemptRepository;
use App\Repositories\QuestionRepository;
use Illuminate\Support\Facades\Crypt;

class AttemptService
{
    protected $repo;
    protected $questionRepo;

    public function __construct(AttemptRepository $repo, QuestionRepository $questionRepo)
    {
        $this->repo = $repo;
        $this->questionRepo = $questionRepo;
    }

    public function submitAnswers(int $studentId, int $assignmentId, array $answers): array
    {
        $questions = $this->questionRepo->getByAssignment($assignmentId);
        $total = count($questions);
        $correct = 0;

        foreach ($questions as $q) {
            if (isset($answers[$q->id])) {
                $studentAnswer = explode(';', $answers[$q->id]);
                $correctAnswer = explode(';', $q->correct_answer);
                sort($studentAnswer);
                sort($correctAnswer);

                if ($studentAnswer === $correctAnswer) {
                    $correct++;
                }
            }
        }

        $score = $total > 0 ? round(($correct / $total) * 100) : 0;
        $encryptedScore = Crypt::encryptString((string)$score);

        $attempt = $this->repo->create([
            'student_id' => $studentId,
            'assignment_id' => $assignmentId,
            'answer_record' => $answers,
            'encrypted_score' => $encryptedScore,
        ]);

        return [
            'attempt_id' => $attempt->attempt_id,
            'assignment_id' => $assignmentId,
            'submitted_at' => $attempt->created_at,
            'score' => $score,
        ];
    }

    public function getStudentAttempts(int $studentId)
    {
        $attempts = $this->repo->getByStudent($studentId);

        return $attempts->map(function ($attempt) {
            return [
                'attempt_id' => $attempt->attempt_id,
                'assignment_id' => $attempt->assignment_id,
                'submitted_at' => $attempt->created_at,
                'score' => Crypt::decryptString($attempt->encrypted_score),
            ];
        });
    }

    public function getAttemptDetail(int $attemptId)
    {
        $attempt = $this->repo->find($attemptId);
        $questions = $this->questionRepo->getByAssignment($attempt->assignment_id);

        $details = [];

        foreach ($questions as $q) {
            $details[] = [
                'question_id' => $q->id,
                'question_text' => $q->question_text,
                'options' => $q->options,
                'correct_answer' => $q->correct_answer,
                'student_answer' => $attempt->answer_record[$q->id] ?? null,
            ];
        }

        return [
            'attempt_id' => $attempt->attempt_id,
            'assignment_id' => $attempt->assignment_id,
            'student_id' => $attempt->student_id,
            'submitted_at' => $attempt->created_at,
            'score' => Crypt::decryptString($attempt->encrypted_score),
            'details' => $details,
        ];
    }
}
