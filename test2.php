<!DOCTYPE html>
<?php
	$connect = mysqli_connect("localhost","root","mark","mydb");

	if(isset($_POST["submit"])) {
		if($_FILES['file']['name']) {
			$filename = explode(".", $_FILES['file']['name']);
			if($filename[1] == 'csv') {
				$handle = fopen($_FILES['file']['tmp_name'], "r");
				while ($data = fgetcsv($handle)) {
					$item1 = mysqli_real_escape_string($connect, $data[0]);
					$item2 = mysqli_real_escape_string($connect, $data[1]);
					$sql = "INSERT INTO mytable(id, name) VALUES('$item1', '$item2')";
					mysqli_query($connect, $sql);

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