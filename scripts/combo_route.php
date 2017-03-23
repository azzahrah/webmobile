<?php
require_once 'connection.php';
$response=array();
$query=isset($_GET['filter']['value'])? " where route like '%".$mysqli->real_escape_string($_GET['filter']['value']) ."%'":'';
$result = $mysqli->query("select * from view_route ". $query ." ORDER BY route LIMIT 100");
if ($result) {
    while($row=$result->fetch_assoc()){
        array_push($response, array("id"=>$row['id'],"value"=>$row["route"]));
    }
    $result->free();
}
$mysqli->close();
echo json_encode($response);
?>