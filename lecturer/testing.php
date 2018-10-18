<?php 
include "../check-user.php";
$lec_id   = $_SESSION['lec_id'];
$lec_name = $_SESSION['lec_name'];
$manage   = $_SESSION['manage'];

if(empty($_POST)){
  $sub_id="";
  $term="";
  $year="";
  $_SESSION['sub_id'] = "";
  $_SESSION['term'] = "";
  $_SESSION['year'] = "";
}else{
  $sub_id=$_POST["sub_id"];
  $term=$_POST["term"];
  $year=$_POST["year"];

  $_SESSION['sub_id'] = $sub_id;
  $_SESSION['term'] = $term;
  $_SESSION['year'] = $year;
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
    button#add-testing {
      float: right;
    }
    #dialog {
      display: none;
      font-size: 14px !important;
    }
    #form-edit [type=text] {
    
      background: lavender;
      border: solid 1px gray;
      padding: 3px;
      margin-bottom: 5px;
      font-size: 14px;
    }
    form label {
      display: inline-block;
      width: 90px;
      text-align: right;
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
    /*$('#add-lesson').click(function() {
      $('#form-lesson')[0].reset();
      $('#action').val('add');
      showDialog();
    }); */
    $('#add-testing').click(function() {
      <?php
        if($sub_id == "" && $term == "" && $year == "") {
          echo "alert('Please click \'View\' button!')";
        } else {
          echo "$('#form-add')[0].reset();
                $('#action').val('add');
                showDialog();";
        }
      ?>
    });

    $('button.edit').click(function() {
      var tr = $(this).parent().parent();

      $('#test_name').val(tr.children(':eq(1)').text());
      $('#detail').val(tr.children(':eq(2)').text());
      //$('#lec_id').val(tr.text());

      $('#test_id').val($(this).attr('data-id'));
      $('#action').val('edit');
      showDialog();
    }); 

    $('#send').click(function() {
      var data = $('#form-add').serializeArray();
      ajaxSend(data);
    });

    $('button.del').click(function() {
      if(!(confirm("If you Delete this Testing it will Affect 5 Tables??"))) {
        return;
      }
      var id = $(this).attr('data-id');
      ajaxSend({'action': 'del', 'test_id': id})
    });

    $('#bt-search').click(function() {
      $('#form-search').submit();
    });

    $('#cancel').click(function () {
      $('#dialog').dialog('close');               
    });
  });

  function showDialog() {
    $('#dialog').dialog({
      title: 'Add Testing',
      width: 'auto',
      modal:true,
      position: { my: "center buttom", at: "center buttom", of: $('div.content-wrapper')}
    });
  }

  function ajaxSend(dataJSON) {
    $.ajax({
      url: 'testing-action.php',
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
  <?php 
    include "navbar-lecturer.php";
    include "../dblink.php";
    $q = "SELECT s.sub_id as sub_id, eng_name FROM manage_status m, subject s 
          WHERE m.sub_id = s.sub_id AND  lec_id = '$lec_id'";    
    $r = mysqli_query($link, $q);
  ?>
  <div class="content-wrapper">
    <?php 
    if(mysqli_num_rows($r) > 0) { 
    // Check manage_status
    ?>
    <div class="container-fluid">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <b>Examination List</b>
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
          <button id="add-testing"> <i class="fa fa-plus"></i> Add Exam </button>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <?php 
            $q = "SELECT test_id, test_name, detail
                  FROM subject s, testing t
                  WHERE s.sub_id = t.sub_id 
                  AND term = '$term' AND year = '$year' AND t.sub_id = '$sub_id'
                  ORDER BY test_id DESC";
            $r = mysqli_query ($link, $q); 
            echo '<table class="table table-bordered table-sm table-striped" id="dataTable" 
                    width="100%" cellspacing="0">';
            echo '<thead class="thead-light">
                    <tr align="center">
                      <th>No.</th>
                      <th>Examination</th>
                      <th>Detail</th>
                      <th>Quizzes</th>
                      <th>Edit</th>
                      <th>Delete</th>
                    </tr>
                  </thead>';
            $order = 1;
            while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
              $test_id = $row['test_id'];

              $q1 = "SELECT COUNT(*) as count FROM test_ques WHERE test_id = '$test_id'";
              $r1 = mysqli_query($link, $q1);
              $row1  = mysqli_fetch_assoc($r1);
              $count = $row1['count'];
              echo '<tr>
                    <td align="center">' . $order . '</td>
                    <td><a href="test-question.php?test_id='.$test_id.'">' . $row['test_name'] . '</a></td>
                    <td>'.$row['detail'].'</td>
                    <td align="center">'.$count.'</td>
                    <td align="center"> 
                      <button class="edit" data-id="'.$test_id.'">Edit</button> </td>
                    <td align="center"> 
                      <button class="del" data-id="'.$test_id.'">Delete</button> </td>
                  </tr>';
                  $order++;
            }
            echo '</table>';
            mysqli_free_result ($r);
            mysqli_close($link);
            ?>
          </div>
        </div>
      </div>
    </div>
    <!-- /.container-fluid-->
    <?php
    // if do not have manage_status
    } else {
      echo '
      <div class="container-fluid">
        <!-- Breadcrumbs-->
        <ol class="breadcrumb">
          <li class="breadcrumb-item">
            <b>Sorry, you can not access Warehouse!</b>
          </li>
        </ol>
      </div> ';
      mysqli_close($link);
    } 
    ?>
    <div id="dialog">
      <form id="form-add">
        <input type="hidden" name="action" id="action" value="">
        <input type="hidden" name="test_id" id="test_id" value="">
        <input type="hidden" name="sub_id" value="<?php echo $sub_id;?>">
        <input type="hidden" name="term" value="<?php echo $term;?>">
        <input type="hidden" name="year" value="<?php echo $year;?>">

        <label>Sub ID: </label>
        <input type="text" value="<?php echo $sub_id;?>" disabled><br>
        <label>Term: </label>
        <input type="text" value="<?php echo $term;?>" disabled><br>
        <label>Year: </label>
        <input type="text" value="<?php echo $year;?>" disabled><br>

        <label>Test Name: </label>
        <input type="text" name="test_name" id="test_name"> <br>
        <label>Detail: </label>
        <input type="text" name="detail" id="detail"><br>

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
