<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Library\Func;

class Tide extends Model
{
    use HasFactory;

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

                $minmax = self::getMinMax($data);
                $data->min = $minmax->min;
                $data->max = $minmax->max;

                $datas[$date_at->format('Y-m-d')] = $data;
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
}
