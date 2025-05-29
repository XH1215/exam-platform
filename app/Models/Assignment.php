<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = ['title', 'description', 'due_date', 'classroom_id', 'teacher_id'];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function scores()
    {
        return $this->hasMany(Score::class);
    }
}