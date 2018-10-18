<?php 
include "../check-user.php";
$lec_id   = $_SESSION['lec_id'];
$lec_name = $_SESSION['lec_name'];
$manage   = $_SESSION['manage'];
$sub_id   = $_SESSION['sub_id'];
$term     = $_SESSION['term'];
$year     = $_SESSION['year'];

if(isset($_GET['test_id'])) {
  $test_id  = $_GET['test_id'];
}else{
  $test_id  = "null";
}

if(isset($_GET['group_id'])) {
  $group_id = $_GET['group_id'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Online Testing System</title>
  <style type="text/css">
    button#export-score {
      float: right;
    }
    button#import-std {
      float: right;
    }
    a {
      text-decoration: none;
      display: inline-block;
    }
    a:hover {
      background-color: #ddd;
      color: black;
    }
    .previous {
      background-color: #00BFFF;
      color: black;
    }
  </style>
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">
  <link href="../css/sb-admin.css" rel="stylesheet">

  <link href="../vendor/jquery/jquery-ui.min.css" rel="stylesheet">
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/jquery/jquery-ui.min.js"></script>
  <script src="../vendor/jquery/jquery.blockUI.js"></script>
  <script type="text/javascript">
  $(function() {
    $('#export-score').click(function() {
      location.href = 'export-score.php?test_id=<?php echo $test_id.'&group_id='.$group_id; ?>';
    });

    $('#import-std').click(function() {
      var group_id = <?php echo $group_id; ?>;
      var test_id  = <?php echo $test_id; ?>;
      var action   = "import";
      if(action == "import") {
        if(!(confirm("Do you want to import from student?"))) {
          return;
        }
      }
      $.ajax({
        url:'student-action.php',
        type:'post',
        data:{'group_id':group_id, 'test_id':test_id, 'action':action},
        beforeSend: function() {
          $.blockUI();
        },
        success: function() {
          document.location.reload();
        },
        complete: function() {
          $.unblockUI();
        } 
      });
    });
  });
  </script>
</head>

<body class="fixed-nav sticky-footer bg-dark" id="page-top">
  <?php include "navbar-lecturer.php";?>
  <div class="content-wrapper">
    <div class="container-fluid">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <?php
            if(!isset($_GET['test_id'])) {
              echo "<b>Total Score List</b>";;
            }else {
              echo "<b>Score List</b>";
            }
          ?>
        </li>
      </ol>
      <div class="card mb-3">
        <div class="card-header">
          <?php
            include "../dblink.php";
            $q = "SELECT eng_name FROM subject WHERE sub_id = '$sub_id'";
            $r = mysqli_query($link, $q);
            $row = mysqli_fetch_array($r);
            echo "<b>Subject:</b> " .$sub_id. " " .$row['eng_name']. "<br>";
            echo "<b>Term:</b> " . $term . " <b>Year:</b> " . $year;

            if(isset($_GET['test_id'])) {
              $q = "SELECT COUNT(ques_id) AS count, test_name, 
                                      DATE_FORMAT(date_test, '%d/%m/%Y') AS date_test, 
                                      TIME_FORMAT(time_start, '%H.%i') AS time_start,  
                                      TIME_FORMAT(time_end, '%H.%i') AS time_end 
                    FROM tb_datetest d, testing t, test_ques q
                    WHERE group_id = '$group_id' 
                    AND d.test_id = '$test_id' AND d.test_id = t.test_id AND q.test_id = '$test_id'";
              $r   = mysqli_query($link, $q);
              $row = mysqli_fetch_array($r);
              echo "<b><br>Examination:</b> " .$row['test_name'] ."<br>";
              echo "<b>Date exam:</b> ".$row['date_test']." <b>Time:</b> ".$row['time_start']." - ".$row['time_end'];
              echo "<br><b>Quesions:</b> ".$row['count'];
            }
          ?>
          <button id="export-score"> <i class="fa fa-file-excel-o"></i> Export</button>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <?php
              if(!isset($_GET['test_id'])) {
                $sql = "SELECT std_id, SUM(score) AS score FROM take_exam 
                        WHERE group_id = '$group_id' GROUP BY std_id";
              }else{
                $sql = "SELECT std_id, score FROM take_exam 
                        WHERE group_id = '$group_id' AND test_id = '$test_id' ORDER BY std_id ASC";
              }
              echo '<table class="table table-bordered table-sm" id="dataTable" width="100%" cellspacing="0">
                  <thead class="thead-light">
                    <tr align="center">
                      <th>No.</th>
                      <th>StuID</th>
                      <th>Name</th>
                      <th>Score</th>
                    </tr>
                  </thead>';
              $order = 1;
              $result = mysqli_query($link, $sql);
              while ($data = mysqli_fetch_array($result)) {
                $std_id = $data['std_id'];
                $score  = $data['score'];

                $s1     = "SELECT std_name FROM student WHERE std_id = '$std_id'";
                $r      = mysqli_query($link, $s1);
                $row1   = mysqli_fetch_array($r);
                $std_name = $row1['std_name'];

                echo '<tr align="center">
                      <td>'.$order.'</td>
                      <td>'.$std_id.'</td>
                      <td>'.$std_name.'</td>
                      <td>'.$score.'</td>
                    </tr>';
                $order++;
              }
              echo '</table>';
              mysqli_free_result($result);
              mysqli_close($link);
            ?>
          </div>
        </div>
        <div class="card-footer small text-muted"> 
          <?php
            if(isset($_GET['test_id'])) {
              echo '<a href="report-list.php" class="previous">&laquo; Previous</a>';
              echo '<button id="import-std"> Retrieve data</button>';
            }else{
              echo '<a href="report-total.php" class="previous">&laquo; Previous</a>';
            }
          ?>
        </div>
      </div>
    </div>
    <!-- /.container-fluid-->
    <?php include "../footer-modal.php";?>
  </div>
</body>
</html>
