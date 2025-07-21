<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileComment extends Model {
    protected $fillable = ['comment', 'user_id', 'author_id', 'parent_id'];
    public function author() { return $this->belongsTo(User::class, 'author_id'); }
    public function user() { return $this->belongsTo(User::class, 'user_id'); }

    public function replies()
    {
        return $this->hasMany(ProfileComment::class, 'parent_id');
    }
    public function parent()
    {
        return $this->belongsTo(ProfileComment::class, 'parent_id');
    }
}
