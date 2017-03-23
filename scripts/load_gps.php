<?php
require_once 'session.php';
require_once 'connection.php';
$user_id=isset($_POST['user_id'])?intval($_POST['user_id']):0;
if($user_id==0){
    $user_id=isset($_SESSION['user_id'])?intval($_SESSION['user_id']):0;
}
$response=array();
$response['total']=0;
$response['data']=array();
$response['group']=array();

$result = $mysqli->query("select * from view_vehicle_simple where user_id='". $user_id ."'");
if ($result) {
    while($row=$result->fetch_assoc()){
        $response['total']++;
        array_push($response['data'], $row);
    }
    $result->free();
}

$result = $mysqli->query("select * from vehicle_group where user_id='". $user_id ."'");
if ($result) {
    while($row=$result->fetch_assoc()){
        array_push($response['group'], $row);
    }
    $result->free();
}

$mysqli->close();
echo json_encode($response);
?>