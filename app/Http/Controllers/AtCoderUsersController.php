<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class AtCoderUsersController extends Controller
{
    public function show($user_id)
    {
        $contests = DB::table('contests')->orderBy('created_at', 'desc')->where('participants', 'like', '%' . $user_id . '%');
        $contest_results = [];

        foreach ($contests->get() as $contest) {
            $info = json_decode($contest->info, true);
            $standings = json_decode($contest->standings, true);
            foreach($standings as $rank => $standing) {
                if ($standing["user_id"] === $user_id) {
                    $contest_results[] = [
                        "contest_title" => $info["title"],
                        "user_rank" => $rank + 1,
                    ];
                    break;
                }
            }
        }

        if(empty($contest_results)) {
            abort(404);
        }

        return view('users.show', [
            'user_id' => $user_id,
            'contest_results' => $contest_results,
        ]);
    }
}
