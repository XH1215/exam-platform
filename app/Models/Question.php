<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['assignment_id', 'question', 'answer', 'options'];

    protected $casts = [
        'options' => 'array',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }
}