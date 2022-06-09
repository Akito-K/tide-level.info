<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Area;
use App\Models\Place;
use App\Models\Tide;
use App\Library\Func;
use App\Library\Qreki;

class HomeController extends Controller
{
    public function index(Request $request){
        $area_names = Area::getNames();
        $place_datas = Place::orderBy('id', 'ASC')->get();

        $json_place_datas = Place::toPlaceJson( Place::orderBy('id', 'ASC')->get() );
        $place_names = Place::getNames($place_datas, $area_names);
//        dd($area_names, $place_names);
//        $skin = Func::setSkin($request);
        $place_code = 'AK';
        $place_data = Place::where('code', $place_code)->first();
        $skin = 'light';

        return view('common.home.index', compact( 'skin', 'area_names', 'place_names', 'json_place_datas', 'place_data'));
    }

    public function tide(Request $request){
        $area_id = $request->area_id;
        $place_id = $request->place_id;
        $date_at = $request->hide_date_at;
        $week = $request->week;
        $place = Place::where('id', $place_id)->first();

        $tide_datas = Tide::getDatas2($place, $date_at, $week);

        $qreki = new Qreki();
        $datas = Tide::getTideDatas($tide_datas, $qreki);

        $area_names = Area::getNames();
        $place_datas = Place::getDatasByAreaId($area_id);
        $json_place_datas = Place::toPlaceJson( Place::orderBy('id', 'ASC')->get() );
        $place_names = Place::getNames($place_datas, $area_names);
        $place_data = Place::where('id', $place_id)->first();
        $skin = 'light';

        return view('common.home.tide', compact('skin', 'area_names', 'place_names', 'json_place_datas', 'datas', 'place_data'));
    }
}
