<?php

namespace App\Models;

use App\Library\Qreki;
use Illuminate\Database\Eloquent\Model;
use App\Library\Func;
use phpDocumentor\Reflection\Types\Integer;

class Tide extends Model
{
    public static function getWeeklyWholeDatas(Place $place, \Datetime $start_at, int $week) {
        $ary = [];
        $datas = self::getDataFromTxt($start_at->format('Y'), $place->code);
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


    public static function getDataFromTxt($year, $place_id) {
        $file_fullpath = Func::getRootPath() . '/tide_datas/' . $year . '/' . $place_id . '.txt';

        $datas = new \stdClass();
        $datas->tides = [];
        $datas->count = 0;

        if(file_exists($file_fullpath)) {
            $ary = file($file_fullpath);
            foreach ($ary as $text) {
                $data = self::formatFromTxt($text);
                $datas->tides[$data->date_at->format('Y')][$data->date_at->format('n')][$data->date_at->format('j')] = $data;
                $datas->count++;
            }
        }

        return $datas;
    }

    public static function getAddSunAndMoon($tide_datas)
    {
        $Qreki = new Qreki();
        $datas = [];
        if (!empty($tide_datas)) {
            foreach ($tide_datas as $tide_data) {
                $data = $tide_data;

                // Sun
                $suninfo = date_sun_info(
                    $tide_data->date_at->format('U'),
                    $tide_data->lat,
                    $tide_data->lng
                );
                $data->sunrise = new \Datetime('@' . $suninfo['sunrise']);
                $data->sunset = new \Datetime('@' . $suninfo['sunset']);

                // Moon
                $moon_age = $Qreki->get_moon(
                    $tide_data->date_at->format('Y'),
                    $tide_data->date_at->format('n'),
                    $tide_data->date_at->format('j'),
                    12,
                    0,
                    0);
                $data->tide_name = $Qreki->tidename($moon_age);

                // Min Max
                $data->max1 = $tide_data->max_time1 ?: "-";
                $data->max2 = $tide_data->max_time2 ?: "-";
                $data->min1 = $tide_data->min_time1 ?: "-";
                $data->min2 = $tide_data->min_time2 ?: "-";

                $minmax = self::getMinMax($data);
                $data->min = $minmax->min;
                $data->max = $minmax->max;

                $datas[$tide_data->date_at->format('Y-m-d')] = $data;
            }
        }

        return $datas;
    }

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













    public static function formatFromTxt($tide_txt)
    {
        $data = new \stdClass();
        for ($i = 0; $i < 24; $i++) {
            $key = 'tide' . sprintf('%02d', $i);
            $data->$key = (int)substr($tide_txt, $i * 3, 3);
        }
        $year  = (int)substr($tide_txt, 72, 2);
        $month = (int)substr($tide_txt, 74, 2);
        $day   = (int)substr($tide_txt, 76, 2);
        $date_at = new \Datetime($year . '-' . $month . '-' . $day);
        $data->date_at = $date_at;//->format('Y-m-d');
        $data->place_id = substr($tide_txt, 78, 2);

        for ($i = 0; $i < 4; $i++) {
            $key = 'max_time'.($i + 1);
            $$key = substr($tide_txt, 80 + $i * 7, 4);
            $data->$key = $$key != 9999 ? self::hhiiToClockString($$key) : NULL;

            $key = 'max_tide'.($i + 1);
            $$key = (int)substr($tide_txt, 84 + $i * 7, 3);
            $data->$key = $$key != 999 ? $$key : NULL;

            $key = 'min_time'.($i + 1);
            $$key = substr($tide_txt, 108 + $i * 7, 4);
            $data->$key = $$key != 9999 ? self::hhiiToClockString($$key) : NULL;

            $key = 'min_tide'.($i + 1);
            $$key = (int)substr($tide_txt, 112 + $i * 7, 3);
            $data->$key = $$key != 999 ? $$key : NULL;

//            $data->{'max_time'.($i + 1)} = substr($text, 80 + $i * 7, 4);
//            $data->{'max_tide'.($i + 1)} = (int)substr($text, 84 + $i * 7, 3);
//            $data->{'min_time'.($i + 1)} = substr($text, 108 + $i * 7, 4);
//            $data->{'min_tide'.($i + 1)} = (int)substr($text, 112 + $i * 7, 3);
        }

        return $data;
    }

    public static function hhiiToClockString($hhii){
        $hour    = (int) substr($hhii, 0, 2);
        $minutes = (int) substr($hhii, 2, 2);

        return $hour . ':' . sprintf('%02d', $minutes);
    }


}
