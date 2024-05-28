<?php

namespace App\Http\Controllers;

use App\Models\Contest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ContestsController extends Controller
{
    private function file_get_contents_by_curl(string $url)
    {
        $curl_p = curl_init();
        curl_setopt($curl_p, CURLOPT_URL, $url);
        curl_setopt($curl_p, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_p, CURLOPT_ENCODING, "gzip");
        $data = curl_exec($curl_p);
        curl_close($curl_p);
        return $data;
    }

    private function isAccessible()
    {
        if (Auth::guest()) {
            return false;
        }
        $user = Auth::user();
        if ($user instanceof User) {
            return $user->isAdmin();
        }
        return false;
    }

    public function index()
    {
        $contests = Contest::orderBy('created_at', 'desc')->paginate(10);

        return view('contests.index', ['contests' => $contests]);
    }

    public function create()
    {
        if (!$this->isAccessible()) {
            abort(404);
        }
        return view('contests.create');
    }

    public function store(Request $request)
    {
        $params = $request->validate([
            'contest_id' => 'required|max:50',
        ]);

        $url = "https://kenkoooo.com/atcoder/internal-api/contest/get/" . $request->input("contest_id");
        $contents = $this->file_get_contents_by_curl($url);
        if (!$contents) {
            return view('contests.index');
        }
        $contents = mb_convert_encoding($contents, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
        $json = json_decode($contents, true);

        $params['info'] = $json["info"];
        $params['problems'] = $json["problems"];
        $params['participants'] = $json["participants"];
        $params['standings'] = [];

        Contest::create($params);

        return redirect()->route('top');
    }

    public function show($id)
    {
        $contest = Contest::findOrFail($id);

        return view('contests.show', [
            'contest' => $contest,
        ]);
    }

    function compareResult($resultA, $resultB){
        if($resultA["points"] === $resultB["points"]){
            return $resultA["penalty"] - $resultB["penalty"];
        }else{
            return $resultB["points"] - $resultA["points"];
        }
    }

    public function calc_standings($contest){
        $problems_id = [];
        foreach($contest["problems"] as $problem){
            $problems_id[] = $problem["id"];
        }
        $problems = implode(",", $problems_id);
        $users = implode(",", $contest["participants"]);
        $time_from = $contest["info"]["start_epoch_second"];
        $time_to = $contest["info"]["start_epoch_second"] + $contest["info"]["duration_second"];
        $url = "https://kenkoooo.com/atcoder/atcoder-api/v3/users_and_time?users={$users}&problems={$problems}&from={$time_from}&to={$time_to}";
        $contents = $this->file_get_contents_by_curl($url);
        if (!$contents) {
            return [];
        }
        $contents = mb_convert_encoding($contents, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
        $json = json_decode($contents, true);

        $contest_result = [];
        foreach($contest["participants"] as $user_id){
            $contest_result[$user_id] = [
                "points" => 0,
                "penalty" => 0,
                "last_submission_time" => $time_from,
                "solved" => [],
                "penalty_per_problem" => [],
            ];
        }

        foreach ($json as $submission) {
            $user_id = $submission["user_id"];
            if(isset($contest_result[$user_id]["solved"][$submission["problem_id"]])){
                continue;
            }
            if($submission["result"] === "AC"){
                $contest_result[$user_id]["points"] += $submission["point"];
                $contest_result[$user_id]["penalty"] += $contest_result[$user_id]["penalty_per_problem"][$submission["problem_id"]] ?? 0;
                $contest_result[$user_id]["last_submission_time"] = $submission["epoch_second"];
                $contest_result[$user_id]["solved"][$submission["problem_id"]] = true;
            }else{
                $contest_result[$user_id]["penalty_per_problem"][$submission["problem_id"]] = ($contest_result[$user_id]["penalty_per_problem"][$submission["problem_id"]] ?? 0) + 1;
            }
        }

        $standings = [];
        foreach($contest_result as $user_id => $result){
            $standings[] = [
                "user_id" => $user_id,
                "points" => $result["points"],
                "penalty" => $result["penalty"] * $contest["info"]["penalty_second"] + $result["last_submission_time"] - $time_from,
            ];
        }
        usort($standings, array($this, "compareResult"));
        return $standings;
    }

    public function update($id, Request $request)
    {
        $params = $request->validate([
            'contest_id' => 'required|max:50',
        ]);

        $contest = Contest::findOrFail($id);
        if (!$this->isAccessible()) {
            abort(404);
        }

        $url = "https://kenkoooo.com/atcoder/internal-api/contest/get/" . $request->input("contest_id");
        $contents = $this->file_get_contents_by_curl($url);
        if (!$contents) {
            return view('contests.index');
        }
        $contents = mb_convert_encoding($contents, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
        $json = json_decode($contents, true);

        $params['info'] = $json["info"];
        $params['problems'] = $json["problems"];
        $params['participants'] = $json["participants"];
        $params['standings'] = $this->calc_standings($json);

        $contest->fill($params)->save();

        return redirect()->route('contests.show', ['contest' => $contest]);
    }

    public function destroy($id)
    {
        $contest = Contest::findOrFail($id);
        if (!$this->isAccessible()) {
            abort(404);
        }

        return redirect()->route('top');
    }
}
