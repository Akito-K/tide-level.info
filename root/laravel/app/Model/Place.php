<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    public static function getDatas(){
        $datas = Place::orderBy('id', 'ASC')->get();

        return $datas;
    }

    public static function getData($place_id){
        $data = Place::where('place_id', $place_id)->first();

        return $data;
    }

    public static function getDatasFromAreaId($area_id){
        $datas = Place::where('area_id', $area_id)->orderBy('place_id', 'ASC')->get();

        return $datas;
    }

    public static function toPlaceJson($datas){
        $ary = [];
        foreach($datas->toArray() as $key => $data){
            $data['lat'] = \Func::mylatlng_format($data['lat']);
            $data['lng'] = \Func::mylatlng_format($data['lng']);
            $ary[$key] = $data;
        }

        return json_encode($ary);
    }

    public static function getNames($datas, $area_names){
        $ary = [];
        foreach($datas as $data){
            if(isset($area_names[$data->area_id])){
                $ary[$data->place_id] = $area_names[$data->area_id].' '.$data->name;
            }
        }

        return $ary;
    }

}
