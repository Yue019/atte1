<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Work;
use App\Models\Rest;
use App\Models\User;
use Illuminate\Support\Facades\Log;


class SwitchWork extends Command
{
    protected $signature = 'work:switch';
    protected $description = '毎日24時に勤務を自動で切り替える';
    const TIMEZONE = 'Asia/Tokyo';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
{
    $now = Carbon::now(self::TIMEZONE);
    $yesterdayEnd = $now->copy()->subDay()->setTime(23, 59, 59);

    // デバッグ情報を出力
    Log::debug('現在の日時: ' . $now);
    Log::debug('前日終了時刻: ' . $yesterdayEnd);

    $works = Work::whereNull('end_work')
        ->where('start_work', '<=', $yesterdayEnd)
        ->get();

    Log::debug('対象となる勤務レコード: ', $works->toArray());

    foreach ($works as $work) {
        Log::debug('勤務終了処理: ' . $work->id);

        // 前日の勤務を終了
        $work->end_work = $yesterdayEnd;
        $work->total_rest = $this->formatTime(Rest::where('work_id', $work->id)->sum('duration'));
        $work->working_hours = $this->formatTime($work->end_work->diffInSeconds($work->start_work) - Rest::where('work_id', $work->id)->sum('duration'));
        $work->save();

        Log::debug('勤務レコード更新後: ', $work->toArray());

        // 翌日の勤務を作成
        Work::create([
            'user_id' => $work->user_id,
            'date' => $now->toDateString(),
            'start_work' => $now->startOfDay()->toDateTimeString(),
            'end_work' => null,
        ]);

        Log::debug('新しい勤務レコード作成完了');
    }

    Log::info('勤怠の切り替えが実行されました。');
}


    private function updateWork($work, $yesterdayEnd, $now)
    {
        // 勤務終了処理と翌日の勤務レコード作成
        $work->update([
            'end_work' => $yesterdayEnd,
            'total_rest' => $this->formatTime(Rest::where('work_id', $work->id)->sum('duration')),
            'working_hours' => $this->formatTime($work->end_work->diffInSeconds($work->start_work) - Rest::where('work_id', $work->id)->sum('duration'))
        ]);

        // 翌日勤務を新規作成
        Work::create([
            'user_id' => $work->user_id,
            'date' => $now->toDateString(),
            'start_work' => $now->startOfDay()->toDateTimeString(),
            'end_work' => null,
        ]);
    }

    private function formatTime($totalSeconds)
    {
        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
}
