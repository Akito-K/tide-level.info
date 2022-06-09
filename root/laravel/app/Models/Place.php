<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Library\Func;

class Place extends Model
{
    use HasFactory;
//    protected $table = 'places';

    public static function getAreaedPlaceDatas($has_file = false, $year = null){
        if(!$year){ $year = date('Y'); }

        $ary = [];
        $places = Place::orderBy('area_id', 'ASC')->get()->keyBy('id');
        if( !empty($places) ){
            foreach($places as $place_id => $place){
                if($has_file){
                    $place->has_file = self::hasTideFile($year, $place->code);
                }
                $ary[ $place->area_id ][$place_id] = $place;
            }
        }

        return $ary;
    }

    public static function toPlaceJson($datas){
        $ary = [];
        foreach($datas->toArray() as $key => $data){
            $data['lat'] = Func::mylatlng_format($data['lat']);
            $data['lng'] = Func::mylatlng_format($data['lng']);
            $ary[$key] = $data;
        }

        return json_encode($ary);
    }

    public static function getNames($place_datas, $area_names){
        $ary = [];
        foreach($place_datas as $place_data){
            if(isset($area_names[$place_data->area_id])){
                $ary[$place_data->id] = $area_names[$place_data->area_id].' '.$place_data->name;
            }
        }

        return $ary;
    }

    public static function getPlaceNames(){
        $datas = self::orderBy('id', 'ASC')->get()->pluck('name', 'code');

        return $datas;
    }

    public static function getDatasByAreaId($area_id){
        if($area_id){
            $datas = self::where('area_id', $area_id)->orderBy('id', 'ASC')->get();
        }else{
            $datas = self::orderBy('id', 'ASC')->get();
        }

        return $datas;
    }

    public static function hasTideFile($year, $code) {
        $root = Func::getRootPath();
        $filepath = $root . '/tide_datas/' . $year . '/' . $code . '.txt';

        return file_exists($filepath);
    }
}
