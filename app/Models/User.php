<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = [
        'name', 'email', 'password', 'role'
    ];

    protected $hidden = ['password'];

    /**
     * If this user is a teacher, the assignments they created.
     */
    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'teacher_id');
    }

    /**
     * The feedback entries this user (student) has received.
     */
    public function feedbacks()
    {
        return $this->hasMany(Feedback::class, 'user_id');
    }
}
