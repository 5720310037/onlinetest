<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Online Testing System</title>
</head>

<body>
<?php
$link = @mysqli_connect("localhost", "root", "mark") or die(mysqli_connect_error());

$sql = "CREATE DATABASE IF NOT EXISTS onlinetest";
if(@mysqli_query($link, $sql)) {   echo "<br>สร้างฐานข้อมูล: onlinetest สำเร็จ<br>";  }
else {  die("<br>สร้างฐานข้อมูล: onlinetest ล้มเหลว<br>" . mysqli_error($link)); }

@mysqli_select_db($link, "onlinetest") or die(mysqli_error($link));


$sql = 	"CREATE TABLE IF NOT EXISTS administrator(
			adm_name VARCHAR(50),
			adm_id   VARCHAR(10),
			adm_pwd  VARCHAR(10))";
if(@mysqli_query($link, $sql)) {   echo "<br>สร้างตาราง: administrator สำเร็จ<br>";  }
else {  echo "<br>สร้างตาราง: administrator ล้มเหลว<br>" . mysqli_error($link); }

$sql = 	"CREATE TABLE IF NOT EXISTS student(
			std_id   VARCHAR(10) NOT NULL PRIMARY KEY,
			std_name VARCHAR(50),
			major    VARCHAR(50),
			faculty  VARCHAR(50))";
if(@mysqli_query($link, $sql)) {   echo "<br>สร้างตาราง: student สำเร็จ<br>";  }
else {  echo "<br>สร้างตาราง: student ล้มเหลว<br>" . mysqli_error($link); }


$sql = 	"CREATE TABLE IF NOT EXISTS lecturer(
			lec_id   VARCHAR(10) NOT NULL PRIMARY KEY,
			lec_name VARCHAR(50),
			lec_pwd  VARCHAR(10))";
if(@mysqli_query($link, $sql)) {   echo "<br>สร้างตาราง: lecturer สำเร็จ<br>";  }
else {  echo "<br>สร้างตาราง: lecturer ล้มเหลว<br>" . mysqli_error($link); }


$sql = 	"CREATE TABLE IF NOT EXISTS subject(
			sub_id    VARCHAR(7) NOT NULL PRIMARY KEY,
			thai_name VARCHAR(100),
			eng_name  VARCHAR(50))";
if(@mysqli_query($link, $sql)) {   echo "<br>สร้างตาราง: subject สำเร็จ<br>";  }
else {  echo "<br>สร้างตาราง: subject ล้มเหลว<br>" . mysqli_error($link); }


$sql = 	"CREATE TABLE IF NOT EXISTS lesson(
			les_id   SMALLINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
			sub_id   VARCHAR(7),
			les_name VARCHAR(50),

			FOREIGN KEY (sub_id) REFERENCES subject(sub_id))";
if(@mysqli_query($link, $sql)) {   echo "<br>สร้างตาราง: lesson สำเร็จ<br>";  }
else {  echo "<br>สร้างตาราง: lesson ล้มเหลว<br>" . mysqli_error($link); }


$sql = 	"CREATE TABLE IF NOT EXISTS question(
			ques_id     SMALLINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
			les_id      SMALLINT UNSIGNED,
			ques_detail TEXT,
			ques_image  MEDIUMBLOB,
			ques_status SET('active','inactive'),
			ques_level  VARCHAR(10),

			FOREIGN KEY (les_id) REFERENCES lesson(les_id))";
if(@mysqli_query($link, $sql)) {   echo "<br>สร้างตาราง: question สำเร็จ<br>";  }
else {  echo "<br>สร้างตาราง: question ล้มเหลว<br>" . mysqli_error($link); }


$sql = 	"CREATE TABLE IF NOT EXISTS choice(
			 choice_id     MEDIUMINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
			 ques_id       SMALLINT UNSIGNED,
			 choice_detail VARCHAR(250),
			 answer SET('yes', 'no'),

			 FOREIGN KEY (ques_id) REFERENCES question(ques_id))";
if(@mysqli_query($link, $sql)) {   echo "<br>สร้างตาราง: choice สำเร็จ<br>";  }
else {  echo "<br>สร้างตาราง: choice ล้มเหลว<br>" . mysqli_error($link); }


$sql = 	"CREATE TABLE IF NOT EXISTS manage_status(
			lec_id VARCHAR(10),
			sub_id VARCHAR(7),
			PRIMARY KEY(lec_id, sub_id),

			FOREIGN KEY (lec_id) REFERENCES lecturer(lec_id),
			FOREIGN KEY (sub_id) REFERENCES subject(sub_id))";
if(@mysqli_query($link, $sql)) {   echo "<br>สร้างตาราง: manage_status สำเร็จ<br>";  }
else {  echo "<br>สร้างตาราง: manage_status ล้มเหลว<br>" . mysqli_error($link); }


$sql = "CREATE TABLE IF NOT EXISTS lec_group(
			gr_id     SMALLINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
			term      SMALLINT UNSIGNED,
			year      SMALLINT UNSIGNED,
			lec_group SMALLINT UNSIGNED,
			lec_id    VARCHAR(10),
			sub_id    VARCHAR(7),

			FOREIGN KEY (lec_id) REFERENCES lecturer(lec_id),
			FOREIGN KEY (sub_id) REFERENCES subject(sub_id))";
if(@mysqli_query($link, $sql)) {   echo "<br>สร้างตาราง: lec_group สำเร็จ<br>";  }
else {  echo "<br>สร้างตาราง: lec_group ล้มเหลว<br>" . mysqli_error($link); }


$sql = 	"CREATE TABLE IF NOT EXISTS enrollment(
			std_id   VARCHAR(10),
			group_id SMALLINT UNSIGNED,
			std_pwd  VARCHAR(10),
			PRIMARY KEY(std_id, group_id),

			FOREIGN KEY (std_id)   REFERENCES student(std_id),
			FOREIGN KEY (group_id) REFERENCES lec_group(gr_id))";
if(@mysqli_query($link, $sql)) {   echo "<br>สร้างตาราง: enrollment สำเร็จ<br>";  }
else {  echo "<br>สร้างตาราง: enrollment ล้มเหลว<br>" . mysqli_error($link); }


$sql = 	"CREATE TABLE IF NOT EXISTS testing(
			test_id   SMALLINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
			test_name VARCHAR(50),
			term      SMALLINT UNSIGNED,
			year      SMALLINT UNSIGNED,
			detail    VARCHAR(50),
			sub_id    VARCHAR(7),

			FOREIGN KEY (sub_id) REFERENCES subject(sub_id))";
if(@mysqli_query($link, $sql)) {   echo "<br>สร้างตาราง: testing สำเร็จ<br>";  }
else {  echo "<br>สร้างตาราง: testing ล้มเหลว<br>" . mysqli_error($link); }


$sql = 	"CREATE TABLE IF NOT EXISTS take_exam(
			std_id   VARCHAR(10),
			group_id SMALLINT UNSIGNED,
			test_id  SMALLINT UNSIGNED,
			score    VARCHAR(3),
			PRIMARY KEY(std_id, test_id, group_id),

			FOREIGN KEY (std_id)   REFERENCES student(std_id),
			FOREIGN KEY (group_id) REFERENCES lec_group(gr_id),
			FOREIGN KEY (test_id)  REFERENCES testing(test_id))";
if(@mysqli_query($link, $sql)) {   echo "<br>สร้างตาราง: take_exam สำเร็จ<br>";  }
else {  echo "<br>สร้างตาราง: take_exam ล้มเหลว<br>" . mysqli_error($link); }


$sql = 	"CREATE TABLE IF NOT EXISTS test_ques(
			test_id SMALLINT UNSIGNED,
			ques_id SMALLINT UNSIGNED,
			PRIMARY KEY(test_id, ques_id),

			FOREIGN KEY (test_id) REFERENCES testing(test_id),
			FOREIGN KEY (ques_id) REFERENCES question(ques_id))";
if(@mysqli_query($link, $sql)) {   echo "<br>สร้างตาราง: test_ques สำเร็จ<br>";  }
else {  echo "<br>สร้างตาราง: test_ques ล้มเหลว<br>" . mysqli_error($link); }


$sql = 	"CREATE TABLE IF NOT EXISTS std_ques(
			test_id SMALLINT UNSIGNED,
			std_id  VARCHAR(10),
			ques_id SMALLINT UNSIGNED,
			choice_id MEDIUMINT UNSIGNED,
			PRIMARY KEY(test_id, std_id, ques_id),

			FOREIGN KEY (test_id)   REFERENCES testing(test_id),
			FOREIGN KEY (std_id)    REFERENCES student(std_id),
			FOREIGN KEY (ques_id)   REFERENCES question(ques_id),
			FOREIGN KEY (choice_id) REFERENCES choice(choice_id))";
if(@mysqli_query($link, $sql)) {   echo "<br>สร้างตาราง: std_ques สำเร็จ<br>";  }
else {  echo "<br>สร้างตาราง: std_ques ล้มเหลว<br>" . mysqli_error($link); }


$sql = 	"CREATE TABLE IF NOT EXISTS tb_datetest(
			test_id    SMALLINT UNSIGNED,
			group_id   SMALLINT UNSIGNED,
			date_test  DATE,
			time_start TIME,
			time_end   TIME,
			PRIMARY KEY(test_id, group_id),

			FOREIGN KEY (test_id)  REFERENCES testing(test_id),
			FOREIGN KEY (group_id) REFERENCES lec_group(gr_id))";
if(@mysqli_query($link, $sql)) {   echo "<br>สร้างตาราง: group_test สำเร็จ<br>";  }
else {  echo "<br>สร้างตาราง: subject ล้มเหลว<br>" . mysqli_error($link); }

@mysqli_close($link);
?>
</body>
</html>