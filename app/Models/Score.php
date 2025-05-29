<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    protected $fillable = ['student_id', 'game_id', 'score'];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
