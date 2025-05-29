<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\Question;

class QuizService
{
    public function createQuizWithQuestions(array $data)
    {
        $quiz = Quiz::create([
            'title' => $data['title'],
            'quiz_number' => $data['quizNumber'],
        ]);

        foreach ($data['questions'] as $q) {
            Question::create([
                'quiz_id' => $quiz->id,
                'question' => $q['question'],
                'answer' => $q['answer'],
                'options' => $q['option'],
            ]);
        }

        return $quiz->load('questions');
    }
}
