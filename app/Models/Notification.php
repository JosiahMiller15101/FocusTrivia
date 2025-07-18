<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['user_id', 'type', 'message', 'read', 'comment_id', 'reaction_type', 'reacting_user_id', 'comment_reaction_id', 'replying_user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comment()
    {
        return $this->belongsTo(\App\Models\QuestionComment::class, 'comment_id');
    }
}
