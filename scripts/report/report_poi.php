<?php

include('../session.php');
include("../connection.php");
$from = isset($_POST['from_date']) ? $mysqli->real_escape_string($_POST['from_date']) : '';
$to = isset($_POST['to_date']) ? $mysqli->real_escape_string($_POST['to_date']) : '';
$vh_id = isset($_POST['vh_id']) ? $mysqli->real_escape_string($_POST['vh_id']) : '';

$response = array();
$response['total'] = 0;
$response['msg'] = "";
$response['data'] = array();

$query = "select id,tdate,poi_id,poi,lat,lng,address from track where  tdate>='" . $from . "' AND tdate<='" . $to . "' AND vh_id='" . $vh_id . "' ORDER BY tdate ASC";
$result = $mysqli->query($query);
if (!$result) {
    $root["msg"] = "Error:" . $mysqli->error();
    $mysqli->close();
    print json_encode($root);
    exit;
}

$data = array();
$lastData;
$last_poi = 0;
while ($row = $result->fetch_assoc()) {
    $poi_id = (int) $row['poi_id'];
    if (($poi_id > 0) && ($last_poi == 0)) { //Enter Poi
        $lastData = array();
        $lastData['nopol'] = $row['nopol'];
        $lastData['enter_date'] = $row['tdate'];
        $lastData['exit_date'] = $row['tdate'];
        $lastData['poi'] = $row['poi'];
        $lastData['poi_id'] = $row['poi_id'];
    } else if (($poi_id == 0) && ($last_poi > 0)) {
        $response['total'] ++;
        $last_poi = 0;
        $newData = array();
        $newData['nopol'] = $lastData['nopol'];
        $newData['enter_date'] = $lastData['enter_date'];
        $newData['poi'] = $lastData['poi'];
        $newData['poi_id'] = $lastData['poi_id'];
        $newData['exit_date'] = $row['tdate'];
        $response['data'][] = $lastData;
    }
}
$result->free();
$mysqli->close();
echo json_encode($response);
?>
