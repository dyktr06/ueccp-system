<?php

namespace App\Http\Controllers;

use App\Services\SeasonRankingService;
use Illuminate\Support\Facades\DB;

class RankingController extends Controller
{
    private $seasonRankingService;
    public function __construct(SeasonRankingService $seasonRankingService)
    {
        $this->seasonRankingService = $seasonRankingService;
    }

    public function show()
    {
        $contests = DB::table('contests')
            ->orderBy('info->start_epoch_second', 'desc')
            ->take(5)
            ->get();

        return view('ranking.show', [
            'season_ranking' => $this->seasonRankingService->getSeasonRanking($contests),
        ]);
    }
}
