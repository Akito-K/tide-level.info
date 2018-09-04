<?php

namespace MyFacade;

use Illuminate\Support\Facades\DB;
use App\Model\Place;
use App\Model\Area;

class MyHTML
{

    /**
     * カレンダー生成（まとめ）
     *
     * @param Strings HTML
     * @param Strings HTML
     * @param array $key => $val
     * @return Strings HTML
     */
    public static function calendar($header, $body, $options){
        $params = ['class', 'wrapper'];
        foreach($params as $param){
            ${$param} = (isset($options[$param]))? $options[$param]: "";
        }
        $calendar = '';
        if($wrapper){
            $calendar .= '<div class="'.$wrapper.'">';
        }
        $calendar .= '
        <div class="holiday__calendar '.$class.'">
          '.$header.'
          <div class="holiday__calendar__body">
            '.$body.'
          </div>
        </div>';
        if($wrapper){
            $calendar .= '</div>';
        }

        return $calendar;
    }

    /**
     * カレンダー本体生成
     *
     * @param Integet year
     * @param Integet month
     * @param array $holidays = [
     *   'factory' => [2016][4] = [1, 2, 9, 16],
     *   'desk' => [2016][4] = [1, 2, 9, 16],
     *   ]
     * @return Strings HTML
     *
     */
    public static function calendarBody($y=null, $m=null, $holidays=null){
        $y = ($y)?: date("Y");
        $m = ($m)?: date("n");

        $days = array();
        $date_s = mktime(0, 0, 0, $m, -5, $y);
        $date_e = mktime(0, 0, 0, $m+1, 6, $y);

        $ts_prev = mktime(0, 0, 0, $m-1, 1, $y);
        $ts_this = mktime(0, 0, 0, $m, 1, $y);
        $ts_next = mktime(0, 0, 0, $m+1, 1, $y);

        $y_prev = date("Y", $ts_prev);
        $m_prev = date("n", $ts_prev);
        $y_this = date("Y", $ts_this);
        $m_this = date("n", $ts_this);
        $y_next = date("Y", $ts_next);
        $m_next = date("n", $ts_next);

        $ary = array();
        $line = 0;
        $i = 0;
        while($date_s + $i < $date_e){
            $ts = $date_s + $i;
            $wn = date("w", $ts);
            $yn = date("Y", $ts);
            $mn = date("n", $ts);
            $dn = date("j", $ts);
            $ary[$line][$wn] = array("y" => $yn, "m" => $mn, "d" => $dn);
            if($wn == 6){
                $line++;
            }
            $i += 24*60*60;
        }
        $dates = [];
        if(!empty($ary)){
            foreach($ary as $a){
                if(count($a) == 7){
                    $dates[] = $a;
                }
            }
        }
        // HTML 組み立て
        $calendar = '';
        if(!empty($dates)){
            foreach($dates as $a7){
                $calendar .= '<tr>';
                if(!empty($a7)){
                    foreach($a7 as $wnum => $a){
                        // 当月判定
                        if($a['y'] != $y_this || $a['m'] != $m_this){
                            $class_other_month = " other-month";
                            $data_this_month = 0;
                        }else{
                            $class_other_month = "";
                            $data_this_month = 1;
                        }
                        // 設定済休日判定
                        $data_holiday = "";
                        $factories = isset( $holidays['factory'][ $a['y'] ][ $a['m'] ] )? $holidays['factory'][ $a['y'] ][ $a['m'] ]: [];
                        $desks = isset( $holidays['desk'][ $a['y'] ][ $a['m'] ] )? $holidays['desk'][ $a['y'] ][ $a['m'] ]: [];
                        if( in_array( $a['d'], $factories ) && in_array( $a['d'], $desks ) ){
                            $data_holiday = "both";
                        }elseif( in_array( $a['d'], $desks ) ){
                            $data_holiday = "desk";
                        }elseif( in_array( $a['d'], $factories ) ){
                            $data_holiday = "factory";
                        }

                        $ymd = $a['y'].sprintf("%02d", $a['m']).sprintf("%02d", $a['d']);
                        $calendar .= '<td class="elm-calendar-day wday-'.$wnum.$class_other_month.'" data-thismonth="'.$data_this_month.'" data-holiday="'.$data_holiday.'" data-y="'.$a['y'].'" data-m="'.$a['m'].'" data-d="'.$a['d'].'">'.$a['d'];
                        if($data_this_month){
                            $calendar .= '<input type="hidden" class="input--holidays" name="holidays['.$ymd.']" value="'.$data_holiday.'">';
                            $calendar .= '<input type="hidden" class="input--years" name="years['.$ymd.']" value="'.$a['y'].'">';
                            $calendar .= '<input type="hidden" class="input--months" name="months['.$ymd.']" value="'.$a['m'].'">';
                            $calendar .= '<input type="hidden" class="input--days" name="days['.$ymd.']" value="'.$a['d'].'">';
                        }
                        $calendar .= '</td>';
                    }
                }
                $calendar .= '</tr>';
            }
        }

        $body = '
            <table class="holiday__calendar__body__table">
              <thead>
                <tr>
                  <th class="wday-0">日</th>
                  <th class="wday-1">月</th>
                  <th class="wday-2">火</th>
                  <th class="wday-3">水</th>
                  <th class="wday-4">木</th>
                  <th class="wday-5">金</th>
                  <th class="wday-6">土</th>
                </tr>
              </thead>
              <tbody>
                '.$calendar.'
              </tbody>
            </table>';

        return $body;
    }

