<?php

include('../session.php');
include("../connection.php");
$from = isset($_POST['from_date']) ? $mysqli->real_escape_string($_POST['from_date']) : '';
$to = isset($_POST['to_date']) ? $mysqli->real_escape_string($_POST['to_date']) : '';
$vh_id = isset($_POST['vh_id']) ? $mysqli->real_escape_string($_POST['vh_id']) : '';

$query = "select id,vh_id, tdate,alarm,gf,gf_id,lat,lng from track where  tdate>='" . $from . "' AND tdate<='" . $to . "' AND vh_id='" . $vh_id . "' ORDER BY tdate ASC";
$result = $mysqli->query($query);
if (!$result) {
    $root["msg"] = "Error:" . $mysqli->error();
    $mysqli->close();
    print json_encode($root);
    exit;
}

$response = array();
$response['total'] = 0;
$response['sql'] = $query;
$response['ext'] = 'test data';
$response['data'] = array();

$data = array();
$last_gf_id = 0;
$total = 0;
while ($row = $result->fetch_assoc()) {
    $gfid = (int) $row['gf_id'];
    if (($gfid > 0) && ($last_gf_id == 0)) {
        $total++;
        $row['jumlah'] = $total;
        $last_gf_id = (int) $row['gf_id'];
        $response['total'] ++;
        $row['status'] = "ENTER";
        $response['data'][] = $row;
    } else if (($gfid > 0) && ($last_gf_id > 0) && ($gfid != $last_gf_id)) {
        $total++;
        $row['jumlah'] = $total;
        $last_gf_id = (int) $row['gf_id'];
        $response['total'] ++;
        $row['status'] = "ENTER";
        $response['data'][] = $row;
    }
    if ($gfid == 0) {
        $last_gf_id = 0;
    }
}

$result->free();
$mysqli->close();
echo json_encode($response);
?>
