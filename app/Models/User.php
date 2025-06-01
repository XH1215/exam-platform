<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
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

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

}
