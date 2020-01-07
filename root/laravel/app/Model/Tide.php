<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use MyFacade\MyFunctions as Func;

class Tide extends Model
{
    protected $table = 'tides';
    protected $dates = ['date_at'];
    protected $guarded = ['id'];

    public static function getFileData($year, $place_id) {
        $datas = new \stdClass();
        $datas->tides = [];
        $datas->count = 0;

        $root = Func::getRootPath();
        $filepath = $root.'/tide_datas/'.$year.'/'.$place_id.'.txt';
        if(file_exists($filepath)){
            $texts = file($filepath);
            $datas = self::toTideDatas2($texts);
        }

        return $datas;
    }

    public static function isExist($place_id, $year)
    {
        $date_at = new \Datetime($year . '-1-1');
        $data = Tide::where('place_id', $place_id)->where('date_at', $date_at->format('Y-m-d'))->first();

        return $data ? true : false;
    }

    public static function toTideDatas($ary)
    {
        $datas = [];
        foreach ($ary as $text) {
            $data = Tide::toTideData($text);
            $datas[$data->date_at] = $data;
        }

        return $datas;
    }

    public static function toTideDatas2($ary)
    {
        $datas = new \stdClass();
        $datas->tides = [];
        $datas->count = 0;
        foreach ($ary as $text) {
            $data = Tide::toTideData($text);
            $date_at = new \Datetime($data->date_at);
            $datas->tides[$date_at->format('Y')][$date_at->format('n')][$date_at->format('j')] = $data;
            $datas->count++;
        }

        return $datas;
    }

    public static function toTideData($text)
    {
        $data = new \stdClass();
        for ($i = 0; $i < 24; $i++) {
            $key = 'tide' . sprintf('%02d', $i);
            $data->$key = (int)substr($text, $i * 3, 3);
        }
        $year = (int)substr($text, 72, 2);
        $month = (int)substr($text, 74, 2);
        $day = (int)substr($text, 76, 2);
        $date_at = new \Datetime($year . '-' . $month . '-' . $day);
        $data->date_at = $date_at->format('Y-m-d');

        $data->place_id = substr($text, 78, 2);

        for ($i = 0; $i < 4; $i++) {
            $key = 'max_time'.($i + 1);
            $$key = substr($text, 80 + $i * 7, 4);
            $data->$key = $$key != 9999 ? Func::toTime($$key) : NULL;

            $key = 'max_tide'.($i + 1);
            $$key = (int)substr($text, 84 + $i * 7, 3);
            $data->$key = $$key != 999 ? $$key : NULL;

            $key = 'min_time'.($i + 1);
            $$key = substr($text, 108 + $i * 7, 4);
            $data->$key = $$key != 9999 ? Func::toTime($$key) : NULL;

            $key = 'min_tide'.($i + 1);
            $$key = (int)substr($text, 112 + $i * 7, 3);
            $data->$key = $$key != 999 ? $$key : NULL;

//            $data->{'max_time'.($i + 1)} = substr($text, 80 + $i * 7, 4);
//            $data->{'max_tide'.($i + 1)} = (int)substr($text, 84 + $i * 7, 3);
//            $data->{'min_time'.($i + 1)} = substr($text, 108 + $i * 7, 4);
//            $data->{'min_tide'.($i + 1)} = (int)substr($text, 112 + $i * 7, 3);
        }

        return $data;
    }

    public static function saveDatas($datas)
    {
        foreach ($datas as $data) {
            $tide = new Tide;
            $tide->date_at = $data->date_at;
            $tide->place_id = $data->place_id;
            for ($i = 0; $i < 24; $i++) {
                $key = 'tide' . sprintf('%02d', $i);
                $tide->$key = $data->$key;
            }
            for ($i = 1; $i <= 4; $i++) {
                $key = 'max_time' . $i;
                $tide->$key = $data->$key != 9999 ? Func::toTime($data->$key) : NULL;
                $key = 'max_tide' . $i;
                $tide->$key = $data->$key != 999 ? $data->$key : NULL;
                $key = 'min_time' . $i;
                $tide->$key = $data->$key != 9999 ? Func::toTime($data->$key) : NULL;
                $key = 'min_tide' . $i;
                $tide->$key = $data->$key != 999 ? $data->$key : NULL;
            }
            $tide->save();
        }
    }

