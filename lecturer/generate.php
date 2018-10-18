<?php
include "../check-user.php";
$l_id     = $_SESSION['lec_id'];
$lec_name = $_SESSION['lec_name'];
$manage   = $_SESSION['manage'];

$sub_id   = $_SESSION['sub_id'];
$term1    = $_SESSION['term'];
$year1    = $_SESSION['year'];
$test_id  = $_GET['test_id'];

if($_POST){
  include "../dblink.php";
  for($i=1; $i<=count($_POST['select2']); $i++) {
    $les_id = $_POST['les_id'][$i];
    $limit  = $_POST['select2'][$i];

    if($limit != 0){
      //define nums of question level
      $n      = floor($limit/4);
      $r      = $limit % 4;
      $level  = array("Hard", "Medium", "Easy", "Undefine");
      $lim    = array($n, $n+$r, $n, $n);

      for ($j=0; $j <4 ; $j++) { 
        $sql = "SELECT ques_id FROM question 
                WHERE les_id = '$les_id' AND ques_status = 'active' AND ques_level = '$level[$j]'
                ORDER BY RAND() LIMIT $lim[$j]";
        $r   = mysqli_query($link, $sql);

        while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
          $ques_id  = $row['ques_id'];
          $sql1 = "REPLACE INTO test_ques VALUES('$test_id', '$ques_id')";
          mysqli_query($link, $sql1);
        }
      }
    }
  }
  echo '<script>window.location = "test-question.php?test_id='.$test_id.'"</script>';
  mysqli_close($link);
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
    #input1 {
      width: 50px;
      height: 25px;
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
    $('#send').click(function(event) { 
      $('form').submit();
    });
    $('#cancel').click(function() {
      window.location = 'test-question.php?test_id=<?php echo $test_id;?>';
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
          <b>Generate quizzes</b> <br> 
        </li>
      </ol>
      <div class="row">
        <div class="col-lg-5">
          <div class="card">
            <div class="card-body">
              <form method="post">
                <div class="table-responsive"> 
                  <?php
                    include "../dblink.php";
                    $q = "SELECT les_id, les_name FROM lesson WHERE sub_id = '$sub_id'"; 
                    $r = mysqli_query($link, $q);
                    echo '<table class="table table-bordered table-sm" width="100%" cellspacing="0">
                      <thead class="thead-light"> 
                        <tr align="center"> 
                          <th>No.</th> 
                          <th>Lesson name</th> 
                          <th>Quizzes</th>
                          <th>Select</th>
                        </tr> 
                      </thead>'; 
                      
                    $order = 1; 
                    while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
                      $les_id = $row['les_id'];
                      $q1 = "SELECT COUNT(*) as count FROM question WHERE les_id = '$les_id'";
                      $r1 = mysqli_query($link, $q1);
                      $row1  = mysqli_fetch_assoc($r1);
                      $count = $row1['count'];
                      echo '<tr>
                          <td>'.$order.'</td>
                          <td align="left">'.$row['les_name'].'</td>
                          <td>'.$count.'</td> 
                          <td> 
                            <input type="hidden" name="les_id['.$order.']" value="'.$les_id.'">
                            <input type="hidden" name="select1" value="'.$count.'">
                            <input type="text" name="select2['.$order.']" id="input1"> 
                          </td> 
                      </tr>';
                      $order++;
                    }
                    echo '</table>';
                    mysqli_free_result($r);
                  ?>
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
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="../vendor/datatables/dataTables.bootstrap4.js"></script>
    <script src="../js/sb-admin.min.js"></script>
    <script src="../js/sb-admin-datatables.min.js"></script>
  </div>
</body>
</html>
