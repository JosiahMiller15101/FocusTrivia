<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionSubmission extends Model
{
    protected $guarded = [];
    // question_code is a persistent identifier for the question, used to preserve stats across reseeds
}
