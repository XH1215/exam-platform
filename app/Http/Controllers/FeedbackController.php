<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback;
use App\Services\FeedbackService;

class FeedbackController extends Controller
{
    protected $feedbackService;

    public function __construct(FeedbackService $feedbackService)
    {
        $this->feedbackService = $feedbackService;
    }

     public function store(Request $request)
    {
        $request->validate([
            'game_id' => 'required|exists:games,id',
            'comment' => 'required|string|max:1000',
        ]);

        $feedback = $this->feedbackService->create(auth()->id(), $request->game_id, $request->comment);

        return response()->json(['message' => 'Feedback submitted', 'feedback' => $feedback], 201);
    }
}
