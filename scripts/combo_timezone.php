<?php
require_once 'connection.php';
$response=array();
$result = $mysqli->query("select * from timezone");
if ($result) {
    while($row=$result->fetch_assoc()){
        array_push($response, array("id"=>$row['timezone'],"value"=>$row["descr"]));
    }
    $result->free();
}
$mysqli->close();
echo json_encode($response);
?>