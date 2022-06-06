<?php
/*
	require_once("config.php");
	require_once("config_admin.php");
	require_once(DIR_LIB."class/Admin.class.php");
// データベース接続
  $nic = new nicDB(DB_US, DB_PW, DB_NM, DB_HT);
// Func クラス
  require_once(DIR_LIB.'class/Func.class.php');
  $func = new Func();

	if(count($_POST)>0){
		foreach($_POST as $k => $v){
			${$k} = $v;
		}
	}

	$rst = "";
	$point = 0;
	$query = 'SELECT i_point FROM blog_view WHERE blog_id = ? LIMIT 1';
	$stmt = $nic->prepare($query);
	$rst = $stmt->execute(array($blog_id));
	$ary = $stmt->fetch(PDO::FETCH_ASSOC);
	if($ary[i_point]){
		$point = $ary[i_point];
	}
	$point++;
	$query = 'UPDATE blog_view SET i_point = ? WHERE blog_id = ? LIMIT 1';
	$stmt = $nic->prepare($query);
	$rst = $stmt->execute(array($point, $blog_id));

//	echo $blog_id."=>".$point;
*/
?>