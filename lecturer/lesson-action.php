<?php
include "../check-user.php";
$sub_id = $_SESSION['sub_id'];

if(!$_POST) {
	exit;
}
include "../dblink.php";
if($_POST['action'] == "add") {
	$les_name = $_POST['les_name'];
	//$sub_id = $_SESSION['sub_id'];
	$sql = "REPLACE INTO lesson VALUES('', '$sub_id', '$les_name')";
	mysqli_query($link, $sql);
}
if($_POST['action'] == "edit") {
	$les_id   = $_POST['les_id'];
	$les_name = $_POST['les_name'];

	$sql = "UPDATE lesson SET les_name = '$les_name' WHERE les_id = '$les_id'";
	mysqli_query($link, $sql);
}
if($_POST['action'] == "del") {
	$les_id = $_POST['les_id'];

	$sql = "SELECT ques_id FROM question WHERE les_id = '$les_id'";
	$r   = mysqli_query($link, $sql);
	while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
		$ques_id = $row['ques_id'];
		$sql = "DELETE FROM choice WHERE ques_id = '$ques_id'";
		mysqli_query($link, $sql);
	}

	$sql = "DELETE FROM question WHERE les_id = '$les_id'";
	mysqli_query($link, $sql);

	$sql = "DELETE FROM lesson WHERE les_id = '$les_id'";
	mysqli_query($link, $sql);
}

mysqli_close($link);
?>