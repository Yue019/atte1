<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Work;
use App\Models\Rest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class WorkController extends Controller
{
  //勤務開始の処理
  public function startWork(Request $request)
  {
    //現在のユーザーを取得
      $userId = Auth::id();

    //今日の日付を取得
      $today = Carbon::today();

    // 現在の時刻を取得
      $startWork = Carbon::now();

    //すでに出勤があるか確認
      $work = Work::where('user_id',$userId)
      ->whereDate('date', $today)
      ->first();

      if($work){
          return redirect()->route('home')->with('error','すでに出勤しています。');
      }
    //出勤開始処理
      Work::create([
      'user_id' => $userId,
      'date' => $today,
      'start_work' => $startWork,
      'end_work' => null,//終了時刻は未定義
      ]);

    //勤務状態を "on" に変更
      $user = Auth::user();
      $user->save();
        return redirect()->route('home')->with('success','出勤しました。');
    }


  //退勤の処理
  public function endWork(Request $request)
  {
    $user = auth()->user();

    $work = Work::where('user_id',auth()->id())
      ->whereNull('end_work')
      ->first();

    if (!$work) {
        return redirect()->route('home')->with('error', '勤務レコードが見つかりません。');
    }

    $work->end_work = Carbon::now(); // 退勤時間を現在時刻で設定

   // 勤務時間と休憩時間を計算
    list($totalRestHMS, $workTimeHMS) = $this->calculateWorkTime($work);

    if ($totalRestHMS == '00:00:00' || $workTimeHMS == '00:00:00') {
        return redirect()->route('home')->with('error', '無効な勤務時間または休憩時間です。');
    }

    //休憩時間と実働時間を保存
    $work->total_rest = $totalRestHMS;  //休憩時間を保存
    $work->working_hours = $workTimeHMS;  //実働時間を保存
    $work->save();

      return redirect()->route('home')->with('success','お疲れさまでした。');
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



  //休憩開始の処理
    public function startRest(Request $request)
    {
    //現在の出勤記録の取得と新規作成
      $work = Work::where('user_id',auth()->id())
      ->whereDate('date', Carbon::now())
      ->first();

      if($work){
      $rest = new Rest();
      $rest->work_id = $work->id;//出勤記録のID
      $rest->start_rest = Carbon::now();
      $rest->save();

          return redirect()->route('home')->with('success','休憩を開始しました。');
      }
    }

  //休憩終了の処理
    public function endRest(Request $request)
    {

    //現在の出勤記録を取得
      $work = Work::where('user_id',auth()->id())
      ->whereDate('date', Carbon::now())
      ->first();

    //最新の休憩記録を取得
      if($work){
      $rest = Rest::where('work_id',$work->id)
      ->whereNull('end_rest')
      ->latest()
      ->first();

      if($rest){
      $rest->end_rest = Carbon::now();
      $rest->save();

    //休憩時間の計算と保存（秒数で保存）
      $duration = $rest->end_rest->diffInSeconds($rest->start_rest);
      $rest->duration = $duration;
      $rest->save();

          return redirect()->route('home')->with('success','休憩を終了しました。');
        }
      }
    }

    //日付別勤怠状況表示
    public function indexDate(Request $request)
    {
    //デフォルトの日付を設定
      $date = $request->input('date',now()->toDateString());

    //日付切り替え
      if($request->input('action') === 'previous') {
      $date = Carbon::parse($date)->subDay()->toDateString(); // 前日
    }  elseif ($request->input('action') === 'next') {
      $date = Carbon::parse($date)->addDay()->toDateString(); // 翌日
      }

    //勤怠データを取得し、ページネーションを適用
      $works = Work::with('rests')
      ->whereDate('date',$date)
      ->orderBy('date', 'desc')//降順で表示
      ->paginate(5);

          return view('attendance_date', compact('works', 'date'));
    }

}