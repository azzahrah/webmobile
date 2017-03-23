<?php

require_once 'session.php';
require_once 'connection.php';
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
$user_level = $_SESSION['user_level'];
$user_session_id = $_SESSION['user_id'];

$response['total'] = 0;
$response['rows'] = array();

$result = $mysqli->query("select * from view_member_device where id_member='" . $user_id . "' order by nopol ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $tracks['total'] ++;
        $tracks['rows'][] = $row;
    }
    $result->free();
}
$mysqli->close();
echo json_encode($tracks);
?>
