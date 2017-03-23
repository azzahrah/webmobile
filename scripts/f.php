<?php
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
?>