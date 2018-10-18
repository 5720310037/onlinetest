<?php
include "../check-user.php";
$lec_id   = $_SESSION['lec_id'];
$lec_name = $_SESSION['lec_name'];
$manage   = $_SESSION['manage'];
$sub_id   = $_SESSION['sub_id'];
$les_id   = $_SESSION['les_id'];
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
    #dialog {
      display: none;
      font-size: 14px !important;
    }
    div#dialog{
      background: lavender;
    }
    #form-question [type=text] {
      background: lavender;
      border: solid 1px gray;
      margin-bottom: 5px;
    }
    form label {
      display: inline-block;
      width: 150px;
      text-align: right;
    }
    form input[name=ques_detail] {
      width: 680px;
    }
    form input[name^=choice] {
      width: 400px;
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
    $('#send').click(function(event) { 
      var error = false;
      $(':text').each(function() {
        if($(this).val().length == 0) {
          alert('ท่านใส่ข้อมูลยังไม่ครบ');
          error  = true;
          return false;
        }
      });
      if(error) {
        return;
      }
      if($(':radio:checked').length == 0) {
        alert('ท่านยังไม่ได้กำหนดตัวเลือกที่เป็นคำตอบ');
        return;
      }
      $('form').submit();
    });
  
    $('#cancel').click(function() {
      window.location = 'lec-question.php?sub_id=<?php echo $sub_id."&les_id=".$les_id;?>';
    });
  });
  </script>
</head>

<body class="fixed-nav sticky-footer bg-dark" id="page-top">
  <?php
  include "navbar-lecturer.php";
  include "../dblink.php";
  $f= "";
  if(isset($_POST["submit"])) {
    if($_FILES['file']['name']) {
      $filename = explode(".", $_FILES['file']['name']);
      if($filename[1] == 'csv') {
        $handle = fopen($_FILES['file']['tmp_name'], "r");
        $j = 1;
        while ($data = fgetcsv($handle)) {
          if($j > 1) {
            $ques_detail = mysqli_real_escape_string($link, $data[0]);
            $choice[1]   = mysqli_real_escape_string($link, $data[1]);
            $choice[2]   = mysqli_real_escape_string($link, $data[2]);
            $choice[3]   = mysqli_real_escape_string($link, $data[3]);
            $choice[4]   = mysqli_real_escape_string($link, $data[4]);
            $ans         = mysqli_real_escape_string($link, $data[5]);
            $ques_level  = mysqli_real_escape_string($link, $data[6]);

            if($ques_detail != ""){
              $sql = "REPLACE INTO question VALUES('','$les_id','$ques_detail','$f','active','$ques_level')";
              if(mysqli_query($link, $sql)) {
                $ques_id = mysqli_insert_id($link);
                for($i = 1; $i <= 4; $i++) {
                  $answer = "no";
                  if($ans == $i) {
                    $answer = "yes";
                  } 
                  $sql = "REPLACE INTO choice VALUES('','$ques_id','$choice[$i]','$answer')";
                  mysqli_query($link, $sql);
                }
                //$msg = "done!";
                //$img = "ok.png";
              }
              else {
                //$msg = "error";
              }
            }
          } 
          $j++;
        }
        fclose($handle);
        //echo "Import Done!";
        echo '<script>window.location = "question.php?sub_id='.$sub_id.'&les_id='.$les_id.'"</script>';
      }
    }
  } 
  ?>

  <div class="content-wrapper">
    <div class="container-fluid">
      <Breadcrumbs>
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <?php 
            $q = "SELECT eng_name FROM subject WHERE sub_id = '$sub_id'";
            $r = mysqli_query($link, $q);
            $row = mysqli_fetch_array($r);
            echo "Subject: " .$sub_id. " " .$row['eng_name']. "<br>";

            $q = "SELECT les_name FROM lesson WHERE les_id = '$les_id'";
            $r = mysqli_query($link, $q);
            $row = mysqli_fetch_array($r);
            echo "Lesson Title: " .$row['les_name'];
            mysqli_free_result($r);
            mysqli_close($link); 
          ?>
        </li>
      </ol>
      <div class="row">
        <div class="col-lg-5">
          <div class="card">
            <div class="card-header">
              Question file uploading form
            </div> 
            <div class="card-body"> 
              <form method="post" enctype="multipart/form-data">
                <div align="center">
                  <p> CSV file only: <input type="file" name="file"> </p>
                  <p> <input type="submit" name="submit" value="Import"></p>
                </div>
              </form>
            </div>
            <div class="card-footer small text-muted">
              <a href="question.php?<?php echo 'sub_id='.$sub_id.'&les_id='.$les_id;?>" class="previous">&laquo; Previous</a>
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
