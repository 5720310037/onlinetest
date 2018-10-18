<?php 
include "../check-user.php";
$lec_id   = $_SESSION['lec_id'];
$lec_name = $_SESSION['lec_name'];
$manage   = $_SESSION['manage'];

if(isset($_GET['sub_id'])) {
  $sub_id = $_GET['sub_id'];
  $_SESSION['sub_id'] = $sub_id;
} elseif(isset($_POST['sub_id'])) {
  $sub_id = $_POST['sub_id'];
  $_SESSION['sub_id'] = $sub_id;
} else {
  echo 'Error!';
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
    button#add-lesson {
      float: right;
    }
    #dialog {
      display: none;
      font-size: 14px !important;
    }
    #form-lesson [type=text] {
      width: 370px;
      background: lavender;
      border: solid 1px gray;
      padding: 3px;
      margin-bottom: 5px;
      font-size: 14px;
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
    $('#add-lesson').click(function() {
      $('#form-lesson')[0].reset();
      $('#action').val('add');
      showDialog();
    });

    $('#send').click(function() {
      var data = $('#form-lesson').serializeArray();
      ajaxSend(data);
    });

    $('button.edit').click(function() {
      var tr = $(this).parent().parent();

      $('#les_name').val(tr.children(':eq(1)').text());
      $('#les_id').val($(this).attr('data-id'));
      $('#action').val('edit');
      showDialog();
    }); 

    $('button.del').click(function() {
      if(!(confirm("Do you want to delete this lesson?"))) {
        return;
      }
      var id = $(this).attr('data-id');
      ajaxSend({'action': 'del', 'les_id': id})
    });

    $('#cancel').click(function () {
      $('#dialog').dialog('close');               
    });
  });
  
  function showDialog() {
    $('#dialog').dialog({
      title: 'Lesson title',
      width: 'auto',
      modal:true,
      position: { my: "center buttom", at: "center buttom", of: $('div.content-wrapper')}
    });
  }

  function ajaxSend(dataJSON) {
    $.ajax({
      url: 'lesson-action.php',
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
            $q = "SELECT eng_name, thai_name FROM subject WHERE sub_id = '$sub_id'";
            $r = mysqli_query($link, $q);
            $row = mysqli_fetch_array($r);
            echo "Subject: " . $sub_id . " " . $row[0];
          ?>
          <button id="add-lesson"> <i class="fa fa-plus"></i> Add Lesson</button> 
        </div> 
        <div class="card-body"> 
          <div class="table-responsive"> 
            <?php
              $q = "SELECT les_id, les_name FROM lesson 
                    WHERE sub_id = '$sub_id' ORDER BY les_id ASC"; 
              $r = mysqli_query($link, $q);
              echo '<table class="table table-bordered table-sm table-striped" id="dataTable" width="100%" cellspacing="0">
              <thead class="thead-light"> 
                <tr align="center"> 
                  <th>No.</th> 
                  <th>Lesson Name</th> 
                  <th>Quizzes</th>
                  <th>View</th>
                  <th>Edit/Delete</th> 
                </tr> 
              </thead>'; 
              $order = 1; 
              while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
                $les_id = $row['les_id'];
                $q1 = "SELECT COUNT(*) as count FROM question WHERE les_id = '$les_id'";
                $r1 = mysqli_query($link, $q1);
                $row1  = mysqli_fetch_assoc($r1);
                $count = $row1['count'];
                echo '<tr>
                  <td align="center">Lesson '.$order.'</td>
                  <td>'.$row['les_name'].'</td>
                  <td align="center">'.$count.'</td> 
                  <td align="center"> 
                    <a href="question.php?sub_id='.$sub_id.'&les_id='.$les_id.'"> View </a> </td> 
                  <td align="center"> 
                    <button class="edit" data-id="'.$row['les_id'].'">Edit</button> 
                    <button class="del" data-id="'.$row['les_id'].'">Delete</button>
                  </td>
                </tr>';
                $order++;
              }
              echo '</table>';
              mysqli_free_result($r);
            ?>
          </div>
        </div>
        <div class="card-footer small text-muted">
          <a href="subject.php" class="previous">&laquo; Previous</a>
        </div>
      </div>
    </div>
    <!-- /.container-fluid-->
    <div id="dialog">
      <form id="form-lesson">
        <input type="hidden" name="action" id="action">
        <input type="hidden" name="les_id" id="les_id">

        <label>Please input lesson title:</label><br>
        <input type="text" name="les_name" id="les_name" placeholder="Text here"><br><br>
        <div class="text-center">
          <button type="button" id="send">Submit</button> 
          <button type="button" id="cancel">Cancel</button>
        </div>
      </form>
    </div>
    <?php include "../footer-modal.php";?>
  </div>
</body>
</html>
