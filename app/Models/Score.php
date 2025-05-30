<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    protected $fillable = ['attempt_id', 'score'];
    protected $casts = [
        // Encrypt the score value in the database
        'score' => 'encrypted:double'
    ];

    public function attempt()
    {
        return $this->belongsTo(Attempt::class);
    }
}
