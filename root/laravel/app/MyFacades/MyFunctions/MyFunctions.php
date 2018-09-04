<?php

namespace MyFacade;

use Illuminate\Support\Facades\DB;
use App\Model\Upload;
//use App\Model\MyUser;

class MyFunctions
{
    public static $wdays = [
        0 => '日',
        1 => '月',
        2 => '火',
        3 => '水',
        4 => '木',
        5 => '金',
        6 => '土',
    ];

    public static function getRootPath(){
        return dirname(dirname(dirname(dirname(__DIR__))));
    }

    // 配列の最初のキーを取得する
    public static function getFirstKey($ary){
        if(!empty($ary)){
            foreach($ary as $k => $v){
                return $k;
                break;
            }
        }
    }

    // 配列の最初のキーを取得する
    public static function getLastKey($ary){
        $key = "";
        if(!empty($ary)){
            foreach($ary as $k => $v){
                $key = $k;
            }
        }

        return $key;
    }

    // 自分のアイコン画像パス
    public static function myIcon(){
        if(\Auth::user()->icon_filepath){
            $img = env('IMG').\Auth::user()->icon_filepath;
        }else{
            $img = '/img/no-image.png';
        }

        return $img;
    }

    // DBの画像パス
    public static function getImage($icon_filepath, $size=NULL){
        if($icon_filepath){
            $img = $icon_filepath;
            if($size){
                $img = str_replace('_sm', '_'.$size, $img);
            }
        }else{
            $img = '/img/no-image.png';
        }

        return $img;
    }

    // 西暦を元号に
    public static function jpnYear($year){
        $jp = "";
        if($year >= 1989){
            $jp = '平成'.($year - 1989 + 1);
        }elseif($year >= 1926){
            $jp = '昭和'.($year - 1926 + 1);
        }elseif($year >= 1912){
            $jp = '大正'.($year - 1912 + 1);
        }elseif($year >= 1868){
            $jp = '明治'.($year - 1868 + 1);
        }

        return '／'.$jp;
    }

    public static function getUploadId(){
        return Upload::getNewId();
    }

    /**
     * @param string | Datetime Object
     * @param string
     * @return string
     */
    public static function dateFormat($datetime, $format=NULL){
        $result = "";
        if($datetime instanceof \Datetime){
            $datetime_at = $datetime;
        }elseif(preg_match('/^[0-9]{4}-[0-9]{1, 2}-[0-9]{1, 2}[.]+/', $datetime)){
            $datetime_at = new \Datetime($datetime);
        }

        if(isset($datetime_at)){
            if($format){
                if(preg_match('/(wday)/', $format)){
                    $result = $datetime_at->format( str_replace('(wday)', '', $format) );
                    $result .= '(' . \Func::$wdays[ $datetime_at->format('w') ] . ')';
                }else{
                    $result = $datetime_at->format($format);
                }
            }else{
                $result = $datetime_at->format('Y/n/j');
            }
        }

        return $result;
    }

    public static function getWeekDay($datetime){
        $result = "";
        if($datetime instanceof \Datetime){
            $result = $datetime->format('w');
        }elseif(preg_match('/^[0-9]{4}-[0-9]{1, 2}-[0-9]{1, 2}[.]+/', $datetime)){
            $datetime_at = new \Datetime($datetime);
            $result = $datetime_at->format('w');
        }

        return \Func::$wdays[$result];
    }

    /**
     * 指定された文字列で指定された長さの乱数を返す
     *
     * @param Strings EX) Aa
     * @param Integer
     * @param Integer
     * @return Strings
     */
    public static function getRandStr($char="Aa0", $len=0, $count=1){
        $alphabet_upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $alphabet_lower = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $chars = "";
        $limits = "";
        if(strpos($char, "0") !== false){
            $chars .= $limits = $numbers;
        }
        if(strpos($char, "a") !== false){
            $chars .= $limits = $alphabet_lower;
        }
        if(strpos($char, "A") !== false){
            $chars .= $limits = $alphabet_upper;
        }
        // $count数繰り返す
        $strs = array();
        for($i=0; $i<$count; $i++){
            $str = "";
            $len = ($len)?: rand(16, 24);
            // 1文字目は限定させる
            $pos = rand(0, (strlen($limits)-1) );
            $str = $limits{$pos};
            // 2文字目以降
            for($j=1; $j<$len; $j++){
                $pos = rand(0, strlen($chars)-1 );
                $str .= $chars{$pos};
            }
            $strs[] = $str;
        }
        if($count == 1){
            return $strs[0];
        }else{
            return $strs;
        }
    }

    // var_dump
    public static function var_dump($data, $dev=NULL){
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
    }

    // 配列から最初に値にマッチしたものを削除
    public static function my_unset($needle, $ary){
        $key = array_search($needle, $ary);
        if($key !== false){
            if(isset($ary[$key])){
                unset($ary[$key]);
            }
        }

        return $ary;
    }

    public static function rmBR($str){
        return str_replace( array("<br />", "<br>"), "", $str);
    }

