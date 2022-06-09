<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;
//    protected $table = 'areas';

    public static function getNames(){
        $datas = self::orderBy('id', 'ASC')->pluck('name', 'id')->toArray();
        $ary = [ '' => 'すべての'];

        return $ary + $datas;
    }
}
