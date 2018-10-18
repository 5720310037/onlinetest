<?php
include "../check-user.php";
if(!$_POST) {
	exit;
}
include "../dblink.php";
if($_POST['action'] == "add") {
	$lec_name = $_POST['lec_name'];
	$lec_id = $_POST['lec_id'];
	$lec_pwd = $_POST['lec_pwd'];

	$sql = "INSERT INTO lecturer VALUES('$lec_id', '$lec_name', '$lec_pwd')";
	mysqli_query($link, $sql);
}
if($_POST['action'] == "del") {
	$lec_id = $_POST['lec_id'];

	$sql = "DELETE FROM lecturer WHERE lec_id = '$lec_id'";
	mysqli_query($link, $sql);
} 
if($_POST['action'] == "edit") {
	$lec_name = $_POST['lec_name'];
	$lec_id = $_POST['lec_id'];
	$lec_pwd = $_POST['lec_pwd'];

	$sql = "UPDATE lecturer SET lec_name = '$lec_name', lec_pwd = '$lec_pwd'
			WHERE lec_id = '$lec_id'";
	mysqli_query($link, $sql);
}
mysqli_close($link);
?>