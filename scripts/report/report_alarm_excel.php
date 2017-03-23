<?php

/** Error reporting */
//error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
//date_default_timezone_set('Europe/London');


/** Include PHPExcel */
require_once '../Classes/PHPExcel.php';
require_once '../connection.php';

include('../../php_script/session.php');
include("../../php_script/connection.php");

$vh_id = isset($_GET['vh_id']) ? mysql_real_escape_string($_GET['vh_id']) : 0;
$alarm = isset($_GET['alarm']) ? mysql_real_escape_string($_GET['alarm']) : 0;
$from = isset($_GET['from']) ? mysql_real_escape_string($_GET['from']) : 0;
$to = isset($_GET['to']) ? mysql_real_escape_string($_GET['to']) : 0;
$nopol = isset($_GET['nopol']) ? mysql_real_escape_string($_GET['nopol']) : 'License Number';

//$imei = '357671032102359';
// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Joko Pitoyo")
        ->setLastModifiedBy("Joko Pitoyo")
        ->setTitle("GPS Tracking Report")
        ->setSubject("GPS Tracking Alarm Report")
        ->setDescription("GPS Tracking Report")
        ->setKeywords("GPS Tracking Report")
        ->setCategory("GPS Tracking Report");

$headerTitle = array('Nopol', 'Date', 'Alarm', 'Address', 'POI', 'Speed', 'Direction');
$columns = array('A', 'B', 'C', 'D', 'E', 'F', 'G');
$columnsWidth = array(20, 20, 20, 20, 20, 20, 20, 20, 20, 20);
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
$objPHPExcel->getActiveSheet()->setTitle('Alarm Report');


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
$objPHPExcel->getActiveSheet()->getStyle('A5:G5')->getFill()
        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FFCABDBD');
// Add some data
$objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', 'License :' . $nopol)
        ->setCellValue('A2', 'Alarm Report')
        ->setCellValue('A3', 'Periode :' . $from . ' S/D ' . $to);

$tempArray = array();

$where = " where tdate>='" . $from . "' AND tdate<='" . $to . "' order by tdate ASC";
$result = mysql_query("SELECT * from track_alarm " . $where);

if ($result) {
    $rows = array();
    while ($r = mysql_fetch_assoc($result)) {
        $tempArray[] = $r;
    }
} else {
    $root["msg"] = "Error:" . mysql_error();
}
function formatAngle($angle) {
    if ((($angle >= 0) && ($angle < 22)) || ($angle >= 337))
        return "Utara";
    if (($angle >= 22) && ($angle < 67))
        return "Timur Laut";
    if (($angle >= 67) && ($angle < 112))
        return "Timur";
    if (($angle >= 112) && ($angle < 157))
        return "Tenggara";
    if (($angle >= 157) && ($angle < 202))
        return "Selatan";
    if (($angle >= 202) && ($angle < 247))
        return "Barat Daya";
    if (($angle >= 247) && ($angle < 292))
        return "Barat";
    if (($angle >= 292) && ($angle < 337))
        return "Barat Laut";
    return $angle;
}
function formatAlarm($alarm) {
    $str = "N/A";
    switch ($alarm) {
        case 1:
            $str = "SOS ALARM";
            break;
        case 2:
            $str = "POWER CUT ALARM";
            break;
        case 3:
            $str = "LOW POWER";
            break;
        case 4:
            $str = "SHOCK ALARM";
            break;
        case 5:
            $str = "OVER SPEED ALARM";
            break;
        case 6:
            $str = "LOW SPEED ALARM";
            break;
        case 7:
            $str = "GEOFENCE IN";
            break;
        case 8:
            $str = "GEOFENCE OUT";
            break;
        case 9:
            $str = "OVERTIME PARK";
            break;
        case 10:
            $str = "MOVE ALARM";
            break;
        case 11:
            $str = "OVERTIME ALARM";
            break;
        case 12:
            $str = "CHANGE OIL";
            break;
        case 13:
            $str = "OUT OF ROUTE";
            break;
        case 14:
            $str = "IO1 OPEN";
            break;
        case 15:
            $str = "IO2 OPEN";
            break;
        case 16:
            $str = "IO3 OPEN";
            break;
        case 17:
            $str = "IO4 OPEN";
            break;
        case 18:
            $str = "IO1 CLOSE";
            break;
        case 19:
            $str = "IO2 CLOSE";
            break;
        case 20:
            $str = "IO3 CLOSE";
            break;
        case 21:
            $str = "IO4 CLOSE";
            break;
        case 22:
            $str = "GSM ANTTENA CUT";
            break;
        case 23:
            $str = "GPS JAMMED";
            break;
    }
    return $str;
}

$startRow = 6;
//'Nopol', 'Date', 'Alarm', 'Address', 'POI', 'Speed', 'Direction'
foreach ($tempArray as $key => $value) {
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $startRow, $nopol)
            ->setCellValue('B' . $startRow, $value['tdate'])
            ->setCellValue('C' . $startRow, formatAlarm($value['alarm']))
            ->setCellValue('D' . $startRow, $value['address'])
            ->setCellValue('E' . $startRow, $value['poi'])
            ->setCellValue('F' . $startRow, $value['speed'])
            ->setCellValue('G' . $startRow, formatAngle($value['angle']));
    $startRow++;
}

mysql_close($connection);
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="alarm_report_' . $from . '_' . $to . '_' . $vh_id . '.xls"');
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