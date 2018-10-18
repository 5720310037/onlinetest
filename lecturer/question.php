<?php
include "../check-user.php";
$lec_id   = $_SESSION['lec_id'];
$lec_name = $_SESSION['lec_name'];
$manage   = $_SESSION['manage'];
$sub_id   = $_SESSION['sub_id'];

if (isset($_GET['les_id'])) { 
  $les_id = $_GET['les_id'];
  $_SESSION['les_id'] = $les_id;
} elseif (isset($_POST['les_id'])) {
  $les_id = $_POST['les_id'];
  $_SESSION['les_id'] = $les_id;
} else {
  echo '<p class="error">This page has been accessed in error.</p>';
  exit();
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
    button#add-question {
      float: right;
    }
    button#import-file {
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
    $('#add-question').click(function() {
      location.href = 'question-add.php';
    });

    $('#import-file').click(function() {
      location.href = 'question-file.php';
    });

    $('td > button').click(function() {
      var id = $(this).attr('data-id');
      var action = $(this).text();
      if(action == "Delete") {
        if(!(confirm("Do you want to delete this question?"))) {
          return;
        }
      }
      $.ajax({
        url:'question-status-action.php',
        type:'post',
        data:{'id':id, 'action':action},
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

    $('button.edit').click(function() {
      var id = $(this).attr('data-id');
      location.href = 'question-edit.php?ques_id='+id;
    });
  });
  </script>
</head>

<body class="fixed-nav sticky-footer bg-dark" id="page-top">
  <?php include "navbar-lecturer.php";?>
  <div class="content-wrapper">
    <div class="container-fluid">
      <div class="card mb-3">
        <div class="card-header">
          <?php
            include "../dblink.php";
            $q = "SELECT eng_name FROM subject WHERE sub_id = '$sub_id'";
            $r = mysqli_query($link, $q);
            $row = mysqli_fetch_array($r);
            echo "Subject: " .$sub_id. " " .$row['eng_name']. "<br>";

            $q = "SELECT les_name FROM lesson WHERE les_id = '$les_id'";
            $r = mysqli_query($link, $q);
            $row = mysqli_fetch_array($r);
            echo "Lesson title: " .$row['les_name'];
          ?>
          <button id="add-question"> <i class="fa fa-plus"></i> Question</button> &nbsp;&nbsp;&nbsp;
          <button id="import-file"> <i class="fa fa-file-excel-o"></i> Import</button> 
        </div> 
        <div class="card-body"> 
          <div class="table-responsive"> 
            <?php
              $q = "SELECT * FROM question WHERE les_id = '$les_id' 
                    ORDER BY ques_id ASC";
              $r = mysqli_query($link, $q);
              echo '<table class="table table-bordered table-sm" id="dataTable" width="100%" cellspacing="0">
                  <thead class="thead-light"> 
                    <tr align="center"> 
                      <th>No.</th> 
                      <th>Question</th> 
                      <th>Level</th> 
                      <th>Status</th> 
                      <th>Edit</th> 
                      <th>Delete</th> 
                    </tr> 
                  </thead> '; 
              $order = 1; 
              while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
                $ques_id = $row['ques_id'];
                $sql = "SELECT * FROM choice WHERE ques_id = '$ques_id' 
                        ORDER BY choice_id ASC";
                $r1  = mysqli_query($link, $sql);

                echo '<tr>';
                echo '<td align="center">' . $order . '.</td>';
                echo '<td>' . $row['ques_detail'] . '<br>';
                if($row['ques_image'] != null) {
                  echo '<p><img src="question-read-image.php?ques_id='.$ques_id.'"></p>';
                }
                while($ch = mysqli_fetch_array($r1)) {
                  $checked = "";
                  $answer  = $ch['answer'];
                  if($answer == 'yes') {
                    $checked = "checked";
                  }
                  echo "&nbsp;&nbsp;&nbsp;<input type=\"radio\"  name=\"$ques_id\"  
                            value=\"{$ch['choice_id']}\" $checked>   
                          {$ch['choice_detail']}<br>"; 
                }
                echo '</td>';
                echo '<td align="center">' . $row['ques_level'] .'</td>';
                echo '<td align="center"> <button data-id="'. $ques_id .'">';
                //define question's status
                if($row['ques_status']=="active") {
                  echo "Active";
                } else {
                  echo "Inactive";
                }
                echo '</button> </td>';
                echo '<td align="center"> <button class="edit" data-id="'. $ques_id .'">Edit</button> </td>'; 
                echo '<td align="center"> <button data-id="'. $ques_id .'">Delete</button></td>'; 
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
          <a href="lesson.php?sub_id=<?php echo $sub_id;?>" class="previous">&laquo; Previous</a>
        </div>
      </div>
    </div>
    <!-- /.container-fluid-->
    <?php include "../footer-modal.php";?>
  </div>
</body>
</html>
