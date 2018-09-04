<?php
  header("Content-type: application/x-javascript");
  include_once("/home/a2public/lib/tide/core/config.php");
// データベース接続
  $nic = new nicDB(DB_US, DB_PW, DB_NM, DB_HT);
// 位置情報
  $query = "SELECT * FROM tide_place";
  $stmt = $nic->prepare($query);
  $stmt->execute();
  $ary = array();
  while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $ary[$row[place_id]] = $row;
  }

  echo '  var locations = ['.NL;
  if(!empty($ary)){
    foreach($ary as $a){
      if($a[place_id] == $_GET[p]){
        $icon = "/img/map_icon_99.png";
      }else{
        $icon = "/img/map_icon_98.png";
      }
      echo '    {"lat":"'.mylatlng_format($a[t_lat]).'", "lng":"'.mylatlng_format($a[t_lng]).'", "id":"'.$a[place_id].'", "title":"'.$a[place_name].'", "type":"2", "icon":"'.$icon.'", category: 1},'.NL;
    }
  }
  echo '  ];'.NL;
?>

  var centerLat = <?=mylatlng_format($ary[$_GET[p]][t_lat])?>;
  var centerLng = <?=mylatlng_format($ary[$_GET[p]][t_lng])?>;
