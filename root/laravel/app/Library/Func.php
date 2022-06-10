<?php
namespace App\Library;

class Func {

    public static $wdays = [
        0 => '日',
        1 => '月',
        2 => '火',
        3 => '水',
        4 => '木',
        5 => '金',
        6 => '土',
    ];

    public static function getWeekDay($datetime){
        $result = 0;
        if($datetime instanceof \Datetime){
            $result = $datetime->format('w');
        }elseif(preg_match('/^[0-9]{4}-[0-9]{1, 2}-[0-9]{1, 2}[.]+/', $datetime)){
            $datetime_at = new \Datetime($datetime);
            $result = $datetime_at->format('w');
        }

        return self::$wdays[$result];
    }

    public static function getRootPath(){
        return dirname(dirname(dirname(__DIR__)));
    }

    // 日付フォーマット
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
                    $result .= '(' . self::$wdays[ $datetime_at->format('w') ] . ')';
                }else{
                    $result = $datetime_at->format($format);
                }
            }else{
                $result = $datetime_at->format('Y/n/j');
            }
        }

        return $result;
    }

    // LatLng フォーマット
    public static function mylatlng_format($latlng){
        $ary = self::getLatLngAry($latlng);
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
