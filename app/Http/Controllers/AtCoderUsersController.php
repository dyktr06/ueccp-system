<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class AtCoderUsersController extends Controller
{
    public function show($user_id)
    {
        $contests = DB::table('contests')
            ->select('id', 'info', 'standings')
            ->orderBy('created_at', 'desc')
            ->where('participants', 'like', '%' . $user_id . '%')
            ->get();

        $contest_dates = [];
        $contest_ranks = [];
        $contest_titles = [];
        $contest_ids = [];

        foreach ($contests as $contest) {
            $info = json_decode($contest->info, true);
            $standings = json_decode($contest->standings, true);

            foreach ($standings as $rank => $standing) {
                if ($standing['user_id'] === $user_id) {
                    $contest_dates[] = date('Y-m-d H:i', $info['start_epoch_second'] + $info['duration_second']);
                    $contest_ranks[] = $rank + 1;
                    $contest_titles[] = $info['title'];
                    $contest_ids[] = $contest->id;
                    break;
                }
            }
        }

        abort_if(empty($contest_titles), 404);

        return view('users.show', [
            'contest_dates' => $contest_dates,
            'contest_ranks' => $contest_ranks,
            'contest_titles' => $contest_titles,
            'contest_ids' => $contest_ids,
            'user_id' => $user_id,
        ]);
    }
}
