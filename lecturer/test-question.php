<?php
include "../check-user.php";
$lec_id   = $_SESSION['lec_id'];
$lec_name = $_SESSION['lec_name'];
$manage   = $_SESSION['manage'];
$sub_id   = $_SESSION['sub_id'];
$term     = $_SESSION['term'];
$year     = $_SESSION['year'];

$test_id  = $_GET['test_id'];
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
    #dialog {
      display: none;
      font-size: 14px !important;
    }
    div#dialog{
    
    }
    #form-question [type=text] {
      background: lavender;
      border: solid 1px gray;
      margin-bottom: 5px;
    }
    form label {
      display: inline-block;
      width: 135px;
      text-align: right;
    }
    form input[name=ques_detail] {
      width: 600px;
    }
    form input[name^=choice] {
      width: 400px;
    }
    div.radio {
      width: 80px;
      text-align: right !important;
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
      location.href = 'generate.php?test_id=<?php echo $test_id;?>';
      //$('#form-question')[0].reset();
      //$('#action').val('add');
      //showDialog();
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
          document.location.reload(); //หลังการเปลี่ยนแปลง ให้โหลดเพจนั้นมาแสดงใหม่
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

    //not use yet
    $('#send').click(function() {
      var data = $('#form-question').serializeArray();
      ajaxSend(data);
    }); 

    /*$('button.edit').click(function() {
      var tr = $(this).parent().parent();
      //$('#les_id').val(tr.children(':eq(1)').text());
      $('#ques_detail').val(tr.children(':eq(0)').text());

      $('#les_id').val($(this).attr('data-id'));
      $('#action').val('edit');
      showDialog();
    }); */

    /*$('button.del').click(function() {
      if(!(confirm("Confirm?"))) {
        return;
      }
      var id = $(this).attr('data-id');
      ajaxSend({'action': 'del', 'ques_id': id})
    }); */
  });

  //not use yet
  function showDialog() {
    $('#dialog').dialog({
      title: 'Question',
      width: 'auto',
      modal:true,
      position: { my: "center buttom", at: "center buttom", of: $('div.content-wrapper')}
    });
  }

  //not use yet
  function ajaxSend(dataJSON) {
    $.ajax({
      url: 'question-action.php',
      type: 'post',
      data: dataJSON,
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
  <!-- Navigation-->
  <?php include "navbar-lecturer.php";?>
  <?php
    //include "dblink.php";
    //$q = "SELECT sub_id FROM manage_status WHERE lec_id = '$lec_id'";    
    //$r = mysqli_query($link, $q); // Run the query.
    //if(mysqli_num_rows($r) > 0) {
  ?>
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
            echo "<b>Term:</b> " . $term . " <b>Year:</b> " . $year . "<br>";

            $q = "SELECT test_name FROM testing WHERE test_id = '$test_id'";
            $r = mysqli_query($link, $q);
            $row = mysqli_fetch_array($r);
            echo " <b>Examination:</b> " .$row['test_name'];

            //mysqli_free_result ($r);
            //mysqli_close($link); 
          ?>
          <button id="add-question"> <i class="fa fa-plus"></i> Generate</button> 
        </div> 
        <div class="card-body"> 
          <div class="table-responsive"> 
            <?php
              $q = "SELECT q.ques_id AS ques_id, ques_detail, ques_image, ques_status, ques_level
                    FROM question q, test_ques t 
                    WHERE q.ques_id = t.ques_id AND test_id = '$test_id'"; 
              $r = mysqli_query($link, $q);
              // Table header: 
              echo '<table class="table table-bordered table-sm" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-light"> 
                    <tr align="center"> 
                      <th>No.</th> 
                      <th>Question</th> 
                      <th>Level</th>
                    </tr> 
                </thead>'; 
              // Fetch and print all the records.... 
              $order = 1; 
              while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
                $ques_id = $row['ques_id'];
                $sql = "SELECT * FROM choice WHERE ques_id = '$ques_id'";
                $r1  = mysqli_query($link, $sql);

                echo '<tr>';
                echo '<td align="center">' . $order . '.</td>';
                echo '<td>' . $row['ques_detail'] . '<br>';
                if($row['ques_image'] != null) {
                  echo '<p><img src="question-read-image.php?ques_id='.$ques_id.'"></p>';
                }
                while($ch = mysqli_fetch_array($r1)) {
                  $checked = "";
                  $answer = $ch['answer'];
                  if($answer == 'yes') {
                    $checked = "checked";
                  }
                  echo "&nbsp;&nbsp;&nbsp;<input type=\"radio\"  name=\"$ques_id\"  
                            value=\"{$ch['choice_id']}\" $checked>   
                          {$ch['choice_detail']}<br>"; 
                }
                echo '</td>';

                echo '<td>'.$row['ques_level'].'</td>';
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
          <a href="testing.php" class="previous">&laquo; Previous</a>
        </div>
      </div>
    </div>
    <!-- /.container-fluid-->

    <!--p>do not use yet</p-->
    <!--div id="dialog">
      <form method="post" enctype="multipart/form-data" id="form-question">
        <input type="hidden" name="action" id="action" value="">
        
        <label>Question:</label>
        <input type="text" name="ques_detail" id="ques_detail"><br>

        <input type="hidden" name="MAX_FILE_SIZE" value="1048576">
        <label>Image file (If have):</label> 
        <input type="file" name="file"><br> 
        <label></label> *The size of file is not over 1 MB<br><br>

        <label>Selector and answer:</label> 
        <input type="text" name="choice[1]"> <input type="radio" name="answer" value="1">(Answer)<br>
        <label></label>
        <input type="text" name="choice[2]"> <input type="radio" name="answer" value="2">(Answer)<br>
        <label></label>
        <input type="text" name="choice[3]"> <input type="radio" name="answer" value="3">(Answer)<br>
        <label></label>
        <input type="text" name="choice[4]"> <input type="radio" name="answer" value="4">(Answer)<br>

        <label>Question Level:</label>
        <select name="ques_level">
          <option value="A">Difficult</option>
          <option value="B">Medium</option>
          <option value="C">Easy</option>
          <option value="D">Not define</option>
        </select> <br>

        <button type="button" id="send">Submit</button> 
        <button type="button" id="cancel">Cancel</button>
      </form>
    </div-->
    <?php include "../footer-modal.php";?>
  </div>
</body>
</html>