    /**
     * 休日設定用カレンダーヘッダー
     *
     * @param Integet year
     * @param Integet month
     * @return Strings HTML
     */
    public static function calendarHeader_holiday($y, $m){
        $body = '
          <div class="holiday__calendar__header">
            <span class="holiday__calendar__header__year">'.$y.'</span>
            <span class="holiday__calendar__header__month">'.$m.'</span>
          </div>
        ';
        return $body;
    }


    // ログアウトの HTML セット
    public static function logout($text="ログアウト", $class="", $id="logout-form"){
        $body = '
            <form id="'.$id.'" action="'.url('').'/logout" method="POST" style="display: none;">
                '.\csrf_field().'
            </form>
            <a href="'.url('').'/logout" class="'.$class.'" onclick="event.preventDefault(); document.getElementById(\'logout-form\').submit();">'.$text.'</a>
        ';

        return $body;
    }

    /**
     * エラーメッセージ
     *
     * @param array $errors
     * @return String HTML
     */
    public static function errorMessage($errors=[]){
        $body = "";
        if( count($errors) > 0){
            $body .= '
                <div class="alert alert-danger">
                    <strong>【入力エラー】</strong> 入力に誤りがあります。ご確認の上もう一度送信してください。<br><br>
                    <ul>';
            foreach ($errors->all() as $error){
                $body .= '<li>'.$error.'</li>';
            }
            $body .= '
                    </ul>
                </div>';
        }
        return $body;
    }

    /**
     * フラッシュメッセージ
     *
     * @return String HTML
     */
    public static function flashMessage(){
        $body = "";
        if (\Session::has('flash_message')){
            $body .= '<div class="alert alert-success">'.\Session::get('flash_message').'</div>';
        }
        return $body;
    }

    public static function boardFile($message){
        $ext = \Func::getExtension($message->filepath);

        if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])){
            $body = '<a class="" href="'.str_replace('_sm', '_lg', env('IMG').$message->filepath).'" target="_blank"><img src="'.env('IMG').$message->filepath.'" alt="ファイル"></a>';

        }else{
            $name = $message->body?: 'アップロードされたファイル';
            $body = '<a class="" href="'.env('IMG').$message->filepath.'" target="_blank"><span class="text-bold">［'.$name.' .'. strtolower( \Func::getExtension($message->filepath) ) .'］</span></a>';
        }

        return $body;
    }

    // ページネーション
    /**
     * @param array
     * @param string
     * @param array | sometimes
     * @return string <HTML>
     */
    public static function pagenation($pages, $url, $option=[]){
        $ary = [];
        $prev = $pages['current'] - 1;
        if($prev <= 1){
            $prev = 1;
        }
        $next = $pages['current'] + 1;
        if($next >= $pages['total']){
            $next = $pages['total'];
        }

        $body = '<div class="pagenation">';
        $body .= '
            <div class="pagenation__buttons">
                <a href="'.$url.$prev.'" class="btn btn-default pagenation__buttons__btn">前へ</a>
                <a href="'.$url.$next.'" class="btn btn-default pagenation__buttons__btn">次へ</a>
                '.\Form::select('', \Func::range(1, $pages['total']), $pages['current'], ['id' => 'trigPageSelect'] ).'ページ
            </div>';

        $body .= '<div class="pagenation__numbers">';
        // <<
        $body .= '<a href="'.$url.'1" class="pagenation__numbers__num"><i class="fa fa-angle-double-left"></i></a>';
        // <
        $body .= '<a href="'.$url.$prev.'" class="pagenation__numbers__num"><i class="fa fa-angle-left"></i></a>';
        // 1
        $ary[] = 1;
        $body .= \MyHTML::pagenationText($pages, $url, 1);
        // 2 | n-1
        if($pages['total'] > 1){
            for($i=2; $i<$pages['total']; $i++){
                if( abs($i - $pages['current']) > 2){
                    if(!in_array('・・・', $ary)){
                        $ary[] = '・・・';
                        $body .= '<span class="pagenation__numbers__num"><i class="fa fa-ellipsis-h"></i></span>';
                    }
                }elseif(!in_array($i, $ary)){
                    $ary[] = $i;
                    $body .= \MyHTML::pagenationText($pages, $url, $i);
                }
            }
        }
        // n
        if(!in_array($pages['total'], $ary)){
            $body .= \MyHTML::pagenationText($pages, $url, $pages['total']);
        }
        // >
        $body .= '<a href="'.$url.$next.'" class="pagenation__numbers__num"><i class="fa fa-angle-right"></i></a>';
        // >>
        $body .= '<a href="'.$url.$pages['total'].'" class="pagenation__numbers__num"><i class="fa fa-angle-double-right"></i></a>';
        $body .= '</div><!--  /.pagenation__numbers -->';

        $body .= '</div><!-- /.pagenation -->';

        return $body;
    }

    public static function pagenationText($pages, $url, $page){
        if($page == $pages['current']){
            $body = '<span class="pagenation__numbers__num pagenation__numbers__num--current">'.$page.'</span>';
        }else{
            $body = '<a href="'.$url.$page.'" class="pagenation__numbers__num">'.$page.'</a>';
        }

        return $body;
    }

    public static function visitedWeekDaysText($data, $delimiter='、', $add_str='曜日'){
        $wdays = \Func::$wdays;
        $flags = [];
        if(!empty($data)){
            for($i=0; $i<=6; $i++){
                $key = 'flag_wday'.$i;
                if($data->$key){
                    $flags[] = $wdays[$i].$add_str;
                }
            }
        }
        $body = "";
        if(!empty($flags)){
            $body = implode($delimiter, $flags);
        }

        return $body;
    }



}
