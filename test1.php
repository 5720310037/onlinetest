<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Example 20-6</title>
<style>
	* {
		font: 14px tahoma;
	}
	body {
		background: url(bg.jpg);
	}
	input[type="text"] {
	 	display: block;  
		width: 400px;
		margin-top: 3px;
	}
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
$(function() {
	var label = '<label for="male">Male</label>';
	var input = ' <select><?php
                  include "dblink.php";
                  $q = "SELECT lec_id, lec_name FROM lecturer";    
                  $r = mysqli_query($link, $q); // Run the query.
                  //$i=0;
                  while($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {?><option value="<?=$row["lec_id"];?>"><?=$row["lec_name"];?></option><?php
                  //$i++;
                  }
                  mysqli_close($link);?></select>';
	
	//เมื่อคลิกปุ่ม + ให้เพิ่มอิลิเมนต์ text ลงไปในฟอร์ม
	$('#add').click(function() {
		$('form').append(label).append(input);
		//$('form').append(input);
	});
	
	//เมื่อคลิกปุ่ม - ให้ลบอิลิเมนต์ text ที่ชื่อขึ้นต้นด้วยคำว่า "choice" ตัวสุดท้าย ออกจากฟอร์ม
	$('#remove').click(function() {
		$('label:last').remove();
 		$('select:last').remove();
	});
});
</script>
</head>

<body>
<form>
 	หัวข้อโพล: <input type="text" name="topic"><br>
 	ตัวเลือก: 
 	<button type="button" id="add">+</button>
 	<button type="button" id="remove">-</button><br><br>
 </form>
</body>
</html>
