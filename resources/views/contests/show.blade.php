@extends('layout')

@section('content')
    <div class="container mt-4">
        <div class="border p-4">
            <h1 class="h5 mb-4">
                {{ $contest->info['title'] }}
            </h1>
            <h2 class="h6">
                開始時刻: {{ date('Y.m.d H:i', $contest->info['start_epoch_second']) }}
            </h2>
            <h2 class="h6 mb-4">
                コンテスト時間: {{ floor($contest->info['duration_second'] / 60) }} 分
            </h2>
            <p class="text-break mb-5">
                {!! $contest->info['memo'] !!}
            </p>
            <div class="card mb-4">
                <div class="card-body">
                    <a class="text-decoration-none"
                        href="{{ "https://kenkoooo.com/atcoder/#/contest/show/{$contest->contest_id}" }}">
                        コンテストへのリンク
                    </a></td>
                </div>
            </div>
            @if (!empty($contest->problems))
                <h2 class="h5">
                    問題一覧
                </h2>
                <table class="table table-striped mb-4">
                    <thead>
                        <tr>
                            <th>問題</th>
                            <th>URL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($contest->problems as $problem)
                            @php
                                $problem_id = $problem['id'];
                                $contest_id = explode('_', $problem_id)[0];
                            @endphp
                            <tr>
                                <td>{{ $problem_id }}</td>
                                <td><a class="text-decoration-none"
                                        href="{{ "https://atcoder.jp/contests/{$contest_id}/tasks/{$problem_id}" }}">
                                        問題URL
                                    </a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
            @if (!empty($contest->standings))
                <h2 class="h5">
                    順位表
                </h2>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>順位</th>
                            <th>ユーザー名</th>
                            <th>得点</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($contest->standings as $key => $standing)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td><a class="text-decoration-none"
                                        href="{{ route('users.show', ['user_id' => $standing['user_id']]) }}">
                                        {{ $standing['user_id'] }}
                                    </a></td>
                                <td>{{ $standing['points'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
            @auth
                @if (Auth::user()->isAdmin())
                    <div class="mb-4 text-end">
                        <form method="POST" action="{{ route('contests.update', ['contest' => $contest]) }}">
                            @csrf
                            @method('PUT')

                            <fieldset class="mb-4">
                                <div class="form-group" style="display: none">
                                    <label for="contest_id">
                                        Contest Id
                                    </label>
                                    <input id="contest_id" name="contest_id"
                                        class="form-control {{ $errors->has('contest_id') ? 'is-invalid' : '' }}"
                                        value="{{ old('contest_id') ?: $contest->contest_id }}" type="text">
                                    @if ($errors->has('contest_id'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('contest_id') }}
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-5">
                                    <button type="submit" class="btn btn-success">
                                        更新する
                                    </button>
                                </div>
                            </fieldset>
                        </form>
                        <form style="display: inline-block;" method="POST"
                            action="{{ route('contests.destroy', ['contest' => $contest]) }}">
                            @csrf
                            @method('DELETE')

                            <button class="btn btn-danger" onclick='return confirm("コンテストを削除しますか？")'>削除する</button>
                        </form>
                    </div>
                @endif
            @endauth
        </div>
    </div>
@endsection
