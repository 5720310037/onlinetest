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
  $sub_id = $_POST["sub_id"];
  $term   = $_POST["term"];
  $year   = $_POST["year"];
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
  
  <style type="text/css">
    button#add-group {
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
    $('#send').click(function() {
      var data = $('#form-edit').serializeArray();
      ajaxSend(data);
    });

    $('button.edit').click(function() {
      var tr = $(this).parent().parent();

      $('#lec_group').val(tr.children(':eq(0)').text());
      $('#lec_name').val(tr.children(':eq(1)').text());
      $('#lec_id').val(tr.text());

      $('#gr_id').val($(this).attr('data-id'));
      $('#action').val('edit');
      showDialog();
    }); 

    $('button.del').click(function() {
      if(!(confirm("Do you want to delete this group?"))) {
        return;
      }
      var id = $(this).attr('data-id');
      ajaxSend({'action': 'del', 'gr_id': id})
    });

    $('#add-group').click(function() {
      <?php
        if($sub_id == "" && $term == "" && $year == "") {
          echo "alert('Please click \'View\' button!')";
        } else {
          echo "window.location.href='group-user.php';";
        }
      ?>
    });

    $('#cancel').click(function () {
      $('#dialog').dialog('close');               
    });
  });

  function showDialog() {
    $('#dialog').dialog({
      title: 'Edit group',
      width: 'auto',
      modal:true,
      position: { my: "center buttom", at: "center buttom", of: $('div.content-wrapper')}
    });
  }

  function ajaxSend(dataJSON) {
    $.ajax({
      url: 'group-view-action.php',
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
          WHERE m.sub_id = s.sub_id AND lec_id = '$lec_id'";    
    $r = mysqli_query($link, $q);
  ?>
  <div class="content-wrapper">
    <div class="container-fluid">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <b>Group List</b>
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
          <button id="add-group">
            <i class="fa fa-plus"></i> Add Group
          </button>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <?php 
            $q = "SELECT lec_name, eng_name, gr_id, lec_group
                  FROM lecturer l, subject s, lec_group g 
                  WHERE l.lec_id = g.lec_id AND s.sub_id = g.sub_id 
                  AND term = '$term' AND year = '$year' AND g.sub_id = '$sub_id'";
            $r =  mysqli_query($link, $q); 
            echo '<table class="table table-bordered table-sm table-striped" id="dataTable" 
                    width="100%" cellspacing="0">';
            echo '<thead class="thead-light">
                    <tr align="center">
                      <th>Group</th>
                      <th>Lecturer</th>
                      <th>Subject</th>
                      <th>Edit</th>
                      <th>Delete</th>
                    </tr>
                  </thead>';
            while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
            echo '<tr>
                  <td align="center">' . $row['lec_group'] . '</td>
                    <td>' . $row['lec_name'] . '</td>
                    <td align="center">' . $sub_id. ' ' . $row['eng_name'] . '</td>
                    <td align="center"> <button class="edit" data-id="'.$row['gr_id'].'">Edit</button> </td>
                    <td align="center"> <button class="del" data-id="'.$row['gr_id'].'">Delete</button> </td>
                  </tr>';
            }
            echo '</table>';
            mysqli_free_result($r);
            mysqli_close($link);
            ?>
          </div>
        </div>
      </div>
    </div>
    <!-- /.container-fluid-->
    <div id="dialog">
      <form id="form-edit">
        <input type="hidden" name="action" id="action">
        <input type="hidden" name="gr_id" id="gr_id">

        <label>Group: </label>
        <input type="text" name="lec_group" id="lec_group" disabled><br>
        <label>Last Lecturer: </label>
        <input type="text" name="lec_name" id="lec_name" disabled><br>
        <label>New Lecturer: </label>
        <select name="lec_id">
          <?php
            include "../dblink.php";
            $q = "SELECT lec_id, lec_name FROM lecturer ORDER BY lec_name ASC";
            $r = mysqli_query($link, $q);
            while($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
            ?>
              <option value="<?=$row["lec_id"];?>"><?=$row["lec_name"];?></option>
            <?php
            }
            mysqli_free_result($r);
            mysqli_close($link);
          ?>
        </select><br>
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
