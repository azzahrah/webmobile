<?php

/** Error reporting */
//error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
//date_default_timezone_set('Europe/London');


/** Include PHPExcel */
require_once '../Classes/PHPExcel.php';
require_once '../connection.php';

//$req_dump = print_r($_REQUEST, TRUE);
//$fp = fopen('request.log', 'a');
//fwrite($fp, $req_dump);
//fclose($fp);

$from = isset($_GET['from']) ? mysql_real_escape_string($_GET['from']) : '2014-01-01';
$to = isset($_GET['to']) ? mysql_real_escape_string($_GET['to']) : '2014-03-01';
$vh_id = isset($_GET['vh_id']) ? mysql_real_escape_string($_GET['vh_id']) : '1';
$nopol = isset($_GET['nopol']) ? mysql_real_escape_string($_GET['nopol']) : 'License Number';


//$imei = '357671032102359';
// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Joko Pitoyo")
        ->setLastModifiedBy("Joko Pitoyo")
        ->setTitle("GPS Tracking Report")
        ->setSubject("GPS Tracking Trip Report")
        ->setDescription("GPS Tracking Report")
        ->setKeywords("GPS Tracking Report")
        ->setCategory("GPS Tracking Report");

$headerTitle = array('Nopol','Start', 'Stop', 'Status', 'Durasi', 'Latitude', 'Longitude');
$columns = array('A', 'B', 'C', 'D', 'E', 'F','G');
$columnsWidth = array(20, 20, 20, 20, 20, 20, 20, 20, 20,20);
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
$objPHPExcel->getActiveSheet()->setTitle('Hour Meter Report');


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
        ->setCellValue('A1', 'License :'. $nopol)
        ->setCellValue('A2', 'Hour Meter Report')
        ->setCellValue('A3', 'Periode :' . $from . ' S/D ' . $to);

function calcdiff($fdate, $todate) {
    $finish = new DateTime($todate);
    $start = new DateTime($fdate);
    $diff = $finish->diff($start);
    $diffStr = $diff->h . ":" . $diff->i . ":" . $diff->s;
    return $diffStr;
}

//$result = mysql_query("select * from view_trip_report where tdate>='".$fdate ."' AND tdate<='". $udate."' AND vh_id='".$vh_id."'");
//$sql = " SELECT MIN(id) AS id, MIN(tdate) AS `start`,MAX(tdate) AS finish,acc, " .
//        " SEC_TO_TIME(TIME_TO_SEC(MAX(tdate))-TIME_TO_SEC(MIN(tdate))) AS durasi, " .
//        " MIN(acc) AS state, COUNT(*) as cnt,lat,lng " .
//        " FROM ( SELECT  @r := @r + (@acc != acc) AS group_state, @acc := acc AS sn,s.* FROM ( SELECT  @r := 0, @acc := 0 ) vars, track s WHERE vh_id='" . $vh_id . "' AND tdate>='" . $fdate . "'  and tdate<='" . $udate . "' ORDER BY  tdate) q GROUP BY  group_state";
//
$query = "select * from track where tdate>='" . $from . "' AND tdate<='" . $to . "' AND vh_id='" . $vh_id . "'";
$result = mysql_query($query);

