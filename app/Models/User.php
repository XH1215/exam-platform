<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role'];
    protected $hidden = ['password'];

    public function assignments()
    {
        return $this->belongsToMany(Assignment::class, 'assignment_student', 'student_id', 'assignment_id');
    }

    public function attempts()
    {
        return $this->hasMany(Attempt::class, 'student_id');
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class, 'teacher_id');
    }
}
