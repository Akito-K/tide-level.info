<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Tide extends Model
{
    protected $table = 'tides';
    protected $dates = ['date_at'];
    protected $guarded = ['id'];


    public static function isExist($place_id, $year){
        $date_at = new \Datetime($year.'-1-1');
        $data = Tide::where('place_id', $place_id)->where('date_at', $date_at->format('Y-m-d'))->first();

        return $data? true: false;
    }

    public static function toTideDatas($ary){
        $datas = [];
        foreach($ary as $text){
            $data = Tide::toTideData($text);
            $datas[ $data->date_at ] = $data;
        }

        return $datas;
    }

    public static function toTideData($text){
        $data = new \stdClass();
        for($i=0; $i<24; $i++){
            $key = 'tide'.sprintf('%02d', $i);
            $data->$key = (int) substr($text, $i*3, 3);
        }
        $year  = (int) substr($text, 72, 2);
        $month = (int) substr($text, 74, 2);
        $day   = (int) substr($text, 76, 2);
        $date_at = new \Datetime($year.'-'.$month.'-'.$day);
        $data->date_at = $date_at->format('Y-m-d');

        $data->place_id = substr($text, 78, 2);

        $data->max_time = [];
        $data->max_tide = [];
        $data->min_time = [];
        $data->min_tide = [];
        for($i=0; $i<4; $i++){
            $data->max_time[$i+1] = substr($text, 80 + $i*7, 4);
            $data->max_tide[$i+1] = (int) substr($text, 84 + $i*7, 3);
            $data->min_time[$i+1] = substr($text, 108 + $i*7, 4);
            $data->min_tide[$i+1] = (int) substr($text, 112 + $i*7, 3);
        }

        return $data;
    }

    public static function saveDatas($datas){
        foreach($datas as $data){
            $tide = new Tide;
            $tide->date_at = $data->date_at;
            $tide->place_id = $data->place_id;
            for($i=0; $i<24; $i++){
                $key = 'tide'.sprintf('%02d', $i);
                $tide->$key = $data->$key;
            }
            for($i=1; $i<=4; $i++) {
                $key = 'max_time'.$i;
                $tide->$key = $data->max_time[$i] != 9999 ? \Func::toTime($data->max_time[$i]): NULL;
                $key = 'max_tide'.$i;
                $tide->$key = $data->max_tide[$i] != 999  ? $data->max_tide[$i]: NULL;
                $key = 'min_time'.$i;
                $tide->$key = $data->min_time[$i] != 9999 ? \Func::toTime($data->min_time[$i]): NULL;
                $key = 'min_tide'.$i;
                $tide->$key = $data->min_tide[$i] != 999  ? $data->min_tide[$i]: NULL;
            }
            $tide->save();
        }
    }

    public static function getDatas($place_id, $date_at, $week){
        $start_at = new \Datetime($date_at);
        $days = 7 * $week;
        $datas = Tide::where('place_id', $place_id)
                        ->where('date_at', '>=', $start_at->format('Y-m-d'))
                        ->orderBy('date_at', 'ASC')
                        ->limit($days)
                        ->get();

        return $datas;
    }

    public static function getSimpleDatas($tide_datas, $qreki){
        $datas = [];
        if(!empty($tide_datas)){
            foreach($tide_datas as $tide_data){
                $date_at = new \Datetime($tide_data->date_at);
                $data = new \stdClass();
                $data->date_at = $date_at;
                $suninfo = date_sun_info(
                    $date_at->format('U'),
                    $tide_data->lat,
                    $tide_data->lng
                );
                $data->sunrise = new \Datetime('@'.$suninfo['sunrise']);
                $data->sunset = new \Datetime('@'.$suninfo['sunset']);
                $moon_age = $qreki->get_moon($date_at->format('Y'), $date_at->format('n'), $date_at->format('j'), 12, 0, 0);
                $data->tide_name = $qreki->tidename($moon_age);
                $data->max1 = $tide_data->max_time1? substr($tide_data->max_time1, 0, -3): "-";
                $data->max2 = $tide_data->max_time2? substr($tide_data->max_time2, 0, -3): "-";
                $data->min1 = $tide_data->min_time1? substr($tide_data->min_time1, 0, -3): "-";
                $data->min2 = $tide_data->min_time2? substr($tide_data->min_time2, 0, -3): "-";
                $datas[ $date_at->format('Y-m-d') ] = $data;
            }
        }

        return $datas;
    }

    public static function getDetailDatas($tide_datas){
        $datas = [];
        if(!empty($tide_datas)){
            foreach($tide_datas as $tide_data){
                $data = $tide_data;
                $minmax = Tide::getMinMax($data);
                $data->min = $minmax->min;
                $data->max = $minmax->max;

                $datas[ $data->date_at->format('Y-m-d') ] = $data;
            }
        }

        return $datas;
    }

    static public function getMinMax($data){
        $minimum =  10000;
        $maximum = -10000;
        $min = $max = [];
        for($i = 0; $i <= 23; $i++){
            $key = 'tide' . sprintf('%02d', $i);
            $tide = $data->$key;
            if($tide < $minimum){
                $minimum = $tide;
                $min = $i;
            }
            if($tide > $maximum){
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
