<?php 
include "../check-user.php";
$admin = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>OTS Administrator</title>
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">
  <link href="../css/sb-admin.css" rel="stylesheet">
  <style type="text/css">
    button#add-status {
      float: right;
    }
    #dialog {
      display: none;
      font-size: 14px !important;
    }
    #form-status [type=text] {
      width: 370px;
      background: lavender;
      border: solid 1px gray;
      padding: 3px;
      margin-bottom: 5px;
      font-size: 14px;
    }
  </style>
  <link href="../vendor/jquery/jquery-ui.min.css" rel="stylesheet">
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/jquery/jquery-ui.min.js"></script>
  <script src="../vendor/jquery/jquery.blockUI.js"></script>
  <script type="text/javascript">
    $(function() {
      $('#add-status').click(function() {
        $('#form-status')[0].reset();
        $('#action').val('add');
        showDialog();
      });

      $('#send').click(function() {
        var data = $('#form-status').serializeArray();
        ajaxSend(data);
      });

      $("#cancel").click(function () {
        $('#dialog').dialog('close');               
      });

      $('button.del').click(function() {
        var id1 = $(this).attr('lec_id');
        var id2 = $(this).attr('sub_id');
        if(!(confirm("Do you want to delete subject '" +id1+ " & " +id2+ "' ?"))) {
          return;
        }
        ajaxSend({'action': 'del', 'lec_id': id1, 'sub_id': id2})
      });
    });

    function showDialog() {
      $('#dialog').dialog({
        title: 'Manage Status',
        width: 'auto',
        modal:true,
        position: { my: "center buttom", at: "center buttom", of: $('div.content-wrapper')}
      });
    } 

    function ajaxSend(dataJSON) {
      $.ajax({
        url: 'status-action.php',
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
  <?php include "navbar-admin.php";?>
  <div class="content-wrapper">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-10">
          <div class="card mb-3">
            <div class="card-header">
              <i class="fa fa-table"></i> View Status
              <button id="add-status"><i class="fa fa-plus"></i> Add Status</button>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <?php 
                  include "../dblink.php";
                  $q = "SELECT lec_name, eng_name, m.lec_id as lec_id, m.sub_id as sub_id 
                        FROM manage_status m, lecturer l, subject s
                        WHERE m.lec_id = l.lec_id AND m.sub_id = s.sub_id ORDER BY sub_id";    
                  $r = mysqli_query($link, $q);
                  echo '<table class="table table-bordered table-sm" id="dataTable" width="100%" cellspacing="0">
                  <thead class="thead-light">
                    <tr align="center">
                      <th>No.</th>
                      <th>Lecturer</th>
                      <th>Subject</th>
                      <th>Delete</th>
                    </tr>
                  </thead>';

                  $order=1;
                  $bg = '#eeeeee'; 
                  while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
                    $bg = ($bg=='#eeeeee' ? '#ffffff' : '#eeeeee');
                      echo '<tr bgcolor="' . $bg . '">
                      <td align="center">' . $order . '</td>
                      <td>' . $row['lec_name'] . '</td>
                      <td>' . $row['sub_id'] . ' ' . $row['eng_name'] . '</td>
                      <td align="center">
                        <button class="del" lec_id="'.$row['lec_id'].'" sub_id="'.$row['sub_id'].'">Delete</button>
                      </td>
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
      </div>
    </div>
    <!-- /.container-fluid-->

    <div id="dialog">
      <form id="form-status">
        <input type="hidden" name="action" id="action" value="">

        <label>Please create a status:</label><br>
        <i class="fa fa-user"></i> Lecturer: 
        <select name="lec_id">
          <?php
            include "../dblink.php";
            $q = "SELECT lec_id, lec_name FROM lecturer";    
            $r = mysqli_query($link, $q);
            while($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
            ?>
              <option value="<?=$row["lec_id"];?>"><?=$row["lec_name"];?></option>
            <?php
            }
          ?>
        </select><br><br>
               
        <i class="fa fa-book"></i> Subject:
        <select name="sub_id">
          <?php
          $q = "SELECT sub_id, eng_name FROM subject";    
          $r = mysqli_query($link, $q);
          while($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
          ?>
            <option value="<?=$row["sub_id"];?>"><?=$row["sub_id"];?> <?=$row["eng_name"];?></option>
          <?php
          }
          mysqli_free_result ($r);
          mysqli_close($link);
          ?>
          </select> <br><br>
        <button type="button" id="send">Submit</button> 
        <button type="button" id="cancel">Cancel</button>
      </form>
    </div>
    
    <?php include "../footer-modal.php";?>
  </div>
</body>
</html>
