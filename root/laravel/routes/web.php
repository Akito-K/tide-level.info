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

Route::group(['middleware' => ['auth']], function () {
    Route::get('phpinfo', function () {
        phpinfo();
    });
});

Route::group(['prefix' => 'admin', 'namespace' => 'admin', 'middleware' => ['auth']], function () {
    Route::group(['prefix' => 'test'], function () {
        Route::get ('{year}/{place_id}', 'TideController@showTestData');
    });
});




Route::get ('',       'homeController@index');
Route::post('tide',   'homeController@tide');

Route::group(['prefix' => 'admin', 'namespace' => 'admin', 'middleware' => ['auth']], function () {
    Route::get ('',                             'homeController@dashboard');
    Route::get ('dashboard',                    'homeController@dashboard')     ->name('admin.dashboard');

    Route::group(['prefix' => 'tide'], function () {
        Route::get ('{year}',                     'TideController@showList')      ->name('admin.tide.list');
//        Route::get ('create',                   'TideController@create')        ->name('admin.tide.create');
//        Route::post('insert',                   'TideController@insert')        ->name('admin.tide.insert');
//        Route::get ('edit',                     'TideController@edit')          ->name('admin.tide.edit');
//        Route::post('update',                   'TideController@update')        ->name('admin.tide.update');
//        Route::post('delete',                   'TideController@delete')        ->name('admin.tide.delete');
    });

    Route::group(['prefix' => 'pagemeta'], function () {
        Route::get ('',                         'pagemetaController@showList');
        Route::get ('create',                   'pagemetaController@create');
        Route::post('confirm',                  'pagemetaController@confirm');
        Route::post('update',                   'pagemetaController@update');
    });
});

Route::group(['prefix' => 'ajax'], function () {
    Route::post('upload_file',                  'AjaxController@uploadFile');
    Route::post('change_places',                'AjaxController@changePlaces');
    Route::post('change_skin',                  'AjaxController@changeSkin');
});


//Route::get ('/get_tide_datas',   'cronController@getTideDatas');
//Route::get ('/save_tide_data',   'cronController@saveTideData');

/*
Route::get('/', function () {
    return view('welcome');
});
*/

