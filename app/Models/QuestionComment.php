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

     public function parent()
    {
        return $this->belongsTo(QuestionComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(QuestionComment::class, 'parent_id')->with('user', 'reactions', 'replies');
    }
}
