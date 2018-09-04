<?php

namespace App\Http\Controllers;
use App\Model\Tide;
use App\Model\Place;

class cronController extends Controller
{
    public static function getTideDatas(){
        $place_datas = Place::getDatas();
        foreach($place_datas as $place_data){
            $place_id = $place_data->place_id;
            $url = 'https://www.data.jma.go.jp/kaiyou/data/db/tide/suisan/txt/'.date('Y').'/'.$place_id.'.txt';
            \Func::getTextFile($url, $place_id);
        }
    }

    public static function saveTideData(){
        $root_dir = \Func::getRootPath();
        $path = $root_dir.'/tide_datas/'.date('Y');
        $place_datas = Place::getDatas();
        foreach($place_datas as $place_data) {
            $place_id = $place_data->place_id;
            $is_exists = Tide::isExist($place_id, date('Y'));
            if( !$is_exists ){
                $filepath = $path.'/'.$place_id.'.txt';
                if( file_exists($filepath) ) {
                    $ary = file($filepath);
                    $datas = Tide::toTideDatas($ary);
                    //\Func::var_dump($datas);
                    Tide::saveDatas($datas);
                    exit;
                }
            }
        }
    }
}

