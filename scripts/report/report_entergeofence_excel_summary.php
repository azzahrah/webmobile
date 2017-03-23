<?php

/** Error reporting */
//error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
//date_default_timezone_set('Asia/Jakarta');

/** Include PHPExcel */
require_once '../Classes/PHPExcel.php';
require_once '../connection.php';
$from = isset($_GET['from']) ? mysql_real_escape_string($_GET['from']) : '2999-01-01';
$to = isset($_GET['to']) ? mysql_real_escape_string($_GET['to']) : '2999-03-01';
$vh_id = isset($_GET['vh_id']) ? mysql_real_escape_string($_GET['vh_id']) : '0';
$nopol = isset($_GET['nopol']) ? mysql_real_escape_string($_GET['nopol']) : '0';


//$imei = '357671032102359';
// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Joko Pitoyo")
        ->setLastModifiedBy("Joko Pitoyo")
        ->setTitle("GPS Tracking Report")
        ->setSubject("GPS Tracking Enter Geofence Report")
        ->setDescription("GPS Tracking Report")
        ->setKeywords("GPS Tracking Report")
        ->setCategory("GPS Tracking Report");

$headerTitle = array('Nopol','Geofence', 'jumlah');
$columns = array('A', 'B','C');
$columnsWidth = array(20, 20,20);
$styleArray = array(
    'borders' => array(
        'outline' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => '00000000'),
        ),
    ),
);

// Rename worksheet
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('Enter Geofence Report');


//Set Width
for ($i = 0; $i < count($columns); $i++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension($columns[$i])->setWidth($columnsWidth[$i]);
}

//Set Title Header
for ($i = 0; $i < count($headerTitle); $i++) {
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($columns[$i] . '5', $headerTitle[$i]);
}


$c = 5; //Reset First /Row Column
//set Border
$objPHPExcel->getActiveSheet()->getStyle('A5:C5')->getFill()
        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FFCABDBD');
// Add some data
$objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', 'Enter-Exit Geofence Report')
        ->setCellValue('A2', 'Periode :' . $from . ' S/D ' . $to);
//$query = "select id,count(alarm) as jumlah,vh_id,DATE(tdate) as tdate,alarm,gf,gf_id,lat,lng from track where alarm=7 and tdate>='" . $from . "' AND tdate<='" . $to . "' AND vh_id='" . $vh_id . "' group by alarm,DATE(tdate)";
$query = "select id,vh_id,tdate,alarm,gf,gf_id,lat,lng from track where  tdate>='" . $from . "' AND tdate<='" . $to . "' AND vh_id='" . $vh_id . "' ORDER BY tdate ASC";

//$query = "select id,vh_id,tdate,alarm,gf,gf_id,lat,lng from track where (alarm=7 or alarm=8) and tdate>='" . $from . "' AND tdate<='" . $to . "' AND vh_id='" . $vh_id . "'";
$result = mysql_query($query);
$response=array();
$startRow = 6;
if ($result) {
    $last_gf = "";
    while ($row = mysql_fetch_assoc($result)) {
        $gf = $row['gf'];
        if (($gf != "") && ($last_gf == "")) {
            $last_gf = $row['gf'];
            if (isset($response[$gf])) {
                $response[$gf] ++;
            } else {
                $response[$gf] = 1;
            }
        } else if (($gf != "") && ($last_gf != "") && ($gf != $last_gf)) {
            $last_gf = $row['gf'];
            if (isset($response[$gf])) {
                $response[$gf] ++;
            } else {
                $response[$gf] = 1;
            }
        }
        if ($gf == "") {
            $last_gf = "";
        }
    }
    foreach ($response as $key => $value) {
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $startRow, $nopol)
                ->setCellValue('B' . $startRow, $key)
                ->setCellValue('C' . $startRow, $value);
        $startRow++;
    }
}

mysql_close($connection);
//return;
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="entergeofence_report_' . $from . '_' . $to . '_' . $vh_id . '.xls"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
?>
