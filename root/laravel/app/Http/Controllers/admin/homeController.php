<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\adminController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Pagemeta;

class homeController extends adminController
{

    public function dashboard(){
        $pagemeta = Pagemeta::getPagemeta('AD-HM-001');
//        $pagemeta->breadcrumbs = '<li><i class="fa fa-gear"></i> ダッシュボード</li>';

        return view('admin.home.dashboard', compact('pagemeta'));
    }

}
