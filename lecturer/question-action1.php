<?php
include "../check-user.php";
$les_id = $_SESSION['les_id'];

//$msg = "";
//$img = "no.png";

if(!$_POST) {
	exit;
}
include "../dblink.php";
// code for add is not use yet
if($_POST['action'] == "add") {
	//$les_id = $_POST['les_id'];
	$ques_detail = $_POST['ques_detail'];
	$ques_level = $_POST['ques_level'];
	//$f = "";
	//$t = "";
	//$msg = "";

	if(is_uploaded_file($_FILES['file']['tmp_name']))  {
		$error =  $_FILES['file']['error'];
		if($error == 0) {
			include "lib/IMager/imager.php";
			$img = image_upload('file');
			$img = image_to_jpg($img);
			//$img = image_resize_max($img, 500, 200); //ให้ภาพกว้างไม่เกิน 200px สูงไม่เกิน 200px
			$f = image_store_db($img, "image/jpeg");
		}
		else if($error == 1 || $error == 2) {
			$msg = "ไฟล์ที่อัปโหลดมีขนาดใหญ่เกินกำหนด";
		}
		else if($error == 4) {
			$msg = "เกิดข้อผิดพลาดในระหว่างอัปโหลดไฟล์";
		}
	}

	if($msg == "") {
		$sql = "REPLACE INTO question VALUES('','$les_id','$ques_detail','$f','active','$ques_level')";
		if(mysqli_query($link, $sql)) {
			$ques_id = mysqli_insert_id($link);

			for($i = 1; $i <= count($_POST['choice']); $i++) {
				$choice_detail = $_POST['choice'][$i];
				$answer = "no";
				if($_POST['answer'] == $i) {
					$answer = "yes";
				}
				$sql = "REPLACE INTO choice VALUES('','$ques_id','$choice_detail','$answer')";
				mysqli_query($link, $sql);
			}
			$msg = 'บันทึกข้อมูลเรียบร้อยแล้ว<br>กรุณาใส่ข้อมูลคำถามถัดไป หรือกลับไปยัง <a href="index.php">หน้าหลัก</a>';
			$img = "ok.png";
		}
		else {
			$msg = "error";
		}
	}
}

// code for edit is not use yet
/*if($_POST['action'] == "edit") {
	$les_id = $_POST['les_id'];
	$les_name = $_POST['les_name'];

	$id = $les_id;
	$sql = "UPDATE lesson SET les_id = '$les_id', les_name = '$les_name' WHERE les_id = '$id'";
	mysqli_query($link, $sql);
} */

if($_POST['action'] == "del") {
	$id = $_POST['ques_id'];

	$sql = "DELETE FROM question WHERE ques_id = $id";
	mysqli_query($link, $sql);

	$sql = "DELETE FROM choice WHERE ques_id = $id";
	mysqli_query($link, $sql);
}

mysqli_close($link);
?>