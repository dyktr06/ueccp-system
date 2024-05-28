@extends('layout')
@section('content')
    <div class="container mt-4">
        <div class="border p-4">
            <h1 class="h5 mb-4">
                コンテストの新規作成
            </h1>

            <form method="POST" action="{{ route('contests.store') }}">
                @csrf

                <fieldset class="mb-4">
                    <div class="form-group">
                        <label for="contest_id">
                            Contest ID
                        </label>
                        <input id="contest_id" name="contest_id"
                            class="form-control {{ $errors->has('contest_id') ? 'is-invalid' : '' }}" value="{{ old('contest_id') }}"
                            type="text">
                        @if ($errors->has('contest_id'))
                            <div class="invalid-feedback">
                                {{ $errors->first('contest_id') }}
                            </div>
                        @endif
                    </div>

                    <div class="mt-5">
                        <a class="btn btn-secondary" href="{{ route('top') }}">
                            キャンセル
                        </a>

                        <button type="submit" class="btn btn-primary">
                            作成する
                        </button>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
@endsection
