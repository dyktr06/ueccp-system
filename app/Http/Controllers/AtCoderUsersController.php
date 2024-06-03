<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class AtCoderUsersController extends Controller
{
    public function show($user_id)
    {
        $contests = DB::table('contests')->orderBy('created_at', 'desc')->where('participants', 'like', '%' . $user_id . '%');
        $dates = [];
        $ranks = [];
        $contest_titles = [];
        foreach ($contests->get() as $contest) {
            $info = json_decode($contest->info, true);
            $standings = json_decode($contest->standings, true);
            foreach($standings as $rank => $standing) {
                if ($standing["user_id"] === $user_id) {
                    $dates[] = date('Y-m-d H:i', $info['start_epoch_second'] + $info['duration_second']);
                    $ranks[] = $rank + 1;
                    $contest_titles[] = $info["title"];
                    break;
                }
            }
        }

        if(empty($contest_titles)) {
            abort(404);
        }

        return view('users.show', [
            'dates' => $dates,
            'ranks' => $ranks,
            'contest_titles' => $contest_titles,
            'user_id' => $user_id,
        ]);
    }
}
