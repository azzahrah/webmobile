<?php

/** Error reporting */
//error_reporting(E_ALL);
ini_set('display_errors', FALSE);
ini_set('display_startup_errors', FALSE);
//date_default_timezone_set('Europe/London');


/** Include PHPExcel */
require_once '../Classes/PHPExcel.php';
require_once '../connection.php';

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

    return $a;
}

$from = isset($_GET['from']) ? mysql_real_escape_string($_GET['from']) : '2014-01-01';
$to = isset($_GET['to']) ? mysql_real_escape_string($_GET['to']) : '2014-03-01';
$vh_id = isset($_GET['vh_id']) ? mysql_real_escape_string($_GET['vh_id']) : '1';


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
//tdate,speed,acc,park,angle,alarm,lat,lng,poi,add
$headerTitle = array('Tanggal', 'Kecepatan', 'ACC', 'Park', 'Angle', 'Alarm', 'Latitude', 'Longitude', 'POI', 'Alamat');
$columns = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
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
$objPHPExcel->getActiveSheet()->setTitle('LAPORAN PERJALANAN');


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
$objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getFill()
        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FFCABDBD');
// Add some data
$objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', 'LAPORAN PERJALANAN')
        ->setCellValue('A2', 'Periode :' . $from . ' S/D ' . $to);

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
$first = false;
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
//Hitung Berhenti
                                $trips[$no]['tdate2'] = $row['tdate'];
                                $diff = calcdiff($trips[$no]['tdate'], $trips[$no]['tdate2']);
                                $diffStr = "";
                                if ($diff->h > 0) {
                                    $diffStr = $diff->h . " Jam ";

                                    if ($diff->i > 0) {
                                        $diffStr .= $diff->i . " Menit ";
                                    }
                                } else {
                                    if ($diff->i >= 5) {
                                        $diffStr .= $diff->i . " Menit ";
                                    }
                                }

                                if ($diffStr != "") {
                                    $diffStr .= "</br> From:" . $trips[$no]['tdate'] . " - " . $trips[$no]['tdate2'];
                                    $trips[$no]['park'] = $diffStr;
                                } else {
                                    $trips[$no]['park'] = '';
//                                    foreach ($tempArrayStop as $key => $value) {
//                                        $no++;
//                                        $trips[$no] = $value;
//                                    }
//                                    unset($tempArrayStop);
                                }

                                $no++;
                                $trips[$no] = $row;
                                break;
                            case 0:
                                //array_push($tempArrayStop, $row);
                                $trips[$no]['tdate2'] = $row['tdate'];
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
            }
        } else {
            $trips[$no] = $row;
            $first = false;
//            if((int)$row['acc']==0){
//                array_push($tempArrayStop,$row);
//            }
        }
    }

    if ((int) $trips[$no]['acc'] == 0) {
        $diff = calcdiff($trips[$no]['tdate'], $trips[$no]['tdate2']);
        $diffStr = "";
        if ($diff->h > 0) {
            $diffStr = $diff->h . " Jam ";

            if ($diff->i > 0) {
                $diffStr .= $diff->i . " Menit ";
            }
        } else {
            if ($diff->i >= 5) {
                $diffStr .= $diff->i . " Menit ";
            }
        }

        if ($diffStr != "") {
            $diffStr .= "</br> From:" . $trips[$no]['tdate'] . " - " . $trips[$no]['tdate2'];
            $trips[$no]['park'] = $diffStr;
        } else {
            $trips[$no]['park'] = '';
        }
    }
}

$startRow = 6;
for ($i = 0; $i < $no; $i++) {
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $startRow, $trips[$i]['tdate'])
            ->setCellValue('B' . $startRow, $trips[$i]['speed'])
            ->setCellValue('C' . $startRow, (int) $trips[$i]['acc'] == 1 ? 'ON' : 'OFF')
            ->setCellValue('D' . $startRow, isset($trips[$i]['park']) ? $trips[$i]['park'] : '')
            ->setCellValue('E' . $startRow, isset($trips[$i]['angle']) ? get_direction((int) $trips[$i]['angle']) : 0)
            ->setCellValue('F' . $startRow, get_alarm(isset($trips[$i]['alarm']) ? get_alarm((int) $trips[$i]['alarm']) : 'N/A'))
            ->setCellValue('G' . $startRow, $trips[$i]['lat'])
            ->setCellValue('H' . $startRow, $trips[$i]['lng'])
            ->setCellValue('I' . $startRow, $trips[$i]['poi'])
            ->setCellValue('J' . $startRow, $trips[$i]['address']);
    $startRow++;
}

mysql_close($connection);
//return;
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="trip_report_' . $from . '_' . $to . '_' . $vh_id . '.xls"');
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

