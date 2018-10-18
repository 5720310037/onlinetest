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
    button#add-user {
      float: right;
    }
    #dialog {
      display: none;
      font-size: 14px !important;
    }
    #form-user [type=text] {
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
      $('#add-user').click(function() {
        $('#form-user')[0].reset();
        $('#action').val('add');
        showDialog();
      });

      $('button.edit').click(function() {
        var tr = $(this).parent().parent();

        $('#lec_name').val(tr.children(':eq(1)').text());
        $('#lec_id').val(tr.children(':eq(2)').text());
        $('#lec_pwd').val(tr.children(':eq(3)').text());
      
        $('#lec_id').val($(this).attr('data-id'));
        $('#action').val('edit');
        showDialog();
      }); 

      $('#send').click(function() {
        var data = $('#form-user').serializeArray();
        ajaxSend(data);
      });

      $("#cancel").click(function () {
        $('#dialog').dialog('close');               
      });

      $('button.del').click(function() {
        var id = $(this).attr('data-id');
        if(!(confirm("Do you want to delete '" +id + "' ?"))) {
          return;
        }
        ajaxSend({'action': 'del', 'lec_id': id})
      });
    });

    function showDialog() {
      $('#dialog').dialog({
        title: 'User',
        width: 'auto',
        modal:true,
        position: { my: "center buttom", at: "center buttom", of: $('div.content-wrapper')}
      });
    } 

    function ajaxSend(dataJSON) {
      $.ajax({
        url: 'user-action.php',
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
              <i class="fa fa-user"></i> View Users
              <button id="add-user"><i class="fa fa-plus"></i> Add User</button>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <?php 
                  require ('../dblink.php');
                  $q = "SELECT lec_id, lec_name, lec_pwd FROM lecturer ORDER BY lec_id";    
                  $r = mysqli_query($link, $q);
                
                  echo '<table class="table table-bordered table-sm" id="dataTable" width="100%" cellspacing="0">
                  <thead class="thead-light">
                    <tr align="center">
                      <th>No.</th>
                      <th>Lecturer</th>
                      <th>Username</th>
                      <th>Password</th>
                      <th>Edit</th>
                      <th>Delete</th>
                    </tr>
                  </thead>';

                  $order = 1;
                  $bg = '#eeeeee'; 
                  while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
                    $bg = ($bg=='#eeeeee' ? '#ffffff' : '#eeeeee');
                      echo '<tr bgcolor="' . $bg . '">
                      <td align="center">' . $order . '</td>
                      <td>' . $row['lec_name'] . '</td>
                      <td>' . $row['lec_id'] . '</td>
                      <td>' . $row['lec_pwd'] . '</td>
                      <td align="center">
                        <button class="edit" data-id="'.$row['lec_id'].'">Edit</button>
                      </td>
                      <td align="center">
                        <button class="del" data-id="'.$row['lec_id'].'">Delete</button>
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
      </div>
    </div>
    <!-- /.container-fluid-->
    <div id="dialog">
      <form id="form-user">
        <input type="hidden" name="action" id="action" value="">
        
        <label>Please create a user:</label><br>
        <input type="text" name="lec_name" id="lec_name"  placeholder="Name-surename"><br>
        <input type="text" name="lec_id" id="lec_id" maxlength="10" placeholder="Username"><br>
        <input type="text" name="lec_pwd" id="lec_pwd" maxlength="10" placeholder="Password"><br>
        <button type="button" id="send">Submit</button> 
        <button type="button" id="cancel">Cancel</button>
      </form>
    </div>
    <?php include "../footer-modal.php";?>
  </div>
</body>
</html>
