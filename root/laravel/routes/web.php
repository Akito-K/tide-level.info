<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\Admin\AdminHomeController;
use App\Http\Controllers\Admin\PlaceController;
use App\Http\Controllers\Admin\TideController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// 公開部分
Route::get ('',       [HomeController::class, 'index'])     ->name('home');
Route::get ('top',    [HomeController::class, 'index'])     ->name('home.top');
Route::post('tide',   [HomeController::class, 'tide'])      ->name('home.tide');

//Route::get('/', function () {
//    return view('welcome');
//});

//Route::get('/dashboard', function () {
//    return view('dashboard');
//})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

// 管理ページ
Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {

    Route::get ('',                         [AdminHomeController::class, 'dashboard'])       ->name('admin');
    Route::get ('dashboard',                [AdminHomeController::class, 'dashboard'])       ->name('admin.dashboard');

    // 地点一覧
    Route::get ('place',                    [PlaceController::class, 'showList'])       ->name('admin.place.list');
});


// Ajax
Route::group(['prefix' => 'ajax'], function () {
    Route::post('upload_file',              [AjaxController::class, 'uploadFile']);
    Route::post('change_places',            [AjaxController::class, 'changePlaces']);
    Route::post('change_skin',              [AjaxController::class, 'changeSkin']);
    Route::post('get_tide_data',            [AjaxController::class, 'getTideData']);
    Route::post('get_yearly_tide_datas',    [AjaxController::class, 'getYearlyTideDatas']);
});
