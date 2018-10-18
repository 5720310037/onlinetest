<?php
include "../check-user.php";
$l_id     = $_SESSION['lec_id'];
$lec_name = $_SESSION['lec_name'];
$manage   = $_SESSION['manage'];

$sub_id1  = $_SESSION['sub_id'];
$term1    = $_SESSION['term'];
$year1    = $_SESSION['year'];

if($_POST) {
  include "../dblink.php";
  $sub_id = $_POST['sub_id'];
  $term   = $_POST['term'];
  $year   = $_POST['year'];
 
  $sql = "SELECT lec_group FROM lec_group 
          WHERE sub_id = '$sub_id' AND term = '$term' AND year = '$year' 
          ORDER BY lec_group DESC LIMIT 1";
  $r   = mysqli_query($link, $sql);
  $row = mysqli_fetch_array($r);
  $grp = $row['lec_group'];

  $groups = array();
  for($i=0; $i < count($_POST['lec_id']); $i++){
    $gr = $i + 1 + $grp;
    $lec_id = $_POST['lec_id'][$i];

    $str = "('', '$term', '$year', '$gr', '$lec_id', '$sub_id')";
    array_push($groups, $str);
  }

  $value = implode(",", $groups);
  $sql   = "INSERT INTO lec_group VALUES $value";
  mysqli_query($link, $sql);
  mysqli_close($link);
  echo "<script>window.location = 'group-view.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Online Testing System</title>
 
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">
  <link href="../css/sb-admin.css" rel="stylesheet">
  <style type="text/css">
    form input {
      width: 200px;
      border: solid 1px gray;
      padding: 2px;
      color: blue;
    }
    form label {
      display: inline-block;
      width: 150px;
      text-align: left;
      padding: 5px;
    }
    form div {
      text-align:center;
      margin-top: 10px;
    }
    div.name {
      margin: 3px;
    }
    div#top{
      text-align: left;
    }
    div#center{
      text-align: left;
    }
    div#buttom{
      padding: 3px 0px;
    }
  </style>
  <link href="../vendor/jquery/jquery-ui.min.css" rel="stylesheet">
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/jquery/jquery-ui.min.js"></script>
  <script src="../vendor/jquery/jquery.blockUI.js"></script>
  <script>
  $(function() {
    var input = '<div class="name">';
        //input += '<label for="group">Group 1</label>';
        input += '<select name="lec_id[]"><?php
                    include "../dblink.php";
                    $q = "SELECT lec_id, lec_name FROM lecturer 
                          ORDER BY lec_name ASC";    
                    $r = mysqli_query($link, $q);
                    //$i=0;
                    while($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {?><option value="<?=$row["lec_id"];?>"><?=$row["lec_name"];?></option><?php
                    //$i++;
                    }
                    mysqli_close($link);?></select></div>';
    
    //เมื่อคลิกปุ่ม + ให้เพิ่มอิลิเมนต์ div(ซึ่งบรรจุ label & select) ลงไปในฟอร์ม
    $('#add').click(function() {
      if($('div.name').length == 50){
        return false;
      }
      $('#name-container').append(input);
    });
    
    //เมื่อคลิกปุ่ม - ให้ลบอิลิเมนต์ div(ซึ่งบรรจุ label & select) ตัวสุดท้ายออกจากฟอร์ม
    $('#remove').click(function() {
      if($('div.name').length == 1){
        return false;
      }
      //$('label:last').remove();
      //$('select:last').remove();
      $('select:last').parent().remove();
    });
    $('#add').click();

    $('#send').click(function(event) { 
      //$('form').submit();
      $.ajax({
          url: '<?php echo $_SERVER['PHP_SELF']; ?>',
          data: $('form').serializeArray(),
          dataType: "script",
          type: "post",
          beforeSend: function() {
            $('form').block({message: '<h2>กำลังส่งข้อมูล</h2>'});
          },
          complete: function() {
            $('form').unblock();
          }
      });
      window.location = 'group-view.php';
    });

    $('#cancel').click(function() {
      window.location = 'group-view.php';
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
          <b>Add Group and User</b>
        </li>
      </ol>
      <div class="row">
        <div class="col-lg-5">
            <div class="card">
              <div class="card-body">
                <form method="post">
                  <div id="top">
                    Subject:
                    <select name="sub_id">
                      <?php echo "<option value=\"$sub_id1\">".$sub_id1."</option>"; ?>
                    </select> &nbsp;&nbsp;&nbsp;
                    Term:
                    <select name="term">
                      <?php echo "<option value=\"$term1\">".$term1."</option>"; ?>
                    </select> &nbsp;&nbsp;&nbsp;
                    Year: 
                    <select name="year">
                      <?php echo "<option value=\"$year1\">".$year1."</option>"; ?>
                    </select>
                  </div>
                  <div id="center">
                    Groups:
                    <button type="button" id="add">+</button>
                    <button type="button" id="remove">-</button>
                    <div id="name-container"></div>
                  </div>
                  <div id="buttom">
                    <button type="button" id="send">Submit</button> 
                    <button type="button" id="cancel">Cancel</button>
                  </div>
                </form>
              </div>
            </div>
        </div>
      </div>
    </div>
    <!-- /.container-fluid-->
    <?php include "../footer-modal.php";?>
  </div>
</body>
</html>
