<?php 
include "../check-user.php";
$user     = $_SESSION['user'];
if($user != "student") {
  exit();
}

$std_id   = $_SESSION['std_id'];
$std_name = $_SESSION['std_name'];
$group_id = $_SESSION['group_id'];

include "../dblink.php";
$sql = "SELECT lec_group, l.sub_id AS sub_id, eng_name, term, year
                        FROM lec_group l, subject s
                        WHERE gr_id = '$group_id' AND l.sub_id = s.sub_id ";
$r   = mysqli_query($link, $sql);
$row = mysqli_fetch_array($r);

$group      = $row['lec_group'];
$sub_id     = $row['sub_id'];
$eng_name   = $row['eng_name'];
$term       = $row['term'];
$year       = $row['year'];

$_SESSION['group']  = $group;
$_SESSION['sub_id']     = $sub_id;
$_SESSION['eng_name']   = $eng_name;
$_SESSION['term']       = $term;
$_SESSION['year']       = $year;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>OTS Student</title>
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">
  <link href="../css/sb-admin.css" rel="stylesheet">

  <link href="../vendor/jquery/jquery-ui.min.css" rel="stylesheet">
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/jquery/jquery-ui.min.js"></script>
  <script src="../vendor/jquery/jquery.blockUI.js"></script>
  <script type="text/javascript">
    $('#test').click(function() {
      window.location = 'test-question.php?test_id=<?php echo $test_id;?>';
    });
  </script>
</head>

<body class="fixed-nav sticky-footer bg-dark" id="page-top">
  <?php include "navbar-stu.php";?>
  <div class="content-wrapper">
    <div class="container-fluid">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <b>Welcome to Online Testing System</b>
        </li>
      </ol>
      <div class="card mb-3">
        <div class="card-header">
          วิชา: <?php echo $sub_id." ".$eng_name; ?> &nbsp;&nbsp;&nbsp; 
          ภาคการศึกษา: <?php echo $term . " &nbsp;&nbsp;&nbsp; ปีการศึกษา: " .$year; ?> &nbsp;&nbsp;&nbsp; กลุ่มที่:   <?php echo $group; ?> 
        </div>
        <div class="card-body">
          <div class="table-responsive"> 
            <?php
              $q = "SELECT d.test_id as test_id, test_name, 
                      DATE_FORMAT(date_test, '%d/%m/%Y') AS date_test, 
                      TIME_FORMAT(time_start, '%H.%i') AS time_start, 
                      TIME_FORMAT(time_end, '%H.%i') AS time_end 
                    FROM tb_datetest d, testing t 
                    WHERE group_id = '$group_id' AND d.test_id = t.test_id"; 
              $r = mysqli_query($link, $q);

              echo '<table class="table table-bordered table-sm" width="100%" cellspacing="0"> 
              <thead align="center">
                <tr>
                  <th>ลำดับ</th>
                  <th>หัวข้อการทดสอบ</th>
                  <th>วันเวลาสอบ</th>
                  <th>จำนวนข้อ</th>
                  <th>คะแนนที่ได้</th>
                  <th>เริ่มทำข้อสอบ</th>
                </tr>
              </thead>';

              $order = 1;
              while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
                $test_id    = $row['test_id'];
                $test_name  = $row['test_name'];
                $dt = "วันที่ " .$row['date_test']." เวลา ".$row['time_start']." - ".$row['time_end'];
                if($row['date_test'] == "0000-00-00") {
                  $dt = "ไม่ระบุ";
                }

                $q1 = "SELECT COUNT(*) as count FROM test_ques WHERE test_id = '$test_id'";
                $r1 = mysqli_query($link, $q1);
                $row1  = mysqli_fetch_array($r1);
                $count = $row1['count'];

                $q2    = "SELECT score FROM take_exam 
                        WHERE std_id = '$std_id' AND group_id = '$group_id' AND test_id = '$test_id'";
                $r2    = mysqli_query($link, $q2);
                $row2  = mysqli_fetch_array($r2);
                $score = $row2['score'];

                echo '
                <tbody>
                  <tr>
                    <td align="center">'.$order.'</td>
                    <td>'.$test_name.'</td>
                    <td align="center">'.$dt.'</td>
                    <td align="center">'.$count.'</td>
                    <td align="center">'.$score.'</td>
                    <td align="center"> <a href="testing.php?test_id='.$test_id.'&group_id='.$group_id.'">Start Test</a> </td>
                  </tr>
                </tbody>';
                $order++;
              }
              echo '</table>';
            ?> 
          </div>
        </div>
      </div>
    </div>
    <!-- /.container-fluid-->
    <?php include "../footer-modal.php";?>
  </div>
</body>
</html>
