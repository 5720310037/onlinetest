<?php
	$link = @mysqli_connect("localhost", "root", "", "onlinetest") or die(mysqli_connect_error());
	mysqli_set_charset($link, "utf8");
?>