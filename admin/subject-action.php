<?php
include "../check-user.php";
if(!$_POST) {
	exit;
}
include "../dblink.php";
if($_POST['action'] == "add") {
	$sub_id    = $_POST['sub_id'];
	$thai_name = $_POST['thai_name'];
	$eng_name  = $_POST['eng_name'];

	$sql = "INSERT INTO subject VALUES('$sub_id', '$thai_name', '$eng_name')";
	mysqli_query($link, $sql);
}
if($_POST['action'] == "edit") {
	$sub_id    = $_POST['sub_id'];
	$thai_name = $_POST['thai_name'];
	$eng_name  = $_POST['eng_name'];

	$sql = "UPDATE subject SET thai_name = '$thai_name', eng_name = '$eng_name' 
			WHERE sub_id = '$sub_id'";
	mysqli_query($link, $sql);
}
if($_POST['action'] == "del") {
	$sub_id = $_POST['sub_id'];
	$sql = "DELETE FROM subject WHERE sub_id = '$sub_id'";
	mysqli_query($link, $sql);
} 
mysqli_close($link);
?>