    public static function N2BR($str){
        return str_replace( array("\n", "\r\n"), "<br>", $str);
    }


    // CSVファイルをオブジェクトの配列に変換
    public static function CSV_to_Arrays($file_path){
        $csv_data = file_get_contents($file_path);
        $csv_datas = explode("\r\n", $csv_data);

        $keys_str = $csv_datas[0];
        $keys = explode(",", str_replace("\"", "", $keys_str));

        $datas = array();
        for($i=1; $i<count($csv_datas); $i++){
            $a_ary = explode(",", str_replace("\"", "", $csv_datas[$i]));
            if($a_ary[0]){
                $data = array();
                foreach($a_ary as $k => $v){
                    $data[$keys[$k]] = $v;
                }
                $datas[] = $data;
            }
        }

        return $datas;
    }

    public static function freeTextToAry($text){
        $text = str_replace("　", " ", $text);
        if(preg_match('/ /', $text)){
            $ary = explode(" ", $text);
            $words = [];
            foreach($ary as $v){
                if($v && count($words) < 3){
                    $words[] = $v;
                }
            }
        }elseif(strlen($text) > 0){
            $words = [$text];
        }else{
            $words = NULL;
        }

        return $words;
    }

    /**
     * @param string
     * @return ary | NULL
     */
    public static function dateStrToObj($str){
        if(preg_match('/\d{4}\/\d{1,2}\/\d{1,2}/', $str)){
            $ary = explode("/", $str);
            $data = [
                'year' => $ary[0],
                'month' => $ary[1],
                'day' => $ary[2],
                ];

            return $data;
        }else{

            return NULL;
        }
    }

    // 連想配列やオブジェクトを配列にする（キーは保存されない）
    public static function arrayAlignment($datas, $start=0){
        $ary = [];
        if(!empty($datas)){
            foreach($datas as $data){
                $ary[ $start ] = $data;
                $start++;
            }
        }

        return $ary;
    }

    // scandir() 改造版
    public static function scanDir($dir){
        if(!$dir){
            return false;
        }else{
            $datas = array();
            $list = scandir($dir);
            if(!empty($list)){
                foreach($list as $v){
                    if(!preg_match("/^\.+$/", $v)){
                        $datas[] = $v;
                    }
                }
            }
            return $datas;
        }
    }

    // scandir() 改造版 - ディレクトリ対応版
    public static function scanDirs($dir){
        if(!$dir){
            return false;
        }else{
            $datas = array();
            if(file_exists($dir)){
                $lists = \Func::scanDir($dir);
                if(!empty($lists)){
                    foreach($lists as $i => $list){
                        $data = new \stdClass();
                        if( is_dir($dir."/".$list) ){
                            $data->name = $list;
                            $data->type = "dir";
                            $data->list = \Func::scanDirs($dir."/".$list);
                        }else{
                            $data->name = $list;
                            $data->type = "file";
                        }
                        $datas[$i] = $data;
                    }
                }
            }
            return $datas;
        }
    }

    // フォルダごとすべて削除
    public static function rmDirs($dir){
        $dir = (preg_match("/(.*)\/$/", $dir))? substr($dir, 0, strlen($dir)-1): $dir;
        if(file_exists($dir)){
            if ($handle = opendir($dir)) {
                while (false !== ($item = readdir($handle))) {
                    if ($item != "." && $item != "..") {
                        if (is_dir($dir.'/'.$item)) {
                            \Func::rmDirs($dir.'/'.$item);
                        } else {
                            unlink($dir.'/'.$item);
                        }
                    }
                }
                closedir($handle);
                rmdir($dir);
            }
        }else{
            echo 'No such file or directory - '.$dir.' !';
            exit;
        }
    }

    // ファイル名から拡張子を小文字で取得
    public static function getExtension($filename){
        $ext = "";
        if(strpos($filename, '.') !== FALSE){
            $arr = explode('.', $filename);
            $ext = strtolower( end($arr) );
        }

        return $ext;
    }

    /**
     * @param array (year: Y, month: n, day: j)
     * @param string
     * @return string
     */
    public static function getNewDatetime($dates, $format=('Y-m-d H:i:s')){
        if( isset($dates['year']) && isset($dates['month']) && isset($dates['day']) ){
//        if( $dates['year'] && $dates['month'] && $dates['day'] ){
            $date = new \Datetime( $dates['year'].'-'.$dates['month'].'-'.$dates['day'] );
            $date_at = $date->format($format);
        }else{
            $date_at = NULL;
        }

        return $date_at;
    }

    // 連想配列に配列を追加する
    public static function array_append($bases, $adds, $pre=null){
        if($pre){
            // 先頭に追加
            return array_merge($adds, $bases);
        }else{
            // 末尾に追加
            return array_merge($bases, $adds);
        }
    }

    public static function stringToAnchor($str){
        $body = $str;
        $matches = \Func::getURL($str);
        if(!empty($matches[0])){
            foreach($matches[0] as $url){
                $replace = '<a href="'.$url.'" target="_blank">'.$url.'</a>';
                $body = str_replace($url, $replace, $body);
            }
        }

        return $body;
    }

