<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\TideRequest as MyRequest;
use App\Model\Pagemeta;
use App\Model\Area;
use App\Model\Place;
use App\Model\Tide;

class homeController extends Controller
{
    public function index(Request $request){
        $pagemeta = Pagemeta::getPagemeta('CM-HM-001');
        $area_names = Area::getNames();
        $place_datas = Place::getDatas();
        $json_place_datas = Place::toPlaceJson($place_datas);
        $place_names = Place::getNames($place_datas, $area_names);
        $skin = \Func::setSkin($request);
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

        $tide_datas = Tide::getDatas($place_id, $date_at, $week);
        $qreki = new \Qreki();
        $simple_datas = Tide::getSimpleDatas($tide_datas, $qreki);
        $detail_datas = Tide::getDetailDatas($tide_datas);
//        \Func::var_dump($simple_datas);
//        \Func::var_dump($detail_datas);

        $area_names = Area::getNames();
        if($area_id){
            $place_datas = Place::getDatasFromAreaId($area_id);
        }else{
            $place_datas = Place::getDatas();
        }
        $json_place_datas = Place::toPlaceJson( Place::getDatas() );
        $place_names = Place::getNames($place_datas, $area_names);
        $place_data = Place::getData($place_id);
        $skin = \Func::setSkin($request);

        return view('common.home.tide', compact('pagemeta','skin', 'area_names', 'place_names', 'json_place_datas', 'simple_datas', 'detail_datas', 'place_data'));
    }
}
