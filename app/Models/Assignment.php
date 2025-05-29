<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = [
        'title', 'description', 'due_date', 'classroom_id', 'teacher_id'
    ];

    /**
     * The classroom this assignment belongs to.
     */
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * The teacher (User) who created the assignment.
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Feedback entries for this assignment.
     */
    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }
}
