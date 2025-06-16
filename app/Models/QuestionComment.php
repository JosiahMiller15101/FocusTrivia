<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionComment extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function reactions()
    {
        return $this->hasMany(CommentReaction::class, 'comment_id');
    }
}
