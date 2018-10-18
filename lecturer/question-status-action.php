<?php 
sleep(1);
include "../dblink.php";
if($_POST) {
	$action = $_POST['action'];
	$id     = $_POST['id'];

	if($action == "Active") {
		$sql = "UPDATE question SET ques_status = 'inactive' WHERE ques_id = '$id'";
		mysqli_query($link, $sql);
	} 
	elseif ($action == "Inactive") {
		$sql = "UPDATE question SET ques_status = 'active' WHERE ques_id = '$id'";
		mysqli_query($link, $sql);
	}
	elseif ($action == "Delete") {
		// If 'Delete', will be deleted all relative table
		$sql = "DELETE FROM choice WHERE ques_id = '$id'";
		mysqli_query($link, $sql);
		
		$sql = "DELETE FROM question WHERE ques_id = '$id'";
		mysqli_query($link, $sql);
	}
	mysqli_lose($link);
}
?>