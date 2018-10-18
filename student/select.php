<?php
include "../check-user.php";
if(!isset($_SESSION['std_id'])) {
	exit;
}

include "../dblink.php";

$group_id  = $_SESSION['group_id'];
$std_id    = $_SESSION['std_id'];

$test_id   = $_POST['test_id'];
$ques_id   = $_POST['ques_id'];
$choice_id = $_POST['choice_id'];

//ป้องกันการทำแบบทดสอบหัวเดิมซ้ำ(อาจเกิดกรณีผู้ใช้เปิดหลายเบราเซอร์พร้อมกัน)
$sql = "SELECT score FROM take_exam
 		WHERE std_id = '$std_id' AND group_id = '$group_id' AND test_id = '$test_id'";
$result = mysqli_query($link, $sql);
$row 	= mysqli_fetch_array($result);
if($row[0] != "") {
	mysqli_close($link);
	exit("alert('ท่านได้ทำแบบสอบทดสอบนี้ไปแล้ว ไม่สามารถทำซ้ำได้อีก');
			window.location = 'index.php';");
}

//ตรวจสอบว่าอยู่ในช่วงวันเวลาที่กำหนดในการทำแบบทดสอบหรือไม่
$sql = "SELECT date_test, time_start, time_end
 		FROM tb_datetest WHERE test_id = '$test_id' AND group_id = '$group_id'";
			
$result = mysqli_query($link, $sql);
$row 	= mysqli_fetch_array($result);

 date_default_timezone_set("Asia/Bangkok");
$now 	= strtotime("now");
$start 	= $row[0] . " " . $row[1];
$end 	= $row[0] . " " . $row[2];
$start 	= strtotime($start);
$end 	= strtotime($end);
//ถ้ากำหนดวันเวลาในการทำแบบทดสอบ และไม่อยู่ในช่วงเวลานั้น
//ให้โหลดเพจ testing.phpใหม่ ซึ่งจะไปบรรจบกับการตรวจสอบภายในเพจนี้พอดี
if(($row[0] != "0000-00-00") && (($now < $start) || ($now > $end))) {
	mysqli_close($link);
	exit("window.location = 'testing.php?test_id=$test_id&group_id=$group_id';");
}

//echo "<script>alert($test_id;);</script>";

$sql = "REPLACE INTO std_ques VALUES('$test_id','$std_id','$ques_id','$choice_id')";
mysqli_query($link, $sql);
mysqli_close($link);
/*
	$std_id = "9920310001";
	$test_id   = 2;
	$ques_id   = 19;
	$choice_id = 73;
*/
?>
