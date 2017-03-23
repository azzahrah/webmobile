<?php
include("../session.php");
include("../connection.php");
$vh_id = isset($_POST['vh_id']) ? mysql_real_escape_string($_POST['vh_id']) : 0;
$from = isset($_POST['from']) ? mysql_real_escape_string($_POST['from']) : 0;
$to = isset($_POST['to']) ? mysql_real_escape_string($_POST['to']) : 0;
$query = "select id,tdate,vh_id,alarm,speed,lat,lng,angle,poi,gf,address from track where tdate>='" . $from . "' AND tdate<='" . $to . "' AND vh_id='" . $vh_id . "' ORDER by tdate ASC";
$result = mysql_query($query);
$response = array();
$response['total']=0;
$response['data'] = array();
if ($result) {
    while ($row = mysql_fetch_assoc($result)) {
        $response['total'] ++;
        $response['data'][] = $row;
    }
}
mysql_close($connection);
echo json_encode($response);
?>