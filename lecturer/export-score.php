<?php
include('../dblink.php');
$test_id  = $_GET['test_id'];
$group_id = $_GET['group_id'];

if($test_id == "null") {
	$s = "SELECT t.std_id AS std_id, std_name, SUM(score) AS score 
      	  FROM take_exam t, student s
      	  WHERE t.std_id = s.std_id AND group_id = '$group_id' 
      	  GROUP BY std_id";
}else{
	$s = "SELECT t.std_id as std_id, std_name, score 
	  	  FROM take_exam t, student s 
	  	  WHERE t.std_id = s.std_id AND test_id = '$test_id' AND group_id = '$group_id'";
}

$r = mysqli_query($link, $s);

$columnHeader ='';
$columnHeader = "Stu ID"."\t"."Name"."\t"."Score"."\t";

$setData='';
while($rec = mysqli_fetch_assoc($r))
{
  $rowData = '';
  foreach($rec as $value)
  {
    $value = '"' . $value . '"' . "\t";
    $rowData .= $value;
  }
  $setData .= trim($rowData)."\n";
}

header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=score-record.xls");
header("Pragma: no-cache");
header("Expires: 0");

echo ucwords($columnHeader)."\n".$setData."\n";
?>
