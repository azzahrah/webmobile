<?php

/** Error reporting */
//error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
//date_default_timezone_set('Europe/London');

/** Include PHPExcel */
require_once '../Classes/PHPExcel.php';
require_once '../connection.php';

$vh_id = isset($_GET['vh_id']) ? mysql_real_escape_string($_GET['vh_id']) : 0;
$from = isset($_GET['from']) ? mysql_real_escape_string($_GET['from']) : 0;
$to = isset($_GET['to']) ? mysql_real_escape_string($_GET['to']) : 0;
$nopol = isset($_GET['nopol']) ? mysql_real_escape_string($_GET['nopol']) : '';

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

function get_alarm($alarm_id) {
    $str = $alarm_id;
    switch ((int) $alarm_id) {
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
            $str = "IDLE ALARM";
            break;
    }
    return $str;
}

function get_direction($a) {
    if (($a >= 337) || (($a >= 0) && ($a <= 22)))
        return "Utara";
    if (($a >= 22.5) && ($a <= 67))
        return "Timur Laut";
    if (($a >= 67.5) && ($a <= 112))
        return "Timur";
    if (($a >= 112.5) && ($a <= 157))
        return "Tenggara";
    if (($a >= 157.5) && ($a <= 202))
        return "Selatan";
    if (($a >= 202.5) && ($a <= 247))
        return "Barat Daya";
    if (($a >= 247.5) && ($a <= 292))
        return "Barat";
    if (($a >= 292.5) && ($a <= 337))
        return "Barat Laut";
}

function calcdiff($fdate, $todate) {
    $finish = new DateTime($todate);
    $start = new DateTime($fdate);
    $diff = $finish->diff($start);
    return $diff;
    //$diffStr = $diff->h . ":" . $diff->i . ":" . $diff->s;
    //return $diffStr;
}

function distance($lat1, $lon1, $lat2, $lon2) {
    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    return ($miles * 1000 * 1.609344); //meter
}

$query = "select * from track where tdate>='" . $from . "' AND tdate<='" . $to . "' AND vh_id='" . $vh_id . "' ORDER by tdate ASC";
$result = mysql_query($query);

$tempArrayStop = array();
$tempArrayRun = array();

$response = array();
$response['data'] = array();
$response['sql'] = $query;

$totalPark = 0;
$totalRun = 0;
$park = false;
$run = false;
$firstPark = null;
$lastRow = null;
$lastRowPark = null;
$prevPoi = "";
$prevAddr = "";
$speedTotal = 0;
$speedCount = 0;
$first = true;
$prevHasGapDistance = false;
$trips = array();
$no = 0;
$prevTrip = null;
$key;

if ($result) {
    while ($row = mysql_fetch_assoc($result)) {
        $row['park'] = '';
        $row['tdate2'] = $row['tdate'];
        $row['odometer'] = 0;
        if ($first == false) {
            if ($prevHasGapDistance == true) {
                $trips[$no] = $row;
                $prevHasGapDistance = false;
            }

            $dist = distance($trips[$no]['lat'], $trips[$no]['lng'], $row['lat'], $row['lng']);
            if ($dist <= 5) {
                $row['odometer'] = $trips[$no]['odometer'] + $dist;
                //$last = end($trips);
                switch ((int) $trips[$no]['acc']) {
                    case 0:
                        switch ((int) $row['acc']) {
                            case 1:
                                //$prevTrip['tdate2'] = $row['tdate'];
                                //Hitung Berhenti
                                $trips[$no]['tdate2'] = $row['tdate'];
                                $diff = calcdiff($trips[$no]['tdate'], $trips[$no]['tdate2']);
                                $diffStr = "";

                                if (((int) $diff->h > 0) || ((int) $diff->i >= 10)) {
                                    if ($diff->h > 0) {
                                        $diffStr = $diff->h . " Hour ";
                                    }
                                    if ($diff->i > 0) {
                                        $diffStr .= $diff->i . " Minute ";
                                    }
                                }

                                $trips[$no]['park'] = $diffStr;

                                $no++;
                                $trips[$no] = $row;
                                break;
                            case 0:
                                //array_push($tempArrayStop,$row);
                                $trips[$no]['tdate2'] = $row['tdate'];
                                //reset($trips);
                                break;
                        }
                        break;
                    case 1:
                        $no++;
                        $trips[$no] = $row;
                        break;
                }
            } else {
                $prevHasGapDistance = true;
                //console.log('Distance More Than 2 Km');
            }
        } else {
            $trips[$no] = $row;
            $first = false;
        }
    }
    if (count($trips) > 0) {
        if ((int) $trips[$no]['acc'] == 0) {
            $diff = calcdiff($trips[$no]['tdate'], $trips[$no]['tdate2']);
            $diffStr = "";
            if (($diff->h > 0) || ($diff->i >= 10)) {
                if ($diff->h > 0) {
                    $diffStr = $diff->h . " Hour ";
                }
                if ($diff->i > 0) {
                    $diffStr .= $diff->i . " Minute";
                }
            }
//        if ($diffStr != "") {
//            $diffStr .= "</br> From:" . $trips[$no]['tdate'] . " - " . $trips[$no]['tdate2'];
//        }
            $trips[$no]['park'] = $diffStr; // . "</br> From:" . $trips[$no]['tdate'] . " - " . $trips[$no]['tdate2'];
        }
    }
}
$response['total'] = 0;
$response['total'] = 0;
for ($i = 0; $i < $no; $i++) {
    if ($trips[$i]['park'] != '') {
        $response['total'] ++;
        $response['data'][] = $trips[$i];
    }
}
mysql_close($connection);

$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Joko Pitoyo")
        ->setLastModifiedBy("Joko Pitoyo")
        ->setTitle("GPS Tracking Report")
        ->setSubject("GPS Tracking Trip Report")
        ->setDescription("GPS Tracking Report")
        ->setKeywords("GPS Tracking Report")
        ->setCategory("GPS Tracking Report");

$headerTitle = array('Nopol', 'Parkir', 'Start', 'Stop', 'Alamat', 'POI');
$columns = array('A', 'B', 'C', 'D', 'E', 'F');
$columnsWidth = array(20, 20, 20, 20, 20, 20);
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
$objPHPExcel->getActiveSheet()->setTitle('Laporan Parkir');


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
$objPHPExcel->getActiveSheet()->getStyle('A5:F5')->getFill()
        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FFCABDBD');
// Add some data
$objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', 'Laporan Parkir:' . $nopol)
        ->setCellValue('A2', 'Tanggal :' . $from . ' S/D ' . $to);


$startRow = 6;
foreach ($response['data'] as $key => $value) {
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $startRow, $nopol)
            ->setCellValue('B' . $startRow, $value['park'])
            ->setCellValue('C' . $startRow, $value['tdate'])
            ->setCellValue('D' . $startRow, $value['tdate2'])
            ->setCellValue('E' . $startRow, $value['address'])
            ->setCellValue('F' . $startRow, $value['poi']);
    $startRow++;
}

//return;
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="park_report_' . $from . '_' . $to . '_' . $vh_id . '.xls"');
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

echo json_encode($response);
?>