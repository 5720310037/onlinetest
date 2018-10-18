<?php
include "../check-user.php";
//$group_id = $_SESSION['group_id'];

if(!$_POST) {
	exit;
}
include "../dblink.php";
if($_POST['action'] == "delete") {
	$group_id = $_POST['group_id'];

	$sql = "SELECT std_id FROM enrollment WHERE group_id = '$group_id'";
	$r   = mysqli_query($link, $sql);

	while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
		$std_id = $row['std_id'];

		$sql = "DELETE FROM enrollment WHERE std_id = '$std_id'";
		mysqli_query($link, $sql);

		$sql = "DELETE FROM take_exam WHERE std_id = '$std_id'";
		mysqli_query($link, $sql);

		$sql = "DELETE FROM std_ques WHERE std_id = '$std_id'";
		mysqli_query($link, $sql);

		$sql = "DELETE FROM student WHERE std_id = '$std_id'";
		mysqli_query($link, $sql);
	}
}

if($_POST['action'] == "import") {
	$group_id = $_POST['group_id'];
	$test_id  = $_POST['test_id'];

	$sql = "SELECT std_id FROM enrollment WHERE group_id = '$group_id'";
	$r   = mysqli_query($link, $sql);

	while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
		$std_id = $row['std_id'];

		$sql = "INSERT INTO take_exam VALUES('$std_id','$group_id','$test_id','')";
		mysqli_query($link, $sql);
	}
}

mysqli_close($link);
?>