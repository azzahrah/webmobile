<?php
require_once 'session.php';
require_once 'connection.php';
$response = array();
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$user_level = isset($_SESSION['user_level']) ? $mysqli->real_escape_string($_SESSION['user_level']) : "";
$where = "";
switch ($user_level) {
    case 'admin':
        $where = isset($_GET['filter']['value']) ? " where real_name like '%" . $mysqli->real_escape_string($_GET['filter']['value']) . "%'" : "";
        break;
    case 'reseller':
        $where = isset($_GET['filter']['value']) ? " where reseller_id='$user_id' and id='$user_id' or real_name like '%" . $mysqli->real_escape_string($_GET['filter']['value']) . "%'" : "where reseller_id='$user_id' or id='$user_id'";
        break;
    default:
        $where = isset($_GET['filter']['value']) ? " where id='$user_id' and real_name like '%" . $mysqli->real_escape_string($_GET['filter']['value']) . "%'" : "WHERE id='$user_id'";
        break;
}

$result = $mysqli->query("select * from view_user " . $where . " ORDER BY real_name");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $row['password']='';
        array_push($response, $row);
    }
    $result->free();
}
$mysqli->close();
echo json_encode($response);
?>