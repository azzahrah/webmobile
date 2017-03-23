<?php

require_once 'session.php';
require_once 'connection.php';
$user_level_session = isset($_SESSION['user_level']) ? $mysqli->real_escape_string($_SESSION['user_level']) : '';
$user_id_session = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;


//$query ="";
$query = isset($_POST['user_id']) ? " where user_id='" . $user_id . "'" : '';
//switch ($user_level_session){
//    case "admin":
//        $query = isset($_POST['user_id']) ? " where user_id='" . $user_id . "'" : '';
//        if($user_id ==0){
//            $query = "";        
//        }
//        break;
//    case "reseller":
//        $query = isset($_POST['user_id']) ? " where user_id='" . $user_id . "'" : " where user_id='" . $user_id_session . "' and reseller_id='". intval($reseller_id) ."'" ;
//        break;
//    default :
//        
//        
//        //$query ="user_id='". intval($user_id) ."'" ;        
//        break;
//}
$result = $mysqli->query("select * from view_vehicle_simple " . $query . " ORDER by nopol");
$response = array();
if ($result) {
    while ($row = $result->fetch_assoc()) {
        array_push($response, $row);
    }
    $result->free();
}
$mysqli->close();
echo json_encode($response);
?>