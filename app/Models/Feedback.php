<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $fillable = [
        'assignment_id', 'user_id', 'comments', 'grade'
    ];

    /**
     * The assignment for which this feedback is given.
     */
    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    /**
     * The student (User) who received this feedback.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
