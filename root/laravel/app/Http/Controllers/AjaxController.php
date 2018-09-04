<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Upload;
use App\Model\Area;
use App\Model\Place;

class AjaxController extends Controller
{

    public function uploadFile(Request $request){
        $upload_id         = Upload::getNewId();
        $original_filename = $request->input('name');
        $extension         = $request->input('ext');
        $type              = $request->input('type');
        $target            = $request->input('target');
        $posted            = $request->file('file')->isValid();

        if($posted){
            $filename = $upload_id.'.'.$extension;
            // アップロード先のディレクトリを取得
            $dirs = Upload::getDirectories($target);

            // オリジナルファイルを保存
            Upload::saveFromUpload($request, $dirs['fullpath'], $filename);

            // DB Insert
            Upload::insert([
                'upload_id' => $upload_id,
                'dirpath' => $dirs['path'],
                'extension' => $extension,
                'original_filename' => $original_filename,
                'created_at' => new \Datetime(),
                'updated_at' => new \Datetime(),
                ]);

            if($type == "image"){
                // 3サイズにリサイズして保存
                foreach(['lg', 'md', 'sm'] as $size){
                    $width = Upload::getWidth($size);
                    $from_fullpath = $dirs['fullpath'].'/'.$filename;
                    $to_fullpath = $dirs['fullpath'].'/'.$upload_id.'_'.$size.'.'.$extension;
                    Upload::resizeAndSave( $width, $from_fullpath, $to_fullpath );
                }

                $data = [
                    'path' => $dirs['path'],
                    'filename' => $upload_id.'_sm.'.$extension,
                    'uploaded_id' => $upload_id,
                    ];

            }else{
                $data = [
                    'path' => $dirs['path'],
                    'filename' => $upload_id.'_sm.'.$extension,
                    'uploaded_id' => $upload_id,
                    'original_filename' => $original_filename,
                ];
            }

            return json_encode($data);
        }
    }

    public function changePlaces(Request $request){
        $area_id = $request['area_id'];
        $area_names = Area::getNames();
        if($area_id){
            $place_datas = Place::getDatasFromAreaId($area_id);
        }else{
            $place_datas = Place::getDatas();
        }
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
}
