<?php

include('session.php');
include("connection.php");
$vh_id = isset($_POST['vh_id']) ? mysql_real_escape_string($_POST['vh_id']) : 0;
$alarm = isset($_POST['alarm']) ? mysql_real_escape_string($_POST['alarm']) : 0;
$from = isset($_POST['from']) ? mysql_real_escape_string($_POST['from']) : 0;
$to = isset($_POST['to']) ? mysql_real_escape_string($_POST['to']) : 0;

$where = " where alarm='".$alarm."' AND vh_id='" . $vh_id . "' AND speed>=0 AND tdate>='" . $from . "' AND tdate<='" . $to . "' order by tdate ASC";
$result = mysql_query("SELECT * from track " . $where);
$root = array();
$root["total"] = 0;
$root["msg"] = "";
$root["data"]=array();
if ($result) {
    $rows = array();
    while ($r = mysql_fetch_assoc($result)) {
        $root["total"]++;
        $root["data"][]=$r;
    }
    
} else {
    $root["msg"] = "Error:".mysql_error();
}

print json_encode($root);
mysql_close($connection);
?>