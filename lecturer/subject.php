<?php 
include "../check-user.php";
$lec_id = $_SESSION['lec_id'];
$lec_name = $_SESSION['lec_name'];
$manage   = $_SESSION['manage'];
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
</head>

<body class="fixed-nav sticky-footer bg-dark" id="page-top">
  <?php 
    include "navbar-lecturer.php";
    include "../dblink.php";
    $q = "SELECT * FROM subject WHERE sub_id IN (SELECT sub_id FROM manage_status 
          WHERE lec_id = '$lec_id')";   
    $r = mysqli_query($link, $q);
  ?>
  <div class="content-wrapper">
    <div class="container-fluid">
      <div class="card mb-3">
        <div class="card-header">
          <b>Subject List</b>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <?php 
              echo '<table class="table table-bordered table-sm table-striped" id="dataTable" width="100%" cellspacing="0">
              <thead class="thead-light">
                <tr align="center">
                  <th>No.</th>
                  <th>Subject ID</th>
                  <th>Subject(Thai)</th>
                  <th>Subject(Eng)</th>
                  <th>View Lesson</th>
                </tr>
              </thead>';
              $order = 1;
              while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
                echo '<tr>
                  <td align="center">' . $order . '</td>
                  <td align="center">' . $row['sub_id'] . '</td>
                  <td>' . $row['thai_name'] . '</td>
                  <td>' . $row['eng_name'] . '</td>
                  <td align="center"> <a href="lesson.php?sub_id='.$row['sub_id'].'">View Lesson</a> </td>
                </tr>';
                $order++;
              }
              echo '</table>';
              mysqli_free_result($r);
            ?>
          </div>
        </div>
      </div>
    </div>
    <!-- /.container-fluid-->

    <?php include "../footer-modal.php";?>
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="../vendor/datatables/jquery.dataTables.js"></script>
    <script src="../vendor/datatables/dataTables.bootstrap4.js"></script>
    <script src="../js/sb-admin.min.js"></script>
    <script src="../js/sb-admin-datatables.min.js"></script>
  </div>
</body>
</html>
