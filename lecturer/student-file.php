<?php
include "../check-user.php";
$lec_id   = $_SESSION['lec_id'];
$lec_name = $_SESSION['lec_name'];
$manage   = $_SESSION['manage'];
$term     = $_SESSION['term'];
$year     = $_SESSION['year'];

$eng_name  = $_SESSION['eng_name'];
$sub_id    = $_SESSION['sub_id'];
$group_id  = $_SESSION['group_id'];
$lec_group = $_SESSION['lec_group'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>OTS Lecturer</title>
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">
  <link href="../css/sb-admin.css" rel="stylesheet">

  <link href="../vendor/jquery/jquery-ui.min.css" rel="stylesheet">
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/jquery/jquery-ui.min.js"></script>
  <script src="../vendor/jquery/jquery.blockUI.js"></script>
</head>

<body class="fixed-nav sticky-footer bg-dark" id="page-top">
  <?php include "navbar-lecturer.php";?>
  <?php
  include "../dblink.php";
  $f= "";
  if(isset($_POST["submit"])) {
    if($_FILES['file']['name']) {
      $filename = explode(".", $_FILES['file']['name']);
      if($filename[1] == 'csv') {
        $handle = fopen($_FILES['file']['tmp_name'], "r");
        $j = 1;
        while ($data = fgetcsv($handle)) {
          if($j > 1) {
            $std_id   = mysqli_real_escape_string($link, $data[0]);
            $std_name = mysqli_real_escape_string($link, $data[1]);
            $major    = mysqli_real_escape_string($link, $data[2]);
            $faculty  = mysqli_real_escape_string($link, $data[3]);
        
            if($std_id != ""){
              $sql = "REPLACE INTO student VALUES('$std_id','$std_name','$major','$faculty')";
              if(mysqli_query($link, $sql)) {
                //$std_id = mysqli_insert_id($link);
                //$chars   = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                $chars   = "abcdefghijklmnopqrstuvwxyz";
                $std_pwd = substr(str_shuffle($chars), 0, 8);

                $sql = "REPLACE INTO enrollment VALUES('$std_id','$group_id','$std_pwd')";
                mysqli_query($link, $sql);
              }
              else {
                //$msg = "";
              }
            }
          } 
          $j++;
        }
        fclose($handle);
        echo "<script>location.href = 'student-pwd.php?group_id=".$group_id."'</script";
      }
    }
    mysqli_close($link); 
  } 
  ?>

  <div class="content-wrapper">
    <div class="container-fluid">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <?php 
            echo "Subject: " .$sub_id. " " .$eng_name. "<br>";
            echo "<b>Term:</b> " . $term . " <b>Year:</b> " . $year . " <b>Group:</b> " .$lec_group;
          ?>
        </li>
      </ol>
      <div class="row">
        <div class="col-lg-5">
          <div class="card">
            <div class="card-header">
              Uploading student file
            </div> 
            <div class="card-body"> 
              <form method="post" enctype="multipart/form-data">
                <div align="center">
                  <p> CSV only: <input type="file" name="file"> </p>
                  <p> <input type="submit" name="submit" value="Import"></p>
                </div>
              </form>
            </div>
            <div class="card-footer small text-muted">
              <a href="student-pwd.php?<?php echo 'group_id='.$group_id.'&lec_group='.$lec_group;?>" class="previous">&laquo; Previous</a>
            </div>
          </div> 
        </div>
      </div>
    </div>
    <!-- /.container-fluid-->
    <?php include "../footer-modal.php";?>
  </div>
</body>
</html>
