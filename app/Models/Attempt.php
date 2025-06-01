<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attempt extends Model
{
    protected $primaryKey = 'attempt_id';

    protected $fillable = [
        'assignment_id',
        'student_id',
        'answer_record',
        'encrypted_score',
    ];

    protected $casts = [
        'answer_record' => 'array',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
