<?php
include "../check-user.php";
if(!$_POST) {
  exit;
}
include "../dblink.php";
if($_POST['action'] == "add") {
  $lec_id = $_POST['lec_id'];
  $sub_id = $_POST['sub_id'];

  $sql = "INSERT INTO manage_status VALUES('$lec_id', '$sub_id')";
  mysqli_query($link, $sql);
}
if($_POST['action'] == "del") {
  $lec_id = $_POST['lec_id'];
  $sub_id = $_POST['sub_id'];

  $sql = "DELETE FROM manage_status WHERE lec_id = '$lec_id' AND sub_id = '$sub_id'";
  mysqli_query($link, $sql);
} 
mysqli_close($link);
?>