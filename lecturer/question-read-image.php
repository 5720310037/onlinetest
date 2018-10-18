<?php
	include "../dblink.php";
	$ques_id = $_GET['ques_id'];
	$sql = "SELECT ques_image FROM question WHERE ques_id = $ques_id";
	$r = mysqli_query($link, $sql);
	$data = mysqli_fetch_array($r);
	header("Contend-Type: image/jpeg");
	echo $data['ques_image'];
	mysqli_close($link);
?>