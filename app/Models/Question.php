<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\QuestionSubmission;

class Question extends Model
{
    protected $fillable = [
    'category',
    'type',
    'difficulty',
    'question',
    'correct_answer',
    'incorrect_answers',
    ];

    public function submissions()
{
    return $this->hasMany(QuestionSubmission::class);
}
}
