<?php
require_once 'connection.php';
$response=array();
$query=isset($_GET['filter']['value'])? " where kernet like '%".$mysqli->real_escape_string($_GET['filter']['value']) ."%'":'';
$result = $mysqli->query("select id,kernet from kernet ". $query ." limit 50");
if ($result) {
    while($row=$result->fetch_assoc()){
        array_push($response, array("id"=>$row['id'],"value"=>$row["kernet"]));
    }
    $result->free();
}
$mysqli->close();
echo json_encode($response);
?>