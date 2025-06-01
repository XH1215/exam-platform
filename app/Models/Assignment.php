<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'due_date', 'teacher_id'];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'assignment_student', 'assignment_id', 'student_id')
                    ->withTimestamps();
    }

    public function attempts()
    {
        return $this->hasMany(Attempt::class);
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }
}