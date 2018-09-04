<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MySpreadsheet extends Model
{
    // エクセルを連想配列に変換
    public function getExcelData($filepath){
        $ext = \Func::getExtension($filepath);
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader( ucfirst($ext) );
        $spreadsheet = $reader->load($filepath);

        return $this->getSheets($spreadsheet);
    }

    // エクセルデータをシート毎の連想配列に格納
    public function getSheets($xlsObject){
        $datas = [];
        for ($i = 0; $i < $xlsObject->getSheetCount(); $i++) {
            $xlsObject->setActiveSheetIndex($i);
            $xlsSheet = $xlsObject->getActiveSheet();
            // シート名
            $sheetTitle = $xlsSheet->getTitle();
            $xlsSheet = $xlsObject->getSheetByName($sheetTitle)->toArray(null,true,true,true);
            $datas[$sheetTitle] = $this->getSheetBody($xlsSheet);
        }

        return $datas;
    }

    // シートの中身を連想配列に格納
    public function getSheetBody($xlsSheet){
        $datas = [];
        if( count($xlsSheet) >= 2){
            $keys = $xlsSheet[1];
            $j = 0;
            foreach($xlsSheet as $i => $xlsRow){
                if( $i != 1 && !empty($xlsRow)){
                    foreach($xlsRow as $k => $cell){
                        if($keys[$k] && $cell !== NULL){
                            $datas[$j][ $keys[$k] ] = $cell;
                        }
                    }
                    $j++;
                }
            }
        }

        return $datas;
    }

    // 取得データの 'password' をハッシュ化して元の連想配列に
    public function getHashedDatas($datas) {
        $hashed_datas = [];
        if(!empty( $datas )){
            foreach( $datas as $sheet_name => $data ){
                if(!empty($data)){
                    $hashed_data = [];
                    foreach( $data as $num => $row ){
                        if(!empty($row)){
                            $hashed_row = [];
                            foreach( $row as $k => $v ){
                                if( $k == 'password' ){
                                    $hashed_row[$k] = bcrypt($v);
                                }else{
                                    $hashed_row[$k] = $v;
                                }
                            }
                            $hashed_data[$num] = $hashed_row;
                        }
                    }
                    $hashed_datas[$sheet_name] = $hashed_data;
                }
            }
        }

        return $hashed_datas;
    }
}
