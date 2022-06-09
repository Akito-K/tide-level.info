<?php

namespace App\Library;
use Google_Client;
use Google_Service_Sheets;

class GoogleClient {

    public static function Client(){
        $root = Func::getRootPath();
        $keyFile = $root . '/google_app/keys/tide-level-1529370489211-3dd398d51fb1.json';

        $client = new Google_Client();
        $client->setAuthConfig($keyFile);
        //任意名
        $client->setApplicationName("Sheet API");
        //サービスの権限スコープ
        $scopes = [Google_Service_Sheets::SPREADSHEETS];
        $client->setScopes($scopes);

        return $client;
    }


}
