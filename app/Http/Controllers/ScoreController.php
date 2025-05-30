<?php

namespace App\Http\Controllers;

use App\Models\Score;

class ScoreController extends Controller
{
    public function getMyScores()
    {
        $scores = Score::with('game')
            ->where('student_id', auth()->id())
            ->get();

        return response()->json(['scores' => $scores]);
    }
}
