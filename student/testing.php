<?php 
include "../check-user.php";
if(!$_GET['test_id']) {
  echo "<script>alert('err');</script>";
  die("<h2>Require Subject ID</h2>");
}

$user     = $_SESSION['user'];
if($user != "student") {
  exit();
}

$std_name = $_SESSION['std_name'];
$group_id = $_SESSION['group_id'];

$test_id    = $_GET['test_id'];
$eng_name   = $_SESSION['eng_name'];
$term       = $_SESSION['term'];
$year       = $_SESSION['year'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Online Testing Student</title>
  <style type="text/css">
    button#bt-fin {
      float: right;
    }
    p#demo {
      text-align: center;
      font-size: 40px;
      color: blue;
      margin-top:0px;
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
  <script>
  $(function() {  
    $(':radio').change(function(event) {
      var test_id   = <?php echo $test_id; ?>;
      var ques_id   = event.target.name;
      var choice_id = event.target.value;
      
      $.ajax({
        url: 'select.php',
        type: 'post',
        data: {'test_id':test_id, 'ques_id':ques_id, 'choice_id':choice_id},
        dataType: 'script',
        beforeSend: function() {
          $('body').css({cursor: 'wait'});
        }, 
        complete: function() {
          $('body').css({cursor: 'default'});
        }
      });
    });
    
    $('#bt-fin').click(function() {
      if(confirm('หลังจากกดปุ่ม OK แล้ว คุณจะไม่สามารถกลับมาทำข้อสอบไหม่ได้!\n ยืนยันการเสร็จสิ้นการทำแบบทดสอบ?')) {
        var test_id = <?php echo $test_id; ?>;
        window.location = 'finish.php?test_id=' + test_id;
      }
    });
  });
  </script>
</head>

