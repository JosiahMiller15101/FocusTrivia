<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\QuestionSubmission;
use App\Models\Notification;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function submissions()
{
    return $this->hasMany(QuestionSubmission::class);
}

    public function getProfileImageAttribute($value)
    {
        if ($value) {
            return $value;
        }
        // Return a default image URL (public/images/p.png)
        return asset('images/p.png');
    }

    public function commentReactions()
    {
        return $this->hasMany(CommentReaction::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

}
