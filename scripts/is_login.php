<?php
session_start();
$response = array();
$response['login'] = false;
if (isset($_SESSION['id'])) {
    $response['login']=true;
    $response['id'] = $_SESSION['user_id'];
    $response['user_id'] = $_SESSION['user_id'];
    $response['user_login'] = $_SESSION['user_login'];
    $response['user_name'] = $_SESSION['user_name'];
    $response['user_level'] = $_SESSION['user_level'];
}
echo json_encode($response);
?>