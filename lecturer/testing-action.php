<?php
include "../check-user.php";
//$sub_id = $_SESSION['sub_id'];

if(!$_POST) {
	exit;
}
include "../dblink.php";
if($_POST['action'] == "add") {
	$test_name 	= $_POST['test_name'];
	$term 		= $_POST['term'];
	$year 		= $_POST['year'];
	$detail 	= $_POST['detail'];
	$sub_id 	= $_POST['sub_id'];

	$sql = "REPLACE INTO testing VALUES('', '$test_name', '$term', '$year', '$detail', '$sub_id')";
	mysqli_query($link, $sql);
}

if($_POST['action'] == "edit") {
	$test_id    = $_POST['test_id'];
	$test_name 	= $_POST['test_name'];
	$detail 	= $_POST['detail'];

	$sql = "UPDATE testing SET test_name = '$test_name', detail = '$detail' 
			WHERE test_id = '$test_id'";
	mysqli_query($link, $sql);
}

if($_POST['action'] == "del") {
	$test_id = $_POST['test_id'];
	
	$sql = "DELETE FROM std_ques WHERE test_id = '$test_id'";
	mysqli_query($link, $sql);

	$sql = "DELETE FROM test_ques WHERE test_id = '$test_id'";
	mysqli_query($link, $sql);

	$sql = "DELETE FROM tb_datetest WHERE test_id = '$test_id'";
	mysqli_query($link, $sql);

	$sql = "DELETE FROM take_exam WHERE test_id = '$test_id'";
	mysqli_query($link, $sql);

	$sql = "DELETE FROM testing WHERE test_id = '$test_id'";
	mysqli_query($link, $sql);
}

mysqli_close($link);
?>