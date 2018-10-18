<?php 
include "check-user.php";
$user     = $_SESSION['user'];
$std_id   = $_SESSION['std_id'];
$std_name = $_SESSION['std_name'];
$group_id = $_SESSION['group_id'];

include "dblink.php";
$sql = "SELECT test_id, eng_name, term, year, 
                              DATE_FORMAT(date_test, '%d/%m/%Y') AS date_test, 
                              TIME_FORMAT(time_start, '%H.%i') AS time_start,  
                              TIME_FORMAT(time_end, '%H.%i') AS time_end
                        FROM tb_datetest t, lec_group l, subject s
                        WHERE t.group_id = '$group_id' AND t.group_id = l.gr_id 
                                                       AND l.sub_id = s.sub_id ";
$r   = mysqli_query($link, $sql);
$row = mysqli_fetch_array($r);

$test_id    = $row['test_id'];
$eng_name   = $row['eng_name'];
$term       = $row['term'];
$year       = $row['year'];
$time_start = $row['time_start'];
$time_end   = $row['time_end'];
$date_test  = $row['date_test'];

$_SESSION['test_id']    = $test_id;
$_SESSION['eng_name']   = $eng_name;
$_SESSION['term']       = $term;
$_SESSION['year']       = $year;
$_SESSION['time_start'] = $time_start;
$_SESSION['time_end']   = $time_end;
$_SESSION['date_test']  = $date_test;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>OTS Student</title>

  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <link href="vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">
  <link href="css/sb-admin.css" rel="stylesheet">
</head>

<body class="fixed-nav sticky-footer bg-dark" id="page-top">
  <!-- Navigation-->
  <?php include "navigation-student.php";?>
  <div class="content-wrapper">
    <div class="container-fluid">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="index.html">Dashboard</a>
        </li>
        <li class="breadcrumb-item active">Blank Page</li>
      </ol>

      <div class="row">
        <div class="col-lg-8">
          <div class="card mb-3">
            <div class="card-header">
              <i class="fa fa-bar-chart"></i> Detail of Quizzes
            </div>
            <div class="card-body">
              <?php
                $q1 = "SELECT test_name, COUNT(q.test_id) as count 
                      FROM testing t, test_ques q 
                      WHERE t.test_id = '$test_id' AND t.test_id = q.test_id";
                $r1 = mysqli_query($link, $q1);
                $row1 = mysqli_fetch_assoc($r1);
                $test_name = $row1['test_name'];
                $count     = $row1['count'];
              ?>
            ข้อสอบ : <?php echo $test_name; ?> <br>
             ชื่อวิชา:    <?php echo $eng_name; ?> <br>
             ภาคการศึกษา:  <?php echo $term; ?><br>
             ปีการศึกษา:   <?php echo $year; ?> <br>
             วันที่สอบ:   <?php echo $date_test; ?> <br>
             เวลาสอบ:     <?php echo $time_start .' ถึง ' .$time_end ; ?> <br>
             จำนวนข้อ:   <?php echo $count; ?> <br>
             หมายเหตุ: กรุณาอ่านโจทย์แต่ละข้อให้ละเอียด ก่อนที่จะเลื่อกคำตอบ เพราะนักศึกษาไม่สามารถทำย้อนหลังได้!!!
            </div>
            <div class="card-footer small text-muted">Updated yesterday at 11:59 PM</div>
          </div>
        </div>
      </div>

    </div>
    <!-- /.container-fluid-->
    <?php include "footer-modal.php";?>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin.min.js"></script>
  </div>
</body>
</html>
