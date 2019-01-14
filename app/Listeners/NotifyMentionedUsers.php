<?php

namespace App\Listeners;

use App\Events\ThreadReceivedNewReply;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;
use App\Notifications\YouWereMentioned;

class NotifyMentionedUsers
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ThreadReceivedNewRepl  $event
     * @return void
     */
    public function handle(ThreadReceivedNewReply $event)
    {
        preg_match_all('/\@([^\s\.]+)/', $event->reply->body, $matches);
        
        $names = $matches[1];

        foreach($names as $name) {
            $user = User::whereName($name)->first();

            if($user) {
                $user->notify(new YouWereMentioned($event->reply));
            }
        }
    }
}
