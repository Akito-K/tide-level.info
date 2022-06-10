<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Library\GoogleClient;
use Google_Service_Sheets;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */

    protected $sheet_id = '1Ouf1SbmeUK3sfta1XVbIUovzbz7pd9PDiWILDDxXSwM';
    private $refresh = true;
//    private $refresh = true;


    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);


        // アカウント認証情報インスタンスを作成
        $client = GoogleClient::Client();

        //シート情報を操作するインスタンスを生成
        $service = new Google_Service_Sheets($client);

        $response = $service->spreadsheets->get($this->sheet_id);
        $sheets = $response->getSheets();
        $sheet_titles = [];
        if(!empty($sheets)){
            foreach($sheets as $sheet){
                $title = $sheet->properties->title;
                $sheet_titles[] = $title;
            }
        }

        if(!empty($sheet_titles)){
            foreach($sheet_titles as $sheet_title){
                $rows = $service->spreadsheets_values->get($this->sheet_id, $sheet_title);
                $keys = $rows->values[0];
                if(!empty($rows->values)) {
                    for($i = 1; $i < count($rows->values); $i++) {
                        $row = $rows->values[$i];
                        $this->datas[$sheet_title][] = $this->getSheetData($row, $keys);
                    }
                }
            }
        }

        $this->MySeeder('App\Models\Area');
        $this->MySeeder('App\Models\Place');
        $this->MySeeder('App\Models\User');


    }

    public function getSheetData($row, $keys){
        $data = [];
        if(!empty($keys)){
            foreach($keys as $i => $key){
                if( isset($row[$i]) ){
                    $val = $row[$i];
                }else{
                    $val = NULL;
                }

                $data[$key] = $this->formatValue($key, $val);
            }
        }

        return $data;
    }

    public function formatValue($key, $val){
        if( $val === '' ){
            $val = NULL;
        }
        if(preg_match('/\_at$/', $key) && $val){
            $val = new \Datetime($val);
        }
        if($key === 'password' && $val){
            $val = bcrypt($val);
        }

        return $val;
    }

    public function MySeeder($model_name){
        $model = new $model_name;
        $table_name = $model->getTable();

        if( isset( $this->datas[ $table_name ])){

            if($this->refresh){
                // 一旦空にする
                DB::table( $table_name )->delete();
            }

            $datas = $this->datas[ $table_name ];
            if(!empty($datas)){
                foreach($datas as $sheet_title => $data){
                    $model->create($data);
                }
            }
        }
    }

}
