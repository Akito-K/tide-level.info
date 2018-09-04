<?php

use Illuminate\Database\Seeder;
use App\Model\MySpreadsheet;
use App\User;
use App\Model\Place;
use App\Model\Area;
use App\Model\Tide;

class DatabaseSeeder extends Seeder
{
    protected $datas;
    protected $MySpreadsheet;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->MySpreadsheet = new MySpreadsheet();

        $datas = $this->getDatas();
        $this->datas = $this->MySpreadsheet->getHashedDatas($datas);

        $this->MyUserSeeder();
        $this->PlaceSeeder();
        $this->AreaSeeder();
    }

    // エクセルからデータを取得して連想配列に
    public function getDatas() {
        $filepath = __DIR__.'/masters.xlsx';
        $datas = $this->MySpreadsheet ->getExcelData($filepath);

        return $datas;
    }

    public function MyUserSeeder(){
        $table_name = 'users';

        DB::table( $table_name )->delete();
        if( isset( $this->datas[ $table_name ])){
            $datas = $this->datas[ $table_name ];

            if(!empty($datas)){
                foreach($datas as $data){
                    User::create($data);
                }
            }
        }
    }

    public function PlaceSeeder(){
        $table_name = 'places';

        DB::table( $table_name )->delete();
        if( isset( $this->datas[ $table_name ])){
            $datas = $this->datas[ $table_name ];

            if(!empty($datas)){
                foreach($datas as $data){
                    Place::create($data);
                }
            }
        }
    }

    public function AreaSeeder(){
        $table_name = 'areas';

        DB::table( $table_name )->delete();
        if( isset( $this->datas[ $table_name ])){
            $datas = $this->datas[ $table_name ];

            if(!empty($datas)){
                foreach($datas as $data){
                    Area::create($data);
                }
            }
        }
    }

}
