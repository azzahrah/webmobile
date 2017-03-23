<?php
include('../session.php');
include("../connection.php");
$vh_id = isset($_POST['vh_id']) ? mysql_real_escape_string($_POST['vh_id']) : 0;
$from = isset($_POST['from']) ? mysql_real_escape_string($_POST['from']) : 0;
$to = isset($_POST['to']) ? mysql_real_escape_string($_POST['to']) : 0;

$distance_per_liter = isset($_POST['distance_per_liter']) ? mysql_real_escape_string($_POST['distance_per_liter']) : 10;
$price_per_liter = isset($_POST['price_per_liter']) ? mysql_real_escape_string($_POST['price_per_liter']) : 6500;

$where = " where vh_id='" . $vh_id . "' AND speed>=2 AND tdate>='" . $from . "' AND tdate<='" . $to . "' order by tdate ASC";
$result = mysql_query("SELECT * from track " . $where);
$response = array();
$response['total']=0;
$response['data']=array();
while ($r = mysql_fetch_assoc($result)) {
    $response['total']++;
    $response['data'][] = $r;
}
print json_encode($response);
mysql_close($connection);
?>