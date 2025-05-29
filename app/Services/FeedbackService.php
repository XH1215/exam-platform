<?php

namespace App\Services;

use App\Models\Feedback;

class FeedbackService
{
    public function create(int $studentId, int $gameId, string $comment): Feedback
    {
        return Feedback::create([
            'student_id' => $studentId,
            'game_id' => $gameId,
            'comment' => $comment,
        ]);
    }
}
