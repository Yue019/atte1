@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
@endsection

@section('content')
    @if(session('success'))
        <div class="alert_success">
        {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert_danger">
        {{ session('error') }}
        </div>
    @endif

    <div class="header__content">
        <p class="header__text">
            {{ \Auth::user()->name }}さんお疲れ様です！
        </p>
    </div>

    <div class="attendance__wrap">
        <form class="form" action="{{ route('start-work') }}" method="post">
            @csrf
            <div class="attendance__button">
                <button class="attendance__button-submit" name="start_work" type="submit">勤務開始</button>
            </div>
        </form>
        <form class="form" action="{{ route('end-work') }}" method="post">
            @csrf
            <div class="attendance__button">
                <button class="attendance__button-submit" name="end_work" type="submit">勤務終了</button>
            </div>
        </form>
        <form class="form" action="{{ route('start-rest') }}" method="post">
            @csrf
            <div class="attendance__button">
                <button class="attendance__button-submit" name="start_rest" type="submit">休憩開始</button>
            </div>
        </form>
        <form class="form" action="{{ route('end-rest') }}" method="post">
            @csrf
            <div class="attendance__button">
                <button class="attendance__button-submit" name="end_rest" type="submit">休憩終了</button>
            </div>
        </form>
    </div>
</form>
@endsection