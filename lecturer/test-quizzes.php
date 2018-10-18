<?php
include "../check-user.php";
$lec_id   = $_SESSION['lec_id'];
$lec_name = $_SESSION['lec_name'];
$manage   = $_SESSION['manage'];
$sub_id   = $_SESSION['sub_id'];
$term     = $_SESSION['term'];
$year     = $_SESSION['year'];

$test_id  = $_GET['test_id'];
$group_id = $_GET['group_id'];
$_SESSION['test_id']  = $test_id;
$_SESSION['group_id'] = $group_id;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Online Testing System</title>
  <style type="text/css">
    button#add-date {
      float: right;
    }
    #dialog {
      display: none;
      font-size: 14px !important;
    }
    form label {
      display: inline-block;
      width: 100px;
      text-align: right;
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
    $('#add-date').click(function() {
      $('#form-date')[0].reset();
      $('#action').val('add');
      showDialog();
    });

    $('#send').click(function() {
      var data = $('#form-date').serializeArray();
      ajaxSend(data);
    });

    $('#cancel').click(function () {
      $('#dialog').dialog('close');               
    });
  });
  
  function showDialog() {
    $('#dialog').dialog({
      title: 'Define examination date',
      width: 'auto',
      modal:true,
      position: { my: "center buttom", at: "center buttom", of: $('div.content-wrapper')}
    });
  }

  function ajaxSend(dataJSON) {
    $.ajax({
      url: 'test-quizzes-action.php',
      data: dataJSON,
      type: 'post',
      dataType: "html",
      beforeSend: function() {
        $.blockUI({message: '<h3>Sending data...</h3>'});
      },
      complete: function() {
        $.unblockUI();
        location.reload();
      }
    });
  }
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
            echo "<b>Subject:</b> " .$sub_id. " " .$row['eng_name']. "<br>";
            echo "<b>Term:</b> " . $term . " <b>Year:</b> " . $year . "<br>" ;

            $q = "SELECT test_name, DATE_FORMAT(date_test, '%d/%m/%Y') AS date_test, 
                                    TIME_FORMAT(time_start, '%H.%i') AS time_start,  
                                    TIME_FORMAT(time_end, '%H.%i') AS time_end 
                  FROM tb_datetest d, testing t 
                  WHERE group_id = '$group_id' 
                  AND d.test_id = '$test_id' AND d.test_id = t.test_id"; 
            $r = mysqli_query($link, $q);
            $row = mysqli_fetch_array($r);
            echo " <b>Examination:</b> " .$row['test_name'] ."<br>";
            echo "<b>Date exam:</b> ".$row['date_test']." <b>Time:</b> ".$row['time_start']." - ".$row['time_end'];
          ?>
          <button id="add-date"> <i class="fa fa-clock-o"></i> Date Exam</button> 
        </div> 
        <div class="card-body"> 
          <div class="table-responsive"> 
            <?php
              $q = "SELECT q.ques_id AS ques_id, ques_detail, ques_image
                    FROM question q, test_ques t 
                    WHERE q.ques_id = t.ques_id AND test_id = '$test_id'"; 
              $r = mysqli_query($link, $q);
                
              echo '<table class="table table-bordered table-sm" id="dataTable" width="100%" cellspacing="0">
                  <thead class="thead-light"> 
                    <tr align="center"> 
                      <th>No.</th> 
                      <th>Question</th> 
                    </tr> 
                  </thead> ';
              $order = 1; 
              while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
                $ques_id = $row['ques_id'];
                $sql = "SELECT * FROM choice WHERE ques_id = '$ques_id'";
                $r1 = mysqli_query($link, $sql);

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
                echo '</tr>'; 
                $order++; 
              }
              echo '</table>'; 
              mysqli_free_result ($r); 
              mysqli_close($link); 
            ?> 
          </div>
        </div>
        <div class="card-footer small text-muted">
          <a href="test-list.php" class="previous">&laquo; Previous</a>
        </div>
      </div>
    </div>
    <!-- /.container-fluid-->
    <div id="dialog">
      <form id="form-date">
        <input type="hidden" name="action" id="action">
        <label>Date Exam: </label>
        <input type="date" name="date"> <br>
        <label>Time Start: </label>
        <input type="time" name="time_start"><br>
        <label>Time End: </label>
        <input type="time" name="time_end"><br>
        <div class="text-center">
          <button type="button" id="send">Submit</button> 
          <button type="button" id="cancel">Cancel</button>
        </div>
      </form>
    </div>
    <?php include "../footer-modal.php" ?>
  </div>
</body>
</html>
