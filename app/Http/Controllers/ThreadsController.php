<?php

namespace App\Http\Controllers;

use App\Filters\ThreadFilters;
use App\Thread;
use App\Channel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Spam;

class ThreadsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    public function index(Channel $channel,ThreadFilters $filters)
    {
        $threads = $this->getThreads($channel, $filters);
        
        if (request()->wantsJson()) {
            return $threads;
        }
        
        return view('threads.index', [
            'threads' => $threads,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('threads.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Spam $spam)
    {
        $this->validate($request, [
            'title' => 'required|spamfree',
            'body' => 'required|spamfree',
            'channel_id' => 'required|exists:channels,id'
        ]);

        $spam->detect(request('body'));

        $thread = Thread::create([
            'user_id' => auth()->id(),
            'channel_id' => request('channel_id'),
            'title' => request('title'),
            'body' => request('body')
        ]);

        return redirect($thread->path())
            ->with('flash', 'Your Thread has been published');
    }

    /**
     * Display the specified resource.
     */
    public function show($channel, Thread $thread)
    {
        if(auth()->check()) {
            cache()->forever(auth()->user()->visitedThreadCacheKey($thread), Carbon::now());
        }

        return view('threads.show', compact('thread'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Thread $thread)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Thread $thread)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($channel, Thread $thread)
    {
        $this->authorize('update', $thread);
        
        $thread->delete();

        if(request()->wantsJson()){
            return response([], 204);
        }

        return redirect('threads');
    }

    protected function getThreads(Channel $channel, ThreadFilters $filters)
    {
        $threads = Thread::latest()->filter($filters);
        if ($channel->exists) {
            $threads->where('channel_id', $channel->id);
        }

        return $threads->paginate(25);
    }
}
