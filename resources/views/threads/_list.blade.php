@forelse($threads as $thread)
            <div class="card">
                <div class="card-header">
                    <div class="level">
                        <div class="flex">
                            <h4 class="flex">
                                <a href="{{ $thread->path() }}">
                                    @if(auth()->check() && $thread->hasUpdatesFor(auth()->user()))
                                        <strong>{{ $thread->title }}</strong>
                                    @else
                                        {{ $thread->title }}
                                    @endif
                                </a>
                            </h4>

                            <p>Posted by: <a href="/profiles/{{ $thread->creator->name }}">{{ $thread->creator->name }}</a></p>
                        </div>
                        <a href="{{ $thread->path() }}">
                            <strong>{{ $thread->replies_count }} {{ str_plural('reply', $thread->replies_count) }}</strong>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="body"> 
                        {{ $thread->body }} 
                    </div>
                </div>
            </div>
            <br>
            @empty
                <p>There are no relevant results at this time.</p>   
            @endforelse