<?php
include "../check-user.php";
/*if($_SESSION['user']=="lecturer") {
	header("location: index.php");
	exit;
}*/

include "../dblink.php";
$group_id = $_SESSION['group_id'];
$std_id   = $_SESSION['std_id'];
$test_id  = $_GET['test_id'];
//ตรวจสอบว่าผู้ใช้รายนี้ได้ทำแบบทดสอบหัวข้อนี้หรือไม่
$sql = "SELECT COUNT(*) FROM std_ques WHERE test_id = $test_id AND std_id = $std_id";
$result = mysqli_query($link, $sql);
$row = mysqli_fetch_array($result);
$msg = "";
if($row[0] == 0) {
	mysqli_close($link);
	$msg = '<h3>
					<img src="../images/no.png">
					ไม่พบข้อมูลการทำแบบสอบทดสอบของท่านในหัวข้อนี้
			 	</h3>';
} else {	//ตรวจนับคะแนน
	$sql = "SELECT COUNT(*) FROM std_ques
			WHERE test_id = $test_id AND std_id = $std_id AND choice_id IN(
					SELECT choice_id 
					FROM choice 
					WHERE test_id = $test_id AND answer = 'yes')";
			
	$result =  mysqli_query($link, $sql);
	$row 	= mysqli_fetch_array($result);
	$score 	= $row[0];

	$sql = "REPLACE INTO take_exam VALUES($std_id, $group_id, $test_id, $score)";
	mysqli_query($link, $sql);

	/*$sql = "DELETE FROM testing WHERE testee_id = $testee_id AND subject_id = $subject_id";
	mysqli_query($link, $sql); */
				
	mysqli_close($link);
	$msg = '<h3>
					<img src="../images/ok.png">
					เสร็จสิ้นการทดสอบและตรวจนับคะแนนแล้ว
				</h3>';
}
header("refresh:5; url=index.php");
?>

<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>Online Testing System: Finish</title>
	<style>
		html {
			cursor: wait;		
		}
		body {
			text-align: center;
		}
		h3 img {
			margin-right: 3px;
			vertical-align: middle;
		}
	</style>
</head>

<body>
	<?php echo $msg; ?>
	<h4>จะกลับไปยังหน้าหลักใน 5 วินาที</h4>
</body>
</html>