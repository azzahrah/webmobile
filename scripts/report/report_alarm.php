<?php
include('../session.php');
include("../connection.php");
$from = isset($_POST['from_date']) ? $mysqli->real_escape_string($_POST['from_date']) : '2016-06-07 09:14:00';
$to = isset($_POST['to_date']) ? $mysqli->real_escape_string($_POST['to_date']) : '2016-06-09 09:14:00';
$vh_id = isset($_POST['vh_id']) ? $mysqli->real_escape_string($_POST['vh_id']) : '0';
$vh_id = 705;

$root = array();
$root["total"] = 0;
$root["msg"] = "";
$root["data"] = array();

$sql = "SELECT * from track_alarm where tdate>='" . $from . "' AND tdate<='" . $to . "' order by tdate ASC";
$roow["sql"] = $sql;
$result = $mysqli->query($sql);
if (!$result) {
    $root["msg"] = "Error:" . $mysqli->error();
    $mysqli->close();
    print json_encode($root);
    exit;
}
while ($row = $result->fetch_assoc()) {
    $root["total"] ++;
    $root["data"][] = $row;
}
$result->free();
$mysqli->close();
print json_encode($root);
?>