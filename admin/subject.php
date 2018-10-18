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
    button#add-subject {
      float: right;
    }
    #dialog {
      display: none;
      font-size: 14px !important;
    }
    #form-subject [type=text] {
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
      $('#add-subject').click(function() {
        $('#form-subject')[0].reset();
        $('#action').val('add');
        showDialog();
      });

      $('#send').click(function() {
        var data = $('#form-subject').serializeArray();
        ajaxSend(data);
      });

      $("#cancel").click(function () {
        $('#dialog').dialog('close');               
      });

      $('button.edit').click(function() {
        var tr = $(this).parent().parent();

        $('#thai_name').val(tr.children(':eq(2)').text());
        $('#eng_name').val(tr.children(':eq(3)').text());

        $('#sub_id').val($(this).attr('data-id'));
        $('#action').val('edit');
        showDialog();
      }); 

      $('button.del').click(function() {
        var id = $(this).attr('data-id');
        if(!(confirm("Do you want to delete subject '" +id + "' ?"))) {
          return;
        }
        ajaxSend({'action': 'del', 'sub_id': id})
      });
    });

    function showDialog() {
      $('#dialog').dialog({
        title: 'Subject',
        width: 'auto',
        modal:true,
        position: { my: "center buttom", at: "center buttom", of: $('div.content-wrapper')}
      });
    } 

    function ajaxSend(dataJSON) {
      $.ajax({
        url: 'subject-action.php',
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
      <div class="card mb-3">
        <div class="card-header">
          <i class="fa fa-book"></i> View Subjects
          <button id="add-subject"><i class="fa fa-plus"></i> Add Subject</button>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <?php 
              require ('../dblink.php');
              $q = "SELECT sub_id, thai_name, eng_name FROM subject ORDER BY sub_id";    
              $r = mysqli_query ($link, $q);
             
              echo '<table class="table table-bordered table-sm" id="dataTable" width="100%" cellspacing="0">
              <thead class="thead-light">
                <tr align="center">
                  <th>No.</th>
                  <th>Subject ID</th>
                  <th>Thai Name</th>
                  <th>Eng Name</th>
                  <th>Edit</th>
                  <th>Delete</th>
                </tr>
              </thead>
              ';

              $order=1;
              $bg = '#eeeeee'; 
              while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
                $bg = ($bg=='#eeeeee' ? '#ffffff' : '#eeeeee');
                  echo '<tr bgcolor="' . $bg . '">
                  <td align="center">' . $order .'</td>
                  <td align="center">' . $row['sub_id'] . '</td>
                  <td align="left">' . $row['thai_name'] . '</td>
                  <td align="left">' . $row['eng_name'] . '</td>
                  <td align="center">
                    <button class="edit" data-id="'.$row['sub_id'].'">Edit</button>
                  </td>
                  <td align="center">
                    <button class="del" data-id="'.$row['sub_id'].'">Delete</button>
                  </td>
                </tr>
                ';
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
    <div id="dialog">
      <form id="form-subject">
        <input type="hidden" name="action" id="action" value="">
        <label>Please input boxes below:</label><br>
        <input type="text" name="sub_id" id="sub_id" maxlength="7"  placeholder="Subject ID"><br>
        <input type="text" name="thai_name" id="thai_name" maxlength="100" placeholder="Thai name"><br>
        <input type="text" name="eng_name" id="eng_name" maxlength="100" placeholder="Eng. name"><br>
        <button type="button" id="send">Submit</button> 
        <button type="button" id="cancel">Cancel</button>
      </form>
    </div>
    <?php include "../footer-modal.php";?>
  </div>
</body>
</html>
