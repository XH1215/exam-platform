<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $fillable = ['name', 'teacher_id'];

    /**
     * The teacher (User) who owns this classroom.
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Assignments in this classroom.
     */
    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
}
