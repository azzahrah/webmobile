<?php
require_once 'connection.php';
$response=array();
$result = $mysqli->query("select * from view_route");
if ($result) {
    while($row=$result->fetch_assoc()){
        array_push($response, $row);
    }
    $result->free();
}
$mysqli->close();
echo json_encode($response);
?>