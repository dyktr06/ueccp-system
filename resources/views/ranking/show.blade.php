@extends('layout')

@section('content')
    <div class="container mt-4">
        <div class="card mb-4">
            <div class="card-header">
                シーズンランキング
            </div>
            <div class="card-body">
                <p class="card-text">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>順位</th>
                                <th>ユーザー名</th>
                                <th>得点</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($season_ranking as $standing)
                                <tr>
                                    <td>{{ $standing['rank'] }}</td>
                                    <td><a class="text-decoration-none"
                                            href="{{ route('users.show', ['user_id' => $standing['user_id']]) }}">
                                            {{ $standing['user_id'] }}
                                        </a></td>
                                    <td>{{ $standing['points'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </p>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header">
                シーズンランキングとは？
            </div>
            <div class="card-body">
                <p class="card-text">
                    直近 5 回のコンテストの結果をもとに、シーズンランキングを算出しています。
                    <br>
                    計算方法は以下のようになっています。
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>順位</th>
                                <th>得られる点数</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>10</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>8</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>6</td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>4</td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td>2</td>
                            </tr>
                            <tr>
                                <td>6-</td>
                                <td>1</td>
                            </tr>
                        </tbody>
                    </table>
                </p>
            </div>
        </div>
    </div>
@endsection
