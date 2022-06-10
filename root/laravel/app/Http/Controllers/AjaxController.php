<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Area;
use App\Models\Place;
use App\Library\Func;

class AjaxController extends Controller
{

    public function changePlaces(Request $request){
        $area_id = $request['area_id'];
        $area_names = Area::getNames();
        $place_datas = Place::getDatasByAreaId($area_id);
        $place_names = Place::getNames($place_datas, $area_names);
        $body = \Form::select('place_id', $place_names).' の潮位を';

        return json_encode([ 'view' => $body]);
    }

    public function changeSkin(Request $request){
        $skin = $request['skin'];
        $request->session()->put('skin', $skin);

        return  json_encode([
            'view' => view('include.skin', compact('skin') )->render(),
        ]);
    }

    public function getTideData(Request $request){
        $year       = $request['year'];
        $place_code = $request['place_code'];
        $rst = $this->downloadTideData($year, $place_code);

        return  json_encode($rst);
    }

    public function getYearlyTideDatas(Request $request){
        $year       = $request['year'];
        $places = Place::getAreaedPlaceDatas(true, $year);
        $results = [];
        $has_error = false;
        foreach($places as $area){
            foreach($area as $place) {
                if($place->has_file){
                    continue;
                }
                $result = $this->downloadTideData($year, $place->code);
                if($result['error']){
                    $has_error = true;
                }
                $results[] = $result;
            }
        }

        return  json_encode([
            'has_error' => $has_error,
            'results' => $results,
        ]);
    }

    public function downloadTideData($year, $place_code){
        $root = Func::getRootPath();
        $filepath = $root . '/tide_datas/' . $year;
        if( !file_exists($filepath) ){
            mkdir($filepath);
        }
        $filepath .= '/' . $place_code . '.txt';

        $url = 'https://www.data.jma.go.jp/kaiyou/data/db/tide/suisan/txt/' . $year . '/' . $place_code . '.txt';

        $error = '';
        $tmp = @file_get_contents($url);
        if ( !$tmp ){
            $error = "URL({$url})からダウンロードできませんでした。";
        }else{
            $fp = fopen($filepath, 'w');
            fwrite($fp, $tmp);
            fclose($fp);
        }

        return [
            'view' => file_exists($filepath) ? '◯':'x',
            'error' => $error,
            'year' => $year,
            'place_code' => $place_code,
        ];
    }


}
