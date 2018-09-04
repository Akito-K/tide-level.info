<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Upload extends Model
{
    use SoftDeletes;
    protected $table = 'uploads';
    protected $dates = ['deleted_at'];
    protected $guarded = ['id'];

    public static function getNewId(){
        $date_at = new \Datetime();
        $new_id = 'UP-'.$date_at->format('ymd-His-').\Func::getRandStr("Aa0", 5);

        return $new_id;
    }

    /**
     * @param
     * @return
     */
    // データ取得
    public static function getData($upload_id){
        $data = Upload::where('upload_id', $upload_id)->orderBy('id', 'DESC')->first();

        return $data;
    }

    // アップロード先のディレクトリを取得
    public static function getDirectories($target){
        $today = new \Datetime();
        $dir = dirname(dirname(dirname(__DIR__))).'/html';
        if(!file_exists($dir)){
            mkdir($dir);
        }

        switch($target){
            case 'facility':
                $path = '/img/facility';
                break;

            case 'user':
                $path = '/img/user';
                break;

            case 'mail':
                $path = '/img/mail';
                break;

            case 'pagemeta':
                $path = '/pagemeta';
                break;

            default:
                $path = '/img/tmp';
                break;
        }
        if(!file_exists($dir.$path)){
            mkdir($dir.$path);
        }

        $path .= '/'.$today->format('Y');
        if(!file_exists($dir.$path)){
            mkdir($dir.$path);
        }

        $path .= '/'.$today->format('m');
        if(!file_exists($dir.$path)){
            mkdir($dir.$path);
        }

        return ['fullpath' => $dir.$path, 'path' => $path];
    }

    /**
     * @param Request
     * @param string: 移動先の絶対パス
     * @param string: foo.jpg
     */
    public static function saveFromUpload($request, $dir, $filename){
        $file = $request->file('file')->move($dir, $filename);
    }

    /**
     * @param string
     * @return int
     */
    public static function getWidth($size){
        $widthes = ['lg' => 1280, 'md' => 640, 'sm' => 320];

        return $widthes[ $size ];
    }

    /**
     * @param object( width, from_url, from_fullpath, to_fullpath, ...)
     * from_url は使わない（負債）
     */
    public static function resizeAndSave($width, $from_fullpath, $to_fullpath){
        $image = \Image::make($from_fullpath);
        $image->resize($width, null, function($constraint){
            $constraint->aspectRatio();
        });
        $image->orientate();

        $image->save($to_fullpath);
        unset($image);
    }

}
