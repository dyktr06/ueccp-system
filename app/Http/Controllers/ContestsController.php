<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContestRequest;
use App\Models\Contest;
use App\Models\User;
use App\Services\UpdateContestService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContestsController extends Controller
{
    private $updateContestService;
    public function __construct()
    {
        $this->updateContestService = new UpdateContestService();
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
        $contests = Contest::orderBy('info->start_epoch_second', 'desc')->paginate(10);

        return view('contests.index', ['contests' => $contests]);
    }

    public function create()
    {
        if (!$this->isAccessible()) {
            abort(404);
        }
        return view('contests.create');
    }

    public function store(ContestRequest $request)
    {
        $params = $this->updateContestService->updateContest($request);
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

    public function update($id, ContestRequest $request)
    {
        $params = $this->updateContestService->updateContest($request);
        if (!$params) {
            abort(500);
        }
        $contest = Contest::findOrFail($id);
        $contest->fill($params)->save();

        return redirect()->route('contests.show', ['contest' => $contest]);
    }

    public function destroy($id)
    {
        if (!$this->isAccessible()) {
            abort(404);
        }

        $contest = Contest::findOrFail($id);
        DB::transaction(function () use ($contest) {
            $contest->delete();
        });

        return redirect()->route('top');
    }
}
