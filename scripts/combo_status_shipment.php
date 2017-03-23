<?php
require_once 'connection.php';
$response=array();
$query=isset($_GET['filter']['value'])? " where status like '%".$mysqli->real_escape_string($_GET['filter']['value']) ."%'":'';
$result = $mysqli->query("select * from shipment_status ". $query ." limit 50");
if ($result) {
    while($row=$result->fetch_assoc()){
        array_push($response, array("id"=>$row['id'],"value"=>$row["status"]));
    }
    $result->free();
}
$mysqli->close();
echo json_encode($response);
?>