<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Place;
use App\Library\Func;

class PlaceController extends Controller
{
    public $year_start = 2018;

    public function showList(){
        $area_names = Area::getNames();
        $years = range(date('Y'), $this->year_start);
        $datas = [];
        foreach($years as $year){
            $datas[$year] = Place::getAreaedPlaceDatas(true, $year);
        }

        return view('admin.place.list', compact('area_names', 'datas', 'years'));
    }

    public function getTide(Request $request) {

        return redirect(route('admin.place.lisst'));
    }

}
