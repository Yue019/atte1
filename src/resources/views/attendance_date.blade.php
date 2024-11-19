@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
@endsection


@section('content')

    <form action="{{ route('attendance.date') }}" method="get">
        @csrf
        <div class ="date-box">
        <button type = "submit" class="arrow-button" name="action" value ="prev">&lt;</button>
        <input type="hidden" name="date" value="{{ $date }}">
        <p class="header__text">{{ $date }}</p>
        <button class="arrow-button" name="action" value="next">&gt;</button>
        <input type="hidden" name="date" value="{{ $date }}">
        </div>
    </form>

    <div class="table__wrap">
        <table class="attendance__table">
            <tr class="table__row">
                <th class="table__header">名前</th>
                <th class="table__header">勤務開始</th>
                <th class="table__header">勤務終了</th>
                <th class="table__header">休憩時間</th>
                <th class="table__header">勤務時間</th>
            <tr>
            @foreach ($works as $work)
            <tr class="table__row">
                <td class="table__item">{{ $user->name }}</td>
                <td class="table__item">{{ $work->start_work }}</td>
                <td class="table__item">{{ $work->end_work }}</td>
                <td class="table__item">{{ $work->total_rest }}</td>
                <td class="table__item">{{ $work->working_hours }}</td>
            </tr>
            @endforeach
        </table>
    </div>

    <!-- ページネーションリンクの表示 -->
    {{ $works->links() }}

@endsection