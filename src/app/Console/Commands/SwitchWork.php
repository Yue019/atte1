<?php

namespace App\Console\Commands;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Work;
use App\Models\Rest;
use App\Models\User;


class SwitchWork extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'work:switch';
    protected $description = '毎日24時に勤務を自動で切り替える';

    /**
     * The console command description.
     *
     * @var string
     */

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now();
        $yesterdayEnd = $now->copy()->subDay()->endOfDay();

        $works = Work::whereNull('end_work')
        ->where('start_work', '<=', $yesterdayEnd)
        ->get();

        foreach ($works as $work) {
            DB::transaction(function () use ($work, $yesterdayEnd, $now) {
            // 前日の勤務を終了処理
            $work->end_work = $yesterdayEnd;
            list($totalRestHMS, $workTimeHMS) = $this->calculateWorkTime($work);
            $work->total_rest = $totalRestHMS;
            $work->working_hours = $workTimeHMS;
            $work->save();

            // 新しい勤務レコード作成（翌日00:00から勤務開始）
            Work::create([
            'user_id' => $work->user_id,
            'date' => $now->toDateString(),
            'start_work' => $now->startOfDay()->toDateTimeString(),
            'end_work' => null,
                ]);
            }
        );
        }
    }


    /**
    * 実働時間と休憩時間を計算するメソッド
    * @param Work $work 勤務レコード
    * @return array [休憩時間, 実働時間]
    */
        private function calculateWorkTime($work)
        {
         //休憩時間の合計を計算
        $totalRest = Rest::where('work_id',$work->id)
        ->sum('duration');

        //勤務終了時間と開始時間の差を計算
        $workingHoursInSeconds = $work->end_work->diffInSeconds($work->start_work);

       //実働時間の計算
        $actualWorkTime = $workingHoursInSeconds - $totalRest;

      //休憩時間と実働時間をH:M:S形式に変換
        $totalRestHMS = $this->formatTime($totalRest);
        $workTimeHMS = $this->formatTime($actualWorkTime);

            return [$totalRestHMS, $workTimeHMS];
    }

    /**
   * 時間をH:M:S形式に変換するメソッド
   * @param int $totalSeconds 秒数
   * @return string H:M:S 形式の時間
   */
    private function formatTime($totalSeconds)
    {
        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
}
