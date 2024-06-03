<?php

namespace App\Services;

class SeasonRankingService
{
    public function getSeasonPoints(int $rank): int
    {
        if ($rank <= 5) {
            return 10 - ($rank - 1);
        }
        return 1;
    }

    public function getSeasonRanking($contests): array
    {

        $contest_results = [];
        foreach ($contests as $contest) {
            $standings = json_decode($contest->standings, true);
            foreach ($standings as $rank => $standing) {
                if (!isset($contest_results[$standing["user_id"]])) {
                    $contest_results[$standing["user_id"]] = 0;
                }
                $contest_results[$standing["user_id"]] += $this->getSeasonPoints($rank + 1);
            }
        }
        arsort($contest_results);

        $season_ranking = [];
        $rank = 1;
        $prev_points = -1;
        foreach ($contest_results as $user_id => $points) {
            if ($prev_points !== $points) {
                $rank = count($season_ranking) + 1;
            }
            $season_ranking[] = [
                "rank" => $rank,
                "user_id" => $user_id,
                "points" => $points,
            ];
            $prev_points = $points;
        }
        return $season_ranking;
    }
}
