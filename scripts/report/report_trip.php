<?php

require_once '../connection.php';
$from = isset($_POST['from_date']) ? $mysqli->real_escape_string($_POST['from_date']) : '2016-10-10 16:55:00';
$to = isset($_POST['to_date']) ? $mysqli->real_escape_string($_POST['to_date']) : '2016-10-11 16:55:00';
$vh_id = isset($_POST['vh_id']) ? $mysqli->real_escape_string($_POST['vh_id']) : '0';
//$vh_id = 828;

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
}

function distance($lat1, $lon1, $lat2, $lon2) {
    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    return ($miles * 1000 * 1.609344); //meter
}

$query = "select * from view_trip where tdate>='" . $from . "' AND tdate<='" . $to . "' AND vh_id='" . $vh_id . "' ORDER by tdate ASC";
//echo $query;
try {
    $result = $mysqli->query($query);
} catch (Exception $ex) {
    
}


$tempArrayStop = array();
$tempArrayRun = array();

$response = array();
$response['total'] = 0;
$response['dist'] = 0;
$response['msg'] = "";
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
$prev = null;
if (!$result) {
    $response['msg'] = $mysqli->error;
    $mysqli->close();
    echo json_encode($response);
    exit;
}
if ($result->num_rows <= 0) {
    $response['msg'] = "Data Kosong";
    $result->free();
    $mysqli->close();
    echo json_encode($response);
    exit;
}
$trips[$no] = $result->fetch_assoc();
$trips[$no]['tdate2']=$trips[$no]['tdate']; 
$trips[$no]['odometer'] = 0;
while ($row = $result->fetch_assoc()) {
    $row['park'] = '';
    $row['tdate2'] = $row['tdate'];
    // $row['odometer'] = 0;
    if ($prevHasGapDistance == true) {
        $trips[$no] = $row;
        $prevHasGapDistance = false;
    }
    $prev = $trips[$no];
    $dist = distance($prev['lat'], $prev['lng'], $row['lat'], $row['lng']);
    if ($dist > 3) {
        $prevHasGapDistance = true;
        continue;
    }
    // $response['dist']+=(int)$dist;
    // $row['odometer'] = $prev['odometer'] + $dist;
    switch ((int) $prev['acc']) {
        case 0:
            switch ((int) $row['acc']) {
                case 1:
                    //Hitung Berhenti
                    $prev['tdate2'] = $row['tdate'];
                    $diff = calcdiff($prev['tdate'], $prev['tdate2']);
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
                        $diffStr .= "</br> From:" . $prev['tdate'] . " - " . $prev['tdate2'];
                        $trips[$no]['park'] = $diffStr;
                    } else {
                        $trips[$no]['park'] = '';
                    }
                    $trips[++$no] = $row;
                    break;
                case 0:
                    $prev['tdate2'] = $row['tdate'];
                    break;
            }
            break;
        case 1:
            $trips[++$no] = $row;
            break;
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
$result->free();

for ($i = 0; $i < $no; $i++) {
    $response['total'] ++;
    $response['data'][] = $trips[$i];
}
$mysqli->close();
echo json_encode($response);
?>
