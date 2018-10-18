<?php
include "../check-user.php";
if(!$_POST) {
	exit;
}
include "../dblink.php";

if($_POST['action'] == "del") {
	$group_id = $_POST['gr_id'];

	$sql = "SELECT std_id FROM enrollment WHERE group_id = '$group_id'";
	$row = mysqli_query($link, $sql);

	while ($row = mysqli_fetch_array($row, MYSQLI_ASSOC)) {
		$std_id = $row['std_id'];

		$sql = "DELETE FROM std_ques WHERE std_id = '$std_id'";
		mysqli_query($link, $sql);

		$sql = "DELETE FROM student WHERE std_id = '$std_id'";
		mysqli_query($link, $sql);
	}

	$sql = "DELETE FROM take_exam WHERE gr_id = '$group_id'";
	mysqli_query($link, $sql);

	$sql = "DELETE FROM enrollment WHERE group_id = '$group_id'";
	mysqli_query($link, $sql);

	$sql = "DELETE FROM tb_datetest WHERE group_id = '$group_id'";
	mysqli_query($link, $sql);

	$sql = "DELETE FROM lec_group WHERE gr_id = '$group_id'";
	mysqli_query($link, $sql);
}

if($_POST['action'] == "edit") {
	$gr_id  = $_POST['gr_id'];
	$lec_id = $_POST['lec_id'];

	$sql = "UPDATE lec_group SET lec_id  = '$lec_id' WHERE gr_id = '$gr_id'";
	mysqli_query($link, $sql);
}
mysqli_close($link);
?>