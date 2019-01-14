<?php

namespace App;
use App\Filters\ThreadFilters;
use App\Notifications\ThreadWasUpdated;
use App\Events\ThreadReceivedNewReply;

use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use RecordsActivity;

    protected $guarded = [];

    protected $fillable = [
        'user_id', 'channel_id', 'title', 'body',
    ];

    protected $with = ['creator', 'channel'];

    protected $appends = ['isSubscribedTo'];
    
    public static function boot()
    {
        parent::boot();

        /*static::addGlobalScope('replyCount', function ($builder){
            $builder->withCount('replies');
        });*/

        static::deleting(function ($thread){
            $thread->replies->each->delete();
        });
    }

    public function path()
    {
        return "/threads/{$this->channel->slug}/{$this->id}";
        //return '/threads/' . $this->channel->slug . '/' . $this->id;
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function addReply($reply)
    {
        $reply = $this->replies()->create($reply);

        event(new ThreadReceivedNewReply($reply));

        foreach($this->subscriptions as $subscription) {
            if($subscription->user_id != $reply->user_id) {
                $subscription->user->notify(new ThreadWasUpdated($this, $reply));
            }
        }

        return $reply;
    }

    public function scopeFilter($query, ThreadFilters $filters)
    {
        return $filters->apply($query);
    }

    public function subscribe($userId =  null)
    {
        $this->subscriptions()->create([
            'user_id' => $userId ?: auth()->id(),
        ]);
    }

    public function unsubscribe($userId = null)
    {
        $this->subscriptions()
            ->where('user_id', $userId ?: auth()->id())
            ->delete();
    }

    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }

    public function getIsSubscribedToAttribute()
    {
        return $this->subscriptions()
                ->where('user_id', auth()->id())
                ->exists();
    }

    public function hasUpdatesFor($user = null)
    {
        $user = $user ?: auth()->user();
        return $this->updated_at > cache($user->visitedThreadCacheKey($this));
    }
}