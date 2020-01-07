<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\TideRequest as MyRequest;
use App\Model\Pagemeta;
use App\Model\Area;
use App\Model\Place;
use App\Model\Tide;
use MyFacade\MyFunctions as Func;

class homeController extends Controller
{
    public function index(Request $request){
        $pagemeta = Pagemeta::getPagemeta('CM-HM-001');
        $area_names = Area::getNames();
        $place_datas = Place::getDatas();
        $json_place_datas = Place::toPlaceJson($place_datas);
        $place_names = Place::getNames($place_datas, $area_names);
        $skin = Func::setSkin($request);
        $place_id = 'AK';
        $place_data = Place::getData($place_id);

        return view('common.home.index', compact('pagemeta', 'skin', 'area_names', 'place_names', 'json_place_datas', 'place_data'));
    }

    public function tide(MyRequest $request){
        $pagemeta = Pagemeta::getPagemeta('CM-HM-002');
        $area_id = $request['area_id'];
        $place_id = $request['place_id'];
        $date_at = $request['hide_date_at'];
        $week = $request['week'];
        $place = Place::getData($place_id);

        $tide_datas = Tide::getDatas2($place, $date_at, $week);
//        Func::var_dump($tide_datas);

        $qreki = new \Qreki();
        $datas = Tide::getTideDatas($tide_datas, $qreki);

        $area_names = Area::getNames();
        if($area_id){
            $place_datas = Place::getDatasFromAreaId($area_id);
        }else{
            $place_datas = Place::getDatas();
        }
        $json_place_datas = Place::toPlaceJson( Place::getDatas() );
        $place_names = Place::getNames($place_datas, $area_names);
        $place_data = Place::getData($place_id);
        $skin = Func::setSkin($request);

        return view('common.home.tide', compact('pagemeta','skin', 'area_names', 'place_names', 'json_place_datas', 'datas', 'place_data'));
    }
}
