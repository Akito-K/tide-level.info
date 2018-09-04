<?php
  include_once("/home/a2public/lib/tide/core/config.php");
  if(!empty($_POST)){
    foreach($_POST as $k => $v){
      ${$k} = $v;
    }
  }

  if($script == "change_calendar"){
  // カレンダー
    $calendar = new Calendar();
    $y = $_POST[y];
    $m = $_POST[m];
    $body = $calendar->tbl_calendar($y, $m);
    echo $body;
  }elseif($script == "save_skin"){
  // スキンを保存
    if($skin == "dark"){
      $save_skin = "light";
    }else{
      $save_skin = "dark";
    }
    $_SESSION[config][skin] = $save_skin;
    echo $save_skin;
  }elseif($script == "change_select_place_option"){
  // エリアから地点選択肢入れ替え
    $nic = new nicDB(DB_US, DB_PW, DB_NM, DB_HT);
    $tide = new Tide();
    $html = $tide->HTML_selectSearchPlaceByArea($area_id);
    echo $html;
  }




?>