<?php

namespace App\Http\Controllers;

use App\Favorite;
use App\Reply;
use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Reply $reply)
    {
        //best way
        $reply->favorite();

        return back();

        //1st way
        //$reply->favorites()->create([ 'user_id' => auth()->id ]);

        //2nd way
        /*Favorite::create('favorites')->insert([
            'user_id' => auth()->id(),
            'favorited_id' => $reply->id,
            'favoroted_type' => get_class($reply)
        ]);
        */
    }

    public function destroy(Reply $reply)
    {
        $reply->unfavorite();
    }
}
