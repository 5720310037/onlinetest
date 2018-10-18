<?php
include "../check-user.php";
$test_id  = $_SESSION['test_id'];
$group_id = $_SESSION['group_id'];
if(!$_POST) {
	exit;
}
include "../dblink.php";
if($_POST['action'] == "add") {
	$date_test  = $_POST['date'];
	$time_start = $_POST['time_start'];
	$time_end   = $_POST['time_end'];
	  
	$sql = "REPLACE INTO tb_datetest VALUES(
	        '$test_id', '$group_id', '$date_test', '$time_start', '$time_end')";
	mysqli_query($link, $sql);
}
mysqli_close($link);
?>