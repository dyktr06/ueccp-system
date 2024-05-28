@extends('layout')

@section('content')
    <div class="container mt-4">
        @auth
            @if (Auth::user()->isAdmin())
                <div class="mb-4">
                    <a href="{{ route('contests.create') }}" class="btn btn-primary">
                        コンテストを新規作成する
                    </a>
                </div>
            @endif
        @endauth
        @foreach ($contests as $contest)
            <div class="card mb-4">
                <div class="card-header">
                    {{ $contest->info["title"] }}
                </div>
                <div class="card-body">
                    <p class="card-text">
                        {!! Str::limit($contest->info["memo"], 200) !!}
                    </p>
                    <a class="card-link text-decoration-none" href="{{ route('contests.show', ['contest' => $contest]) }}">
                        詳細を見る
                    </a>
                </div>
                <div class="card-footer">
                    <span class="me-2">
                        開始時刻: {{ date('Y.m.d H:i', $contest->info["start_epoch_second"]) }}
                    </span>
                    <span class="me-2">
                        コンテスト時間: {{ floor($contest->info["duration_second"] / 60) }} 分
                    </span>
                </div>
            </div>
        @endforeach
        <div class="d-flex justify-content-center mb-5">
            {{ $contests->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endsection
