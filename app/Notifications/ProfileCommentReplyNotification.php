<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProfileCommentReplyNotification extends Notification
{
    use Queueable;

    protected $reply;
    protected $profileOwnerName;

    public function __construct($reply)
    {
        $this->reply = $reply;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'profile_comment_reply',
            'reply_id' => $this->reply->id,
            'comment_id' => $this->reply->parent_id,
            'author_id' => $this->reply->author_id,
            'message' => $this->reply->author->first_name . ' replied to your profile comment.',
        ];
    }
}
