<?php

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

Auth::routes();

Route::get ('/',       'homeController@index');
Route::post('/tide',   'homeController@tide');

Route::group(['middleware' => ['auth']], function () {
    Route::get ('/admin',                                   'admin\homeController@dashboard');
    Route::get ('/admin/dashboard',                         'admin\homeController@dashboard');

    Route::get ('/admin/pagemeta',                          'admin\pagemetaController@showList');
    Route::get ('/admin/pagemeta/create',                   'admin\pagemetaController@create');
    Route::post('/admin/pagemeta/confirm',                  'admin\pagemetaController@confirm');
    Route::post('/admin/pagemeta/update',                   'admin\pagemetaController@update');
});

Route::post('/ajax/upload_file',                            'AjaxController@uploadFile');
Route::post('/ajax/change_places',                          'AjaxController@changePlaces');
Route::post('/ajax/change_skin',                            'AjaxController@changeSkin');

//Route::get ('/get_tide_datas',   'cronController@getTideDatas');
//Route::get ('/save_tide_data',   'cronController@saveTideData');

/*
Route::get('/', function () {
    return view('welcome');
});
*/

