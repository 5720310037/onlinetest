<?php
include "../check-user.php";
$lec_id   = $_SESSION['lec_id'];
$lec_name = $_SESSION['lec_name'];
$manage   = $_SESSION['manage'];
$sub_id   = $_SESSION['sub_id'];
$les_id   = $_SESSION['les_id'];

if (isset($_GET['ques_id'])) { 
  $ques_id = $_GET['ques_id'];
}else{
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
      width: 85%;
    }
    form input[name^=choice] {
      width: 70%;
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
      window.location = 'question.php?sub_id=<?php echo $sub_id."&les_id=".$les_id;?>';
    });
  });
  </script>
</head>

<body class="fixed-nav sticky-footer bg-dark" id="page-top">
  <?php
    include "navbar-lecturer.php";
    include "../dblink.php";
    $msg = "";
    $img = "no.png";
    if($_POST) {
      $ques_id      = $_POST['ques_id'];
      $ques_detail  = $_POST['ques_detail'];
      $ques_level   = $_POST['ques_level'];
      $f   = "";
      $t   = "";
      $msg = "";

      if(is_uploaded_file($_FILES['file']['tmp_name']))  {
        $error =  $_FILES['file']['error'];
        if($error == 0) {
          include "../lib/IMager/imager.php";
          $img = image_upload('file');
          $img = image_to_jpg($img);
          $img = image_resize_max($img, 500, 200); //ให้ภาพกว้างไม่เกิน 200px สูงไม่เกิน 200px
          $f   = image_store_db($img, "image/jpeg");
        }
        else if($error == 1 || $error == 2) {
          $msg = "ไฟล์ที่อัปโหลดมีขนาดใหญ่เกินกำหนด";
        }
        else if($error == 4) {
          $msg = "เกิดข้อผิดพลาดในระหว่างอัปโหลดไฟล์";
        }
      }

      if($msg == "") {
        if($f == ""){
          $sql = "UPDATE question SET ques_detail = '$ques_detail', ques_level = '$ques_level'
                  WHERE ques_id = '$ques_id'";
        }else{
          $sql = "UPDATE question SET ques_detail = '$ques_detail', ques_image = '$f', 
                         ques_level = '$ques_level'
                  WHERE ques_id = '$ques_id'";
        }
        if(mysqli_query($link, $sql)) {
          //$ques_id = mysqli_insert_id($link);
          for($i = 1; $i <= count($_POST['choice']); $i++) {
            $choice_id     = $_POST['choice_id'][$i];
            $choice_detail = $_POST['choice'][$i];
            $answer = "no";
            if($_POST['answer'] == $i) {
              $answer = "yes";
            }
            $sql = "UPDATE choice SET choice_detail = '$choice_detail', answer = '$answer'
                    WHERE choice_id = '$choice_id'";
            mysqli_query($link, $sql);
          }
          //$msg = "done";
          //$img = "ok.png";
        }
        else {
          //$msg = "error";
        }
        echo '<script>window.location = "question.php?sub_id='.$sub_id.'&les_id='.$les_id.'"</script>';
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
            echo "Lesson title: " .$row['les_name'];
          ?>
        </li>
      </ol>
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-header">
              Add Question
            </div> 
            <?php  
              $q = "SELECT * FROM question WHERE ques_id = '$ques_id'"; 
              $r = mysqli_query($link, $q); 
              $row = mysqli_fetch_array($r);
            ?>
            <div class="card-body"> 
              <form method="post" enctype="multipart/form-data" id="form-question">
                <input type="hidden" name="action" id="action" value="">
                <input type="hidden" name="ques_id" id="ques_id" value="<?php echo $row['ques_id'] ?>">
                
                <label>Question:</label>
                <input type="text" name="ques_detail" id="ques_detail" value="<?php echo $row['ques_detail'] ?>"><br>

                <input type="hidden" name="MAX_FILE_SIZE" value="1048576">
                <label>Image file (If exist):</label> 
                <input type="file" name="file"><br> 
                <label></label> *The size of file is not over 1 MB<br><br>

                <label>Selector and answer:</label> 
                <?php 
                  $sql = "SELECT * FROM choice WHERE ques_id = '$ques_id'";
                  $r1  = mysqli_query($link, $sql);
                  $i   = 1;
                  while($ch = mysqli_fetch_array($r1)) {
                    $checked = "";
                    $answer  = $ch['answer'];
                    if($answer == 'yes') {
                      $checked = "checked";
                    }
                    echo "<input type=\"hidden\" name=\"choice_id[$i]\" value=\"{$ch['choice_id']}\">";
                    echo "<input type=\"text\" name=\"choice[$i]\" value=\"{$ch['choice_detail']}\"> ";
                    echo "<input type=\"radio\" name=\"answer\" value=\"{$i}\" $checked>(Answer)<br>";
                    if($i < 4){
                      echo "<label></label> ";
                    }
                    $i++;
                  }
                  mysqli_free_result($r1); 
                  mysqli_close($link); 
                ?> 
                <label>Question Level:</label>
                <select name="ques_level">
                  <option value="Hard">Hard</option>
                  <option value="Medium">Medium</option>
                  <option value="Easy">Easy</option>
                  <option value="Undefine">Undefine</option>
                </select> <br>
                <div class="text-center">
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
