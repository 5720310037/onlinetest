<?php
include "check-user.php";

include "dblink.php";
if($_POST['action'] == "add"){
	$lec_name = $_POST['lec_name'];
	$lec_id = $_POST['lec_id'];
	$lec_pwd = $_POST['lec_pwd'];
	$sql = "REPLACE INTO lecturer VALUES('$lec_id', '$lec_name', '$lec_pwd')";
	mysqli_query($link, $sql);
}

mysqli_close($link);
?>