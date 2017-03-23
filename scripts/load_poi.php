<?php
require_once 'connection.php';
$response=array();
$query=isset($_GET['filter']['value'])? " where poi like '%".$mysqli->real_escape_string($_GET['filter']['value']) ."%'":'';
$result = $mysqli->query("select id,poi,y(ogc_geom) as lat,x(ogc_geom) as lng from poi ". $query ." ORDER BY poi limit 50");
if ($result) {
    while($row=$result->fetch_assoc()){
        array_push($response,$row);// array("id"=>$row['id'],"value"=>$row["poi"]));
    }
    $result->free();
}
$mysqli->close();
echo json_encode($response);
?>