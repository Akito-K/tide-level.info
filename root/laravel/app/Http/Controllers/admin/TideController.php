<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\adminController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Pagemeta;
use App\Model\Tide;
use App\Model\Place;
use MyFacade\MyFunctions as Func;

class TideController extends adminController
{
    public $years = [2018, 2019, 2020];

    public function showTestData($year, $place_id){
    }

    public function showList($year){
        $pagemeta = Pagemeta::getPagemeta('AD-HM-001');
        $place_names = Place::getPlaceNames();
        $datas = [];
        foreach($place_names as $place_id => $name){
            $datas[$place_id] = Tide::getFileData($year, $place_id);
        }

        return view('admin.tide.list', compact('pagemeta', 'datas', 'place_names'));
    }

}
