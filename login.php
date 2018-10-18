<?php
session_start();

if($_POST){
  $msg = "";
  $username = $_POST['username'];
  $password = $_POST['password'];

  include "dblink.php";
  $sql = "SELECT *
          FROM administrator 
          WHERE adm_id = '$username' AND adm_pwd = '$password'";
  $result = mysqli_query($link, $sql);

  if(mysqli_num_rows($result)>0) {
    $_SESSION['user'] = "Administrator";

    mysqli_close($link);
    header("location: admin/user.php");
    ob_end_flush();
    exit;
  }else{
    $sql = "SELECT lec_id, lec_name 
            FROM lecturer 
            WHERE lec_id = '$username' AND lec_pwd = '$password'";
    $result = mysqli_query($link, $sql);
    if(mysqli_num_rows($result)>0) {
      $row = mysqli_fetch_array($result);
      $_SESSION['user']     = "lecturer";
      $_SESSION['lec_id']   = $row['lec_id'];
      $_SESSION['lec_name'] = $row['lec_name'];

      //check that lecturer manage the subject or not
      $q = "SELECT sub_id FROM manage_status WHERE lec_id = '$username'"; 
      $r = mysqli_query($link, $q);
      if(mysqli_num_rows($r) > 0) {
        $_SESSION['manage'] = 1;
      }else{
        $_SESSION['manage'] = 0;
      }
          
      mysqli_close($link);
      header("location: lecturer/test-list.php");
      ob_end_flush();
      exit;
    }else{
      $sql = "SELECT e.std_id AS std_id, s.std_name AS std_name, group_id
              FROM enrollment e, student s 
              WHERE e.std_id = '$username' AND e.std_id = s.std_id AND std_pwd = '$password'"; 
      $result = mysqli_query($link, $sql);
      if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);
        $_SESSION['user']     = "student";
        $_SESSION['std_id']   = $row['std_id'];
        $_SESSION['std_name'] = $row['std_name'];
        $_SESSION['group_id'] = $row['group_id'];
          
        mysqli_close($link);
        header("location: student/index.php");
        ob_end_flush();
        exit;
      }
      else {
        $msg = "ท่านกำหนด Username หรือ Password ไม่ถูกต้อง";
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Online Testing System</title>
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <link href="css/sb-admin.css" rel="stylesheet">
  <link href="css/login-style.css" rel="stylesheet">
</head>

<body>
  <div id="header">
    <br>
    <h1>ระบบข้อสอบออนไลน์</h1>
    <h2>Welcome to Online Testing System...</h2>
  </div>
  <div class="container"><br><br>
    <div class="card card-login mx-auto mt-5">
      <div class="card-header"><i class="fa fa-user"></i> <b>Please login to system</b></div>
      <div class="card-body">
        <form method="post">
          <div class="form-group" align="center">
            <input type="text" name="username" maxlength="10" placeholder="Username">
          </div>
          <div class="form-group" align="center">
            <input type="password" name="password" maxlength="10" placeholder="Password">
          </div>
        </form>
        <div class="text-center">
          <button type="submit" id="ok">Login</button>
        </div>
      </div>
    </div>
  </div><br><br><br><br><br><br><br><br>
  <div id="footer">
    <p align="center">&copy; <?php echo date('Y');?> Department of Mathematics and Computer Science, <br>Faculty of Science and Technology, Prince of Songkla University. All Rights Reserved.</p>
  </div>
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
  <script>
  $(function() {
    $('button').click(function() {
      if($(':text').val() == "")  {
        alert('ท่านยังไม่ได้กำหนด Username');
      }
      else if($(':password').val() == "") {
        alert('ท่านยังไม่ได้กำหนด Password');
      }
      /*else if($(':radio:checked').length == 0) {
        alert('ท่านยังไม่ได้เลือก User Type');
      }*/
      else {
        $('form').submit();
      }
    });
  });
  </script>
</body>
</html>
