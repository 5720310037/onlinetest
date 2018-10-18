<!DOCTYPE html>
<?php
	include "dblink.php";
	$les_id = "12";
	$f= "";

	if(isset($_POST["submit"])) {
		if($_FILES['file']['name']) {
			$filename = explode(".", $_FILES['file']['name']);
			if($filename[1] == 'csv') {
				$handle = fopen($_FILES['file']['tmp_name'], "r");
				while ($data = fgetcsv($handle)) {
					$ques_detail = mysqli_real_escape_string($link, $data[0]);
					$ques_level = mysqli_real_escape_string($link, $data[1]);

					$choice[1] = mysqli_real_escape_string($link, $data[2]);
					$choice[2] = mysqli_real_escape_string($link, $data[3]);
					$choice[3] = mysqli_real_escape_string($link, $data[4]);
					$choice[4] = mysqli_real_escape_string($link, $data[5]);
					$ans = mysqli_real_escape_string($link, $data[6]);

					$sql = "REPLACE INTO question VALUES('','$les_id','$ques_detail','$f','active','$ques_level')";
					if(@mysqli_query($link, $sql)) {
						$ques_id = mysqli_insert_id($link);

						for($i = 1; $i <= 4; $i++) {
							$answer = "no";
							if($ans == $i) {
								$answer = "yes";
							} 
							$sql = "REPLACE INTO choice VALUES('','$ques_id','$choice[$i]','$answer')";
							mysqli_query($link, $sql);
						}
						//$msg = 'บันทึกข้อมูลเรียบร้อยแล้ว<br>กรุณาใส่ข้อมูลคำถามถัดไป หรือกลับไปยัง <a href="index.php">หน้าหลัก</a>';
						//$img = "ok.png";
					}
					else {
						//$msg = "error";
					}

				}
				fclose($handle);
				echo "Import Done!";
			}
		}
	} 
?>
<html>
<head>
	<title></title>
</head>
<body>
	<form method="post" enctype="multipart/form-data">
		<div align="center">
			<p> Upload CSV: <input type="file" name="file"> </p>
			<p> <input type="submit" name="submit" value="Import"></p>
		</div>
	</form>
</body>
</html>