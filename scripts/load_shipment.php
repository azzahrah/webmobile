<?php

require_once 'connection.php';
$response = array();
$query = isset($_GET['filter']['value']) ? " where nopol like '%" . $mysqli->real_escape_string($_GET['filter']['value']) . "%'" : '';

//Check filter by route
//if (isset($_POST['id_asal']) && isset($_POST['id_tujuan'])) {
//    $query = " where id_asal='" . intval($_POST['id_asal']). "' and id_tujuan='" . intval($_POST['id_tujuan']). "' ";
//}

$result = $mysqli->query("select * from view_shipment " . $query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        array_push($response, $row);
    }
    $result->free();
}
$mysqli->close();
echo json_encode($response);
?>