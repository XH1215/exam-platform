<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attempt extends Model
{
    protected $fillable = ['assignment_id', 'student_id', 'answers'];
    protected $casts = ['answers' => 'array'];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function score()
    {
        return $this->hasOne(Score::class);
    }

    public function feedback()
    {
        return $this->hasOne(Feedback::class);
    }
}
