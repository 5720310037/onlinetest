<?php 
include "../check-user.php";
$lec_id   = $_SESSION['lec_id'];
$lec_name = $_SESSION['lec_name'];
$manage   = $_SESSION['manage'];

if(empty($_POST)){
  $sub_id = "";
  $term   = "";
  $year   = "";
  $_SESSION['sub_id'] = "";
  $_SESSION['term']   = "";
  $_SESSION['year']   = "";
}else{
  $sub_id = $_POST['sub_id'];
  $term   = $_POST['term'];
  $year   = $_POST['year'];
  $_SESSION['sub_id'] = $sub_id;
  $_SESSION['term']   = $term;
  $_SESSION['year']   = $year;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Online Testing System</title>
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">
  <link href="../css/sb-admin.css" rel="stylesheet">

  <link href="../vendor/jquery/jquery-ui.min.css" rel="stylesheet">
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/jquery/jquery-ui.min.js"></script>
  <script src="../vendor/jquery/jquery.blockUI.js"></script>
  <script type="text/javascript">
  </script>
</head>

<body class="fixed-nav sticky-footer bg-dark" id="page-top">
  <?php 
    include "navbar-lecturer.php";
    include "../dblink.php";
    if($manage == 1) {
      $q = "SELECT s.sub_id as sub_id, eng_name FROM manage_status m, subject s 
            WHERE m.sub_id = s.sub_id AND  lec_id = '$lec_id'";
    }else{
      $q = "SELECT DISTINCT l.sub_id as sub_id, eng_name FROM lec_group l, subject s 
            WHERE lec_id = '$lec_id' AND l.sub_id = s.sub_id";
    }
    $r = mysqli_query($link, $q);
  ?>
  <div class="content-wrapper">
    <?php 
    if(mysqli_num_rows($r) > 0) {  //check group
    ?>
    <div class="container-fluid">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <b>Report List</b>
        </li>
      </ol>
      <div class="card mb-3">
        <div class="card-header">
          <form method="post">
            Subject:
            <select name="sub_id">
              <?php
                while($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
                  if($sub_id == $row['sub_id']) {
                    $selected = "selected";
                  }
                  else {
                    $selected = "";
                  }
                  echo "<option value=\"{$row['sub_id']}\" $selected>" .$row['sub_id'].' '. 
                        $row['eng_name']. "</option>";
                }
              ?>
            </select> &nbsp;&nbsp;&nbsp;
            Term:
            <select name="term">
              <?php 
                for($i=1; $i<=3; $i++) {
                  if($term == $i) {
                    $selected = "selected";
                  } 
                  else {
                    $selected = "";
                  }
                  echo "<option value=\"{$i}\" $selected>" . $i . "</option>";
                }
              ?>
            </select> &nbsp;&nbsp;&nbsp;
            Year:
            <select name="year">
              <?php 
                $y = date('Y') + 543;
                for($i = $y ; $i >= $y-4; $i--){  //show only 5 options
                  if($year == $i){
                    $selected = "selected";
                  }
                  else {
                    $selected = "";
                  }
                  echo "<option value=\"{$i}\" $selected>" . $i. "</option>";
                }
              ?>
            </select> &nbsp;&nbsp;&nbsp;
            <input type="submit" value="View">
          </form>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <?php
              if($manage == 1) {
                $q = "SELECT test_id, test_name, t.term, t.year, detail, 
                      gr_id AS group_id, lec_group
                    FROM lec_group l, testing t 
                    WHERE l.sub_id = '$sub_id' AND t.sub_id = '$sub_id' 
                    AND l.term = t.term AND t.term = '$term' 
                    AND l.year = t.year AND t.year = '$year' 
                    ORDER BY test_id DESC, group_id";
              }else{
                $q = "SELECT test_id, test_name, t.term, t.year, detail, 
                      gr_id AS group_id, lec_group
                    FROM lec_group l, testing t 
                    WHERE l.sub_id = '$sub_id' AND t.sub_id = '$sub_id' 
                    AND l.term = t.term AND t.term = '$term' 
                    AND l.year = t.year AND t.year = '$year' 
                    AND lec_id = '$lec_id' ORDER BY test_id DESC, group_id";
              }
              $r = mysqli_query ($link, $q); 
              echo '<table class="table table-bordered table-sm table-striped" id="dataTable" 
                      width="100%" cellspacing="0">'; 
              echo '<thead class="thead-light"> 
                      <tr align="center"> 
                        <th>No.</th> 
                        <th>Examination</th> 
                        <th>Group</th> 
                        <th>Testee</th> 
                        <th>Date exam</th> 
                        <th>Detail</th> 
                      </tr> 
                    </thead>';
              $order = 1;
              while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
                $test_id  = $row['test_id'];
                $group_id = $row['group_id'];

                $q1 = "SELECT COUNT(*) as count FROM enrollment WHERE group_id = '$group_id'";
                $r1 = mysqli_query($link, $q1);
                $row1  = mysqli_fetch_assoc($r1);
                $count = $row1['count'];

                $q2 = "SELECT DATE_FORMAT(date_test, '%d/%m/%Y') AS date_test, 
                              TIME_FORMAT(time_start, '%H.%i') AS time_start,  
                              TIME_FORMAT(time_end, '%H.%i') AS time_end   
                       FROM tb_datetest WHERE test_id = '$test_id' AND group_id = '$group_id'";
                $r2   = mysqli_query($link, $q2);
                $row2 = mysqli_fetch_array($r2);
                $date_test  = $row2['date_test'];
                $time_start = $row2['time_start'];
                $time_end   = $row2['time_end'];

                echo '<tr>
                      <td align="center">'.$order.'</td>
                      <td>
                        <a href="report.php?test_id='.$row['test_id'].'&group_id='.$group_id.'">'.$row['test_name'].'</a>
                      </td>
                      <td align="center">'.$row['lec_group'].'</td>
                      <td align="center">'.$count.'</td>
                      <td align="center">Date: '.$date_test.' Time: '.$time_start.'-'.$time_end.'</td>
                      <td>'.$row['detail'].'</td>
                    </tr>';
                    $order++;
              }
              echo '</table>';
              mysqli_free_result ($r);
              mysqli_close($link);
            ?>
          </div>
        </div>
        <div class="card-footer small text-muted"> 
          <a href="report-total.php" class="total-report"> Total Report</a>
        </div>
      </div>
    </div>
    <!-- /.container-fluid-->
    <?php
    } else {
      // if do not have group
      echo '
      <div class="container-fluid">
        <!-- Breadcrumbs-->
        <ol class="breadcrumb">
          <li class="breadcrumb-item">
            <b>Sorry, you can not access Reports!</b>
          </li>
        </ol>
      </div> ';
      mysqli_close($link);
    }
    ?>
    <?php include "../footer-modal.php";?>
  </div>
</body>
</html>