    public static function getDatas($place_id, $date_at, $week)
    {
        $start_at = new \Datetime($date_at);
        $days = 7 * $week;
        $datas = Tide::where('place_id', $place_id)
            ->where('date_at', '>=', $start_at->format('Y-m-d'))
            ->orderBy('date_at', 'ASC')
            ->limit($days)
            ->get();

        return $datas;
    }

    public static function getDatas2($place, $date_at, $week) {
        $ary = [];
        $start_at = new \Datetime($date_at);
        $datas = self::getFileData($start_at->format('Y'), $place->place_id);
        $i = 0;
        $active = false;
        if(isset($datas->tides)){
            foreach($datas->tides as $year => $monthes){
                foreach($monthes as $month => $days){
                    foreach($days as $day => $data){
                        if($i >= 7 * $week){
                            break 3;
                        }
                        if($month == $start_at->format('n') && $day == $start_at->format('j')){
                            $active = true;
                        }
                        if($active){
                            $data->lat = Func::mylatlng_format($place->lat);
                            $data->lng = Func::mylatlng_format($place->lng);
                            $ary[] = $data;
                            $i++;
                        }
                    }
                }
            }
        }

        return $ary;
    }

    public static function getTideDatas($tide_datas, $qreki)
    {
        $datas = [];
        if (!empty($tide_datas)) {
            foreach ($tide_datas as $tide_data) {
                $data = $tide_data;

                $date_at = new \Datetime($tide_data->date_at);
                $data->date_at = $date_at;

                $suninfo = date_sun_info(
                    $date_at->format('U'),
                    $tide_data->lat,
                    $tide_data->lng
                );
                $data->sunrise = new \Datetime('@' . $suninfo['sunrise']);
                $data->sunset = new \Datetime('@' . $suninfo['sunset']);
                $moon_age = $qreki->get_moon($date_at->format('Y'), $date_at->format('n'), $date_at->format('j'), 12, 0, 0);
                $data->tide_name = $qreki->tidename($moon_age);

                $data->max1 = $tide_data->max_time1 ?: "-";
                $data->max2 = $tide_data->max_time2 ?: "-";
                $data->min1 = $tide_data->min_time1 ?: "-";
                $data->min2 = $tide_data->min_time2 ?: "-";

                $minmax = Tide::getMinMax($data);
                $data->min = $minmax->min;
                $data->max = $minmax->max;

                $datas[$date_at->format('Y-m-d')] = $data;
            }
        }

        return $datas;
    }
/*
    public static function getDetailDatas($tide_datas) {
        $datas = [];
        if (!empty($tide_datas)) {
            foreach ($tide_datas as $tide_data) {
                $data = $tide_data;
                $date_at = new \Datetime($tide_data->date_at);
                $data->date_at = $date_at;
                $minmax = Tide::getMinMax($data);
                $data->min = $minmax->min;
                $data->max = $minmax->max;

                $datas[Func::dateFormat($data->date_at, 'Y-m-d')] = $data;
            }
        }

        return $datas;
    }
*/
    static public function getMinMax($data)
    {
        $minimum = 10000;
        $maximum = -10000;
        $min = $max = [];
        for ($i = 0; $i <= 23; $i++) {
            $key = 'tide' . sprintf('%02d', $i);
            $tide = $data->$key;
            if ($tide < $minimum) {
                $minimum = $tide;
                $min = $i;
            }
            if ($tide > $maximum) {
                $maximum = $tide;
                $max = $i;
            }
        }
        $data = new \stdClass();
        $data->min = $min;
        $data->max = $max;

        return $data;
    }

}