<body class="fixed-nav sticky-footer bg-dark" id="page-top">
  <?php 
    include "navbar-stu.php";
  ?>
  <div class="content-wrapper">
    <div class="container-fluid">
      <ol class="breadcrumb">
        <?php
          include "../dblink.php"; 
          $q = "SELECT test_name, 
                      DATE_FORMAT(date_test, '%d/%m/%Y'), 
                      TIME_FORMAT(time_start, '%H.%i'), 
                      TIME_FORMAT(time_end, '%H.%i'), 
                      date_test, time_start, time_end 
                FROM tb_datetest d, testing t 
                WHERE t.test_id = '$test_id' AND d.test_id = t.test_id AND group_id = '$group_id'"; 
          $r = mysqli_query($link, $q);
          $row = mysqli_fetch_array($r);
          $test_name = $row[0];
          $dt = "วันที่สอบ " .$row[1]." เวลา ".$row[2]." - ".$row[3];
          if($row[1] == "00/00/0000") {
            $dt = "วันที่สอบ ไม่กำหนด";
          }
          echo 'ข้อสอบ '.$test_name.' วิชา '.$eng_name.' <br>ภาคการศึกษา '.$term.' ปีการศึกษา '.$year.'<br>'.$dt; 
         ?>
      </ol>
      <?php
        //the time function is lower than now 5 hours 
        date_default_timezone_set("Asia/Bangkok");
        $now   = strtotime("now"); 
        $start = $row[4] . " " . $row[5];
        $end1   = $row[4] . " " . $row[6];
        $start = strtotime($start);
        $end   = strtotime($end1);
        //ถ้าเป็นผู้ทำแบบทดสอบ และกำหนดวันเวลาที่แน่นอนในการทำแบบทดสอบ
        //แล้วถ้าไม่อยู่ในช่วงวันเวลาที่กำหนดในการทำแบบทดสอบ จะไม่แสดงคำถาม
        if(($_SESSION['user'] == "student") && ($row[1] == "00/00/0000") ||
            (($now < $start) || ($now > $end))) {
          echo '<div class="card mb-3">
                  <div class="card-body">';
          echo 'ขณะนี้ไม่อยู่ในช่วงวันเวลาที่กำหนดในการทำแบบทดสอบ <br>
                หากท่านทำแบบทดสอบหัวข้อนี้ไปแล้ว แต่ยังไม่ได้ยืนยันการเสร็จสิ้นการทำแบบทดสอบ<br>
                ให้คลิกลิงก์ต่อไปนี้เพื่อยืนยัน มิฉะนั้นการทำแบบทดสอบในหัวข้อนี้ของท่านจะเป็นโมฆะ<br><br>
                <a href="finish.php?test_id='.$test_id.'">เสร็จสิ้นการทดสอบ</a>';
          //echo '<br>' . $now .'<br>'. $start.'<br>'. $end;
          echo '  </div>
              </div>';

          //before exit must include footer
          include "../footer-modal.php";
          exit;
        }
        //ถ้าเป็นผู้ทำแบบทดสอบ และเคยทำแบบทดสอบหัวข้อนี้ไปแล้ว ก็จะไม่อนุญาตให้ทำซ้ำอีก
        if(isset($_SESSION['std_id'])) {
          $std_id = $_SESSION['std_id'];
          $sql = "SELECT score FROM take_exam 
                  WHERE std_id = '$std_id' AND group_id = '$group_id' AND test_id = '$test_id'";
          $result = mysqli_query($link, $sql);
          $row = mysqli_fetch_array($result);
          if($row[0] != "") {
            mysqli_close($link);
            echo '<div class="card mb-3">
                  <div class="card-body">';
            echo 'ท่านได้ทำแบบสอบทดสอบหัวข้อนี้ไปแล้ว ไม่สามารถทำซ้ำได้อีก';
            echo '  </div>
              </div>';

            //before exit must include footer
            include "../footer-modal.php";
            exit;
          }
        }
      ?>
      <script>
      var end = new Date("<?php echo $end1; ?>").getTime();
      var x = setInterval(function() {
          var now = new Date().getTime();
        
          var distance = end - now;
          
          var days    = Math.floor(distance / (1000 * 60 * 60 * 24));
          var hours   = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
          var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
          var seconds = Math.floor((distance % (1000 * 60)) / 1000);
          
          document.getElementById("demo").innerHTML = hours + "h "
          + minutes + "m " + seconds + "s ";
          
          if (distance < 0) {
              clearInterval(x);
              document.getElementById("demo").innerHTML = "EXPIRED";
          }
      }, 1000);
      </script>
      <div class="row">
        <div class="col-lg-8">
          <div class="card mb-3">
            <div class="card-header" style="font-size: 20px;">
              <i class="fa fa-edit"></i> Examination Board
            </div>
            <div class="card-body">
              <div class="table-responsive" align="center">
                <?php
                  include "../vendor/pagination/pagination.php";
                  $q = "SELECT q.ques_id AS ques_id, ques_detail, ques_image
                        FROM question q, test_ques t 
                        WHERE q.ques_id = t.ques_id AND test_id = '$test_id'"; 
                  $r = page_query($link, $q, 1); 

                  echo '<table class="table table-bordered table-sm" width="100%" cellspacing="0">
                  <thead class="thead-light"> 
                    <tr align="center"> 
                      <th>No.</th> 
                      <th>Question</th> 
                    </tr> 
                  </thead> 
                  '; 
                  $order = 1; 
                  while ($data = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
                    $ques_id = $data['ques_id'];
                    $page    = $_GET['page'];

                    $sql = "SELECT * FROM choice WHERE ques_id = '$ques_id' ORDER BY RAND() ASC";
                    $r1  = mysqli_query($link, $sql);
                    echo '<tr>';
                    echo '<td align="center">' . $page . '.</td>';
                    echo '<td>' . $data['ques_detail'] . '<br>';
                    if($data['ques_image'] != null) {
                      echo '<p><img src="question-read-image.php?ques_id='.$ques_id.'"></p>';
                    }
                    //แสดง radio และตัวเลือกของคำถามนั้นๆ
                    while($ch = mysqli_fetch_array($r1)) {
                      //ถ้าเป็นผู้ทำแบบทดสอบ จะตรวจสอบว่าเคยเลือกตัวเลือกของคำถามข้อนั้นไว้ก่อนหรือไม่
                      $checked = "";
                      if(isset($_SESSION['std_id'])) {
                        $std_id = $_SESSION['std_id'];

                        $sql = "SELECT choice_id FROM std_ques 
                                WHERE test_id = '$test_id' AND std_id = '$std_id' 
                                  AND ques_id = '$ques_id'";
                        $choose = mysqli_query($link, $sql);
                        $row    = mysqli_fetch_array($choose);
                        $id     = $row[0];
                        if($id == $ch['choice_id']) {
                          $checked = "checked";
                        }
                      }
                      echo "&nbsp;&nbsp;&nbsp;<input type=\"radio\"  name=\"$ques_id\"  
                            value=\"{$ch['choice_id']}\" $checked>   
                          {$ch['choice_detail']}<br>"; 
                    }
                    echo '</td>';
                    echo '</tr>'; 
                    $order++; 
                  }
                  echo '</table><br>'; 
                  page_link_border("solid", "1px", "gray");
                  page_link_bg_color("lightblue", "pink");
                  page_link_color("blue");
                  page_cur_border("solid", "1px", "gray");
                  page_cur_bg_color("navy");
                  page_cur_color("white");
                  page_echo_prevnext();
                  echo '<br><br>';
                  mysqli_free_result($r); 
                  mysqli_close($link); 
                ?> 
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="card mb-3">
            <div class="card-header" style="font-size: 20px;">
              <i class="fa fa-clock-o"></i> เวลาที่เหลื่อ
              <?php
                if($page == $_total_rows){
                  echo '<button type="button" id="bt-fin">เสร็จสิ้นการสอบ</button>';
                }
              ?>
            </div>
            <div class="card-body">
              <p id="demo"></p>
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
