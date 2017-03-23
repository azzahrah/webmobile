<?php

require_once 'connection.php';
$response = array();
$query = isset($_GET['filter']['value']) ? " where real_name like '%" . $mysqli->real_escape_string($_GET['filter']['value']) . "%'" : '';
$result = $mysqli->query("select * from user " . $query . " limit 50");
//function get_root()

if ($result) {
    array_push($response, array("id" => "group_0", "Ungroup"));
    while ($row = $result->fetch_assoc()) {
        if (($row['level'] == 'admin') || ($row['level'] == 'reseller')) {
            array_push($response, array("id" => "group_" . $row['id'], "value" => $row["real_name"]));
        }
    }
    $result->data_seek(0);
    while ($row = $result->fetch_assoc()) {
        if (($row['level'] == 'admin') || ($row['level'] == 'reseller')) {
            continue;
        }
        array_push($response, array("id" => $row['id'], "value" => $row["real_name"]));
    }
    $result->free();
}
$mysqli->close();
echo json_encode($response);
?>