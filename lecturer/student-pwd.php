<?php 
include "../check-user.php";
$lec_id   = $_SESSION['lec_id'];
$lec_name = $_SESSION['lec_name'];
$manage   = $_SESSION['manage'];
$sub_id   = $_SESSION['sub_id'];
$term     = $_SESSION['term'];
$year     = $_SESSION['year'];

$group_id  = $_GET['group_id'];
$lec_group = $_GET['lec_group'];
$_SESSION['group_id']  = $group_id;
$_SESSION['lec_group'] = $lec_group;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Online Testing System</title>
  <style type="text/css">
    button#import-file {
      float: right;
    }
    button#export-pdf {
      float: right;
    }
    button#delete {
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
    $('#import-file').click(function() {
      location.href = 'student-file.php';
    });

    $('#delete').click(function() {
      var group_id     = <?php echo $group_id; ?>;
      var action = "delete";
      if(action == "delete") {
        if(!(confirm("Are you sure to delete these student data???"))) {
          return;
        }
      }
      $.ajax({
        url:'student-action.php',
        type:'post',
        data:{'group_id':group_id, 'action':action},
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
          <b>Student List</b>
        </li>
      </ol>
      <div class="card mb-3">
        <div class="card-header small">
          <?php
            include "../dblink.php";
            $q = "SELECT eng_name FROM subject WHERE sub_id = '$sub_id'";
            $r = mysqli_query($link, $q);
            $row = mysqli_fetch_array($r);
            $_SESSION['eng_name'] = $row['eng_name'];
            
            echo "<b>Subject:</b> " .$sub_id. " " .$row['eng_name']. "<br>";
            echo "<b>Term:</b> " . $term . " <b>Year:</b> " . $year . " <b>Group:</b> " .$lec_group;
          ?>
          <button id="import-file"> <i class="fa fa-file-excel-o"></i> Import</button>
          <button id="export-pdf" onclick="window.open('student-pdf.php?group_id=<?php echo $group_id;?>')"> <i class="fa fa-file-pdf-o"></i> To PDF </button>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <?php
              /*$q = "SELECT std_pwd, s.std_id AS std_id, std_name, major, faculty 
                        FROM enrollment e, student s 
                        WHERE e.std_id = s.std_id AND group_id = '$group_id'"; */
              $q = "SELECT std_pwd, s.std_id AS std_id, std_name, major, faculty 
                    FROM enrollment e 
                    INNER JOIN student s ON e.std_id = s.std_id 
                    WHERE group_id = '$group_id' ORDER BY std_id ASC";
              $r = mysqli_query($link, $q);
              echo '<table class="table table-bordered table-sm" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-light"> 
                  <tr align="center"> 
                    <th>No.</th> 
                    <th>Student ID</th> 
                    <th>Password</th>
                    <th>Name</th> 
                    <th>Major</th> 
                    <th>Faculty</th>
                  </tr> 
                </thead>'; 
              $order = 1; 
              while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
                echo '<tr>';
                echo '<td align="center">'.$order.'</td>';
                echo '<td align="center">'.$row['std_id'].'</td>';
                echo '<td>'.$row['std_pwd'].'</td>';
                echo '<td>'.$row['std_name'].'</td>';
                echo '<td align="center">'.$row['major'].'</td>';
                echo '<td align="center">'.$row['faculty'].'</td>';
                echo '</tr>';
                $order++; 
              }
              echo '</table>'; 
              mysqli_free_result($r); 
              mysqli_close($link); 
            ?> 
          </div>
        </div>
        <div class="card-footer small text-muted">
          <a href="student-list.php" class="previous">&laquo; Previous</a>
          <button id="delete"> <i class="fa fa-trash-o"></i> Delete All </button>
        </div>
      </div>
    </div>
    <!-- /.container-fluid-->
    <?php include "../footer-modal.php";?>
  </div>
</body>
</html>
