<?php

namespace App\Services;

use App\Http\Curl;
use App\Http\Requests\ContestRequest;

class UpdateContestService
{
    public function updateContest(ContestRequest $request): mixed
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
            $params['standings'] = ContestStandingService::calcStandings($json);
        } else {
            $params['standings'] = [];
        }
        return $params;
    }
}