    public static function getURL($str){
        $pattern = 'https?\:\/\/[\w\/\:%#\$&\?\(\)~\.=\+\-]+';
        if(preg_match_all('/'.$pattern.'/', $str, $matches)){
            return $matches;
        }
    }

    // range オリジナル（連想配列）
    public static function range($start, $end, $step=1){
        $ary = [];
        $range = range($start, $end, $step);
        if(!empty($range)){
            foreach($range as $n){
                $ary[$n] = $n;
            }
        }

        return $ary;
    }

    public static function myFilePutContents($content, $fullpath=NULL, $flag_add=true){
        $fullpath = $fullpath?: '/usr/home/ae159j6q55/html/mylogs/log';
        $content = date('Y-m-d H:i:s')."\n".$content."\n";
        if($flag_add){
            if(file_exists($fullpath)){
                // ファイルポインタをオープン
                $handle = fopen($fullpath, "r");
                // ファイル内容を取得
                $body = "";
                while ($line = fgets($handle)) {
                  $body .= $line;
                }
                $content = $body.$content;
                // ファイルポインタをクローズ
                fclose($handle);
            }
        }

        // ファイルポインタをオープン
        $handle = fopen($fullpath, "w");
        // ファイルへ書き込み
        fwrite($handle, $content);
        // ファイルポインタをクローズ
        fclose($handle);
    }

    public static function var_var_dump($data) {
        // 出力バッファリング開始
        ob_start();
        var_dump($data);
        // バッファの内容を変数へ格納
        $var = ob_get_contents();
        // 出力バッファを消去してバッファリング終了
        ob_end_clean();

        return $var;
    }

    // 本文抜粋
    public static function getExcerpt($str, $length=44, $flag_remain=true){
        $str = str_replace("\n", "", $str);
        if(mb_strlen($str) > $length){
            $body = mb_substr($str, 0, $length);
            if($flag_remain){
                $body .= ' ...（残 '.( mb_strlen($str) - $length ).'字）';
            }
        }elseif($str){
            $body = $str;
        }else{
            $body = '-';
        }

        return $body;
    }

    // 年月から期間を取得
    public static function getPeriod($year, $month){
        $day_first_at = new \DatetimeImmutable($year.'-'.$month.'-1');
        $day_last_at = $day_first_at->modify('Last day of this month');

        return $day_first_at->format('Y/n/j').' ～ '.$day_last_at->format('Y/n/j');
    }

    // その月の最終日を取得
    public static function getLastDay($year, $month){
        $day_first_at = new \DatetimeImmutable($year.'-'.$month.'-1');
        $day_last_at = $day_first_at->modify('Last day of this month');

        return $day_last_at->format('j');
    }

    // LatLng フォーマット
    public static function mylatlng_format($latlng){
        $ary = \Func::getLatLngAry($latlng);
        $val = 0;
        if(isset($ary['do'])){
            $val += $ary['do'];
        }
        if(isset($ary['hun'])){
            $val += $ary['hun'] / 60;
        }
        if(isset($ary['byou'])){
            $val += $ary['byou'] / 3600;
        }

        return $val;
    }

    // 東経北緯を配列に格納
    public static function getLatLngAry($latlng){
        $ary = [];
        if($latlng){
            if(preg_match("/゜/", $latlng)){
                $dos_ary = explode("゜", $latlng);
                $ary['do'] = $dos_ary[0];
                if(preg_match("/'/", $dos_ary[1])){
                    $huns_ary = explode("'", $dos_ary[1]);
                    $ary['hun'] = $huns_ary[0];
                    if($huns_ary[1]){
                        $ary['byou'] = $huns_ary[1];
                    }
                }else{
                    $ary['hun'] = $dos_ary[1];
                }
            }
        }

        return $ary;
    }

    public static function getTextFile($url, $place_id){
        if($text = @file_get_contents($url)) {
            $root_dir = \Func::getRootPath();
            $path = $root_dir.'/tide_datas';
            if( !file_exists( $path ) ){
                mkdir($path);
            }
            $path .= '/'.date('Y');
            if( !file_exists($path) ){
                mkdir($path);
            }

            $file_name = $place_id . '.txt';
            $file_path = $path.'/'.$file_name;
            $fp = @fopen($file_path, 'w');
            flock($fp, LOCK_EX);
            fputs($fp, $text);
            fclose($fp);
        }
    }

    public static function toTime($str){
        $hour = (int) substr($str, 0, 2);
        $minutes = (int) substr($str, 2, 2);

        return $hour.':'.sprintf('%02d', $minutes);
    }

    static public function setSkin($request){
        if($request->session()->has('skin')){
            $skin = $request->session()->get('skin');
        }else{
            $skin = 'dark';
            if(date('H') >= 5 || date('H') <= 18){
                $skin = 'light';
            }
            $request->session()->put('skin', $skin);
        }

        return $skin;
    }


}
