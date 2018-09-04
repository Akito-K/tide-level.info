<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    public static function getDatas(){
        $datas = Area::orderBy('id', 'ASC')->get();

        return $datas;
    }

    public static function getData($area_id){
        $data = Area::where('area_id', $area_id)->first();

        return $data;
    }

    public static function getNames(){
        $datas = Area::orderBy('area_id', 'ASC')->pluck('name', 'area_id')->toArray();
        $ary = [ '' => 'すべての'];

        return $ary + $datas;
    }

}