$tempArray = array();
$lastAcc = "";
$countLastState = 0;
$index = 0;
$firstData = true;
$prevEmpty = false;
$lastDate = "";
if ($result) {
    while ($row = mysql_fetch_assoc($result)) {
        $lastDate = $row['tdate'];
        if ($firstData == false) {
            switch ((int) $row['acc']) {
                case 0:
                    if ($tempArray[$index]['acc'] == 0) {
                        $tempArray[$index]['cnt'] ++;
                        $tempArray[$index]['finish'] = $row['tdate'];
                    } else {
                        switch ((int) $tempArray[$index]['acc']) {
                            case 1:
                                $tempArray[$index]['finish'] = $row['tdate'];
                                $tempArray[$index]['duration'] = calcdiff($tempArray[$index]['start'], $row['tdate']);

                                $index++;
                                $tempArray[$index]['acc'] = 0;
                                $tempArray[$index]['cnt'] = 1;
                                $tempArray[$index]['start'] = $row['tdate'];
                                $tempArray[$index]['finish'] = $row['tdate'];
                                $tempArray[$index]['lat'] = $row['lat'];
                                $tempArray[$index]['lng'] = $row['lng'];

                                //echo "Index:" . $index . "</br>";
                                break;
                            case -1:
                                $prevEmpty = true;
                                $tempArray[$index]['finish'] = $row['tdate'];
                                break;
                        }
                    }
                    break;
                case 1:
                    if ($tempArray[$index]['acc'] == 1) {
                        $tempArray[$index]['cnt'] ++;
                        $tempArray[$index]['finish'] = $row['tdate'];
                    } else {
                        switch ((int) $tempArray[$index]['acc']) {
                            case 0:
                                $tempArray[$index]['finish'] = $row['tdate'];
                                $tempArray[$index]['duration'] = calcdiff($tempArray[$index]['start'], $row['tdate']);
                                $index++;
                                $tempArray[$index]['acc'] = 1;
                                $tempArray[$index]['cnt'] = 1;
                                $tempArray[$index]['start'] = $row['tdate'];
                                $tempArray[$index]['finish'] = $row['tdate'];
                                $tempArray[$index]['duration'] = "";
                                $tempArray[$index]['lat'] = $row['lat'];
                                $tempArray[$index]['lng'] = $row['lng'];
                                //  echo "Index:" . $index . "</br>";
                                break;
                            case -1:
                                $prevEmpty = true;
                                $tempArray[$index]['finish'] = $row['tdate'];
                                break;
                        }
                    }
                    break;
                case -1:
                    if ($prevEmpty == false) {
                        $prevEmpty = true;
                        $tempArray[$index]['finish'] = $row['tdate'];
                    }
                    break;
            }
        } else {
            if ((int) $row['acc'] == 1 || (int) $row['acc'] == 0) {
                $tempArray[$index]['acc'] = (int) $row['acc'];
                $tempArray[$index]['start'] = $row['tdate'];
                $tempArray[$index]['finish'] = $row['tdate'];
                $tempArray[$index]['duration'] = "";
                $tempArray[$index]['cnt'] = 1;
                $tempArray[$index]['lat'] = $row['lat'];
                $tempArray[$index]['lng'] = $row['lng'];
                $firstData = false;
                //echo "Index:" . $index . "</br>";
            }
        }
    }
    $tempArray[$index]['finish'] = $lastDate;
    $tempArray[$index]['duration'] = calcdiff($tempArray[$index]['start'], $lastDate);
    // return;
    //$b = array('m' => 'monkey', 'foo' => 'bar', 'x' => array('x', 'y', 'z'));
    // print_r($b); // $results now contains output from print_r
    //echo "Total Array:". count($tempArray);
    $startRow = 6;
    for ($i = 0; $i < count($tempArray); $i++) {
        //echo $tempArray[$i]['start'];
        //  print_r($tempArray[$i]['start']);
        //$finish = new DateTime($tempArray[$i]['finish']);
        //$start = new DateTime($tempArray[$i]['start']);
        //$diff = $finish->diff($start);
        //$diff->h . ":" . $diff->i . ":" . $diff->s
        //$dateDiff=$diff->H .":".$diff->i .":".$diff->s;
         $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $startRow, $nopol)
                    ->setCellValue('B' . $startRow, $tempArray[$i]['start'])
                    ->setCellValue('C' . $startRow, $tempArray[$i]['finish'])
                    ->setCellValue('D' . $startRow, (int) $tempArray[$i]['acc'] == 1 ? "ON" : "OFF" ) //. " : " . $tempArray[$i]['cnt']
                    ->setCellValue('E' . $startRow, $tempArray[$i]['duration'])
                    ->setCellValue('F' . $startRow, $tempArray[$i]['lat'])
                    ->setCellValue('G' . $startRow, $tempArray[$i]['lng']);
            $startRow++;
    }
    //  return;
//    $start_date = new DateTime("2012-02-10 11:26:00");
//    $end_date = new DateTime("2012-04-25 01:50:00");
//    $interval = $start_date->diff($end_date);
//    echo "Result " . $interval->y . " years, " . $interval->m . " months, " . $interval->d . " days ";
    // return;
}
//if ($result) {
//    $startRow = 6;
//    $tempArray = array();
//    while ($row = mysql_fetch_assoc($result)) 
// {
//        if (((int) $row['cnt'] >= 3) and ((int) $row['acc'] == 1 || (int) $row['acc'] == 0)) {
//            array_push($tempArray, array(
//                'start' => $row['start'],
//                'finish' => $row['finish'],
//                'status' => ((int) $row['acc'] == 1) ? "ON" : "OFF",
//                'durasi' => $row['durasi'] . $row['cnt'],
//                'lat' => $row['lat'],
//                'lng' => $row['lng']
//            ));
//        }
//        //  echo $row['start'] . " " . $row['finish'] . " - " . $row['cnt'] . "</br>";
////         $objPHPExcel->setActiveSheetIndex(0)
////                ->setCellValue('A' . $startRow, $row['start'])
////                ->setCellValue('B' . $startRow, $row['finish'])
////                ->setCellValue('C' . $startRow, ((int)$row['acc']==1)?"ON":"OFF")
////                ->setCellValue('D' . $startRow, $row['durasi'])
////                ->setCellValue('E' . $startRow, $row['lat'])
////                ->setCellValue('F' . $startRow, $row['lng'])
////                ->setCellValue('G' . $startRow, $row['lng']);
//    }
//    for ($i = 0; $i < count($tempArray); $i++) {
//        $objPHPExcel->setActiveSheetIndex(0)
//                ->setCellValue('A' . $startRow, $tempArray[$i]['start'])
//                ->setCellValue('B' . $startRow, $tempArray[$i]['finish'])
//                ->setCellValue('C' . $startRow, $tempArray[$i]['status'])
//                ->setCellValue('D' . $startRow, $tempArray[$i]['durasi'])
//                ->setCellValue('E' . $startRow, $tempArray[$i]['lat'])
//                ->setCellValue('F' . $startRow, $tempArray[$i]['lng']);
//        $startRow++;
//    }
//}

mysql_close($connection);
//return;
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="hour_report_' . $from . '_' . $to . '_' . $vh_id . '.xls"');
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
