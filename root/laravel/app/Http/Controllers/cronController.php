<?php

namespace App\Http\Controllers;
use App\Model\Tide;
use App\Model\Place;
use MyFacade\MyFunctions as Func;

class cronController extends Controller
{
    public static function setNewTideFiles($year){
        $place_datas = Place::getDatas();
        foreach($place_datas as $place_data){
            $place_id = $place_data->place_id;
            $data = Func::getNewTideTextData($place_id, $year);
            Func::setNewTideTextFile($data, $place_id, $year);
        }
    }

    public static function saveTideData($year){
        $root_dir = Func::getRootPath();
        $saved_dirpath = $root_dir . '/tide_datas/' . $year;
        if(!file_exists($saved_dirpath)){
            exit;
        }

        $place_datas = Place::getDatas();
        foreach($place_datas as $place_data) {
            $place_id = $place_data->place_id;
            $is_exists = Tide::isExist($place_id, $year);

            if( !$is_exists ){
                $filepath = $saved_dirpath . '/' . $place_id . '.txt';
                if( file_exists($filepath) ) {
                    echo $place_id . '（' . $place_data->name . '）';
                    $ary = file($filepath);
                    $datas = Tide::toTideDatas($ary);
                    Tide::saveDatas($datas);
                    exit;
                }
            }
        }
    }
}

