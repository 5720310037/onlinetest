<?php
require_once("student-pdfdb.php");
$group_id  = $_GET['group_id'];
$db_handle = new DBController();
$result = $db_handle->runQuery("SELECT * FROM enrollment WHERE group_id = '$group_id'");
$header = $db_handle->runQuery("SELECT `COLUMN_NAME` 
FROM `INFORMATION_SCHEMA`.`COLUMNS` 
WHERE `TABLE_SCHEMA`='onlinetest' 
    AND `TABLE_NAME`='enrollment'");

require('../fpdf/fpdf.php');
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Times','B',10);
//$pdf->SetTextColor(25, 144, 155);
foreach($header as $heading) {
	foreach($heading as $column_heading)
		$pdf->Cell(30,6,$column_heading,1);
}
foreach($result as $row) {
	$pdf->SetFont('Times','',10);
	//$pdf->SetTextColor(25, 144, 155);	
	$pdf->Ln();
	foreach($row as $column)
		$pdf->Cell(30,6,$column,1);
}
$pdf->Output();
?>