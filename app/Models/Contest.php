<?php

namespace App\Models;

use App\Http\Curl;
use App\Http\Requests\ContestRequest;
use Illuminate\Database\Eloquent\Model;

class Contest extends Model
{

    protected $fillable = [
        'contest_id',
        'info',
        'problems',
        'participants',
        'standings',
    ];

    protected $casts = [
        'info' => 'array',
        'problems' => 'array',
        'participants' => 'array',
        'standings' => 'array',
    ];

    public static function updateContest(ContestRequest $request): mixed
    {
        $params = $request->validated();

        $url = "https://kenkoooo.com/atcoder/internal-api/contest/get/" . $request->input("contest_id");
        $contents = Curl::file_get_contents_by_curl($url);
        if (!$contents) {
            return null;
        }
        $contents = mb_convert_encoding($contents, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
        $json = json_decode($contents, true);
        if (empty($json)) {
            return null;
        }
        $params['info'] = $json["info"];
        if (time() > $json["info"]["start_epoch_second"]) {
            $params['problems'] = $json["problems"];
        } else {
            $params['problems'] = [];
        }
        $params['participants'] = $json["participants"];
        if (time() > $json["info"]["start_epoch_second"] + $json["info"]["duration_second"] + 60 * 10) {
            $params['standings'] = self::calcStandings($json);
        } else {
            $params['standings'] = [];
        }
        return $params;
    }

    private static function compareResult($resultA, $resultB): int
    {
        if ($resultA["points"] === $resultB["points"]) {
            return $resultA["penalty"] - $resultB["penalty"];
        } else {
            return $resultB["points"] - $resultA["points"];
        }
    }

    public static function calcStandings($contest): array
    {
        $problems_id = [];
        foreach ($contest["problems"] as $problem) {
            $problems_id[] = $problem["id"];
        }
        $problems = implode(",", $problems_id);
        $users = implode(",", $contest["participants"]);
        $time_from = $contest["info"]["start_epoch_second"];
        $time_to = $contest["info"]["start_epoch_second"] + $contest["info"]["duration_second"];
        $url = "https://kenkoooo.com/atcoder/atcoder-api/v3/users_and_time?users={$users}&problems={$problems}&from={$time_from}&to={$time_to}";
        $contents = Curl::file_get_contents_by_curl($url);
        if (!$contents) {
            return [];
        }
        $contents = mb_convert_encoding($contents, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
        $json = json_decode($contents, true);

        $contest_result = [];
        foreach ($contest["participants"] as $user_id) {
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
            if (isset($contest_result[$user_id]["solved"][$submission["problem_id"]])) {
                continue;
            }
            if ($submission["result"] === "AC") {
                $contest_result[$user_id]["points"] += $submission["point"];
                $contest_result[$user_id]["penalty"] += $contest_result[$user_id]["penalty_per_problem"][$submission["problem_id"]] ?? 0;
                $contest_result[$user_id]["last_submission_time"] = $submission["epoch_second"];
                $contest_result[$user_id]["solved"][$submission["problem_id"]] = true;
            } else {
                $contest_result[$user_id]["penalty_per_problem"][$submission["problem_id"]] = ($contest_result[$user_id]["penalty_per_problem"][$submission["problem_id"]] ?? 0) + 1;
            }
        }

        $standings = [];
        foreach ($contest_result as $user_id => $result) {
            $standings[] = [
                "user_id" => $user_id,
                "points" => $result["points"],
                "penalty" => $result["penalty"] * $contest["info"]["penalty_second"] + $result["last_submission_time"] - $time_from,
            ];
        }
        usort($standings, [self::class, "compareResult"]);
        return $standings;
    }
}
