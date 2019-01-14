<?php

namespace App\Http\Controllers;

use App\Thread;
use App\Reply;
use App\User;
use App\Events\ThreadReceivedNewReply;
use App\Http\Requests\CreatePostForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Notifications\YouWereMentioned;

class RepliesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => 'index']);
    }

    public function index($channelId, Thread $thread)
    {
        return $thread->replies()->paginate(10);
    }

    public function store($channelId, Thread $thread, CreatePostForm $form)
    {
        $reply = $thread->addReply([
            'body' => request('body'),
            'user_id' => auth()->id()
        ]);

        return $reply->load('owner');
    }

    public function update(Reply $reply)
    {
        $this->authorize('update', $reply);

        $this->validate(request(), ['body' => 'required|spamfree']);
            
        $reply->update(['body' => request('body')]);
    }

    public function destroy(Reply $reply)
    {
        $this->authorize('update', $reply);
        $reply->delete();

        if(request()->expectsJson())
        {
            return response(['status' => 'Reply Deleted']);
        }

        return back();
    }
}