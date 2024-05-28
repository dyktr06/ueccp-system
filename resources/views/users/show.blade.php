@extends('layout')

@section('content')
    <div class="container mt-4">
        <div class="border p-4">
            <h1 class="h5 mb-4">
                {{ $user_id }}
            </h1>
            @if (!empty($contest_results))
                <h2 class="h5">
                    過去の成績表
                </h2>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>コンテスト名</th>
                            <th>順位</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($contest_results as $result)
                            <tr>
                                <td>{{ $result["contest_title"] }}</td>
                                <td>{{ $result["user_rank"] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection
