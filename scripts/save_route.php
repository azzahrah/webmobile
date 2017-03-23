<?php
require_once 'connection.php';
$valid = true;
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$mode = isset($_POST['mode']) ? $mysqli->real_escape_string($_POST['mode']) : '';
$route = isset($_POST['route']) ? $mysqli->real_escape_string($_POST['route']) : '';
$id_asal = isset($_POST['id_asal']) ? intval($_POST['id_asal']) : 0;
$id_tujuan = isset($_POST['id_tujuan']) ? intval($_POST['id_tujuan']) : 0;
$durasi = isset($_POST['durasi']) ? intval($_POST['durasi']) : 0;

$msgError = "";
$mode='add';
if ($mode == 'edit' || $mode == 'add') {
    if ($route == '') {
        $valid = false;
        $msgError .="Nama Rute Kosong\r\n";
    }    
}
$response = array();
$response['code'] = 'ERROR';
$response['msg'] = $msgError;
$response['data'] = $_POST;
$response['sql'] = '';
$response['mode'] = $mode;
if ($valid == true) {
    switch ($mode) {
        case 'add':
            $sql = "INSERT INTO `route` (`user_id`,`route`,`id_asal`,`id_tujuan`) VALUES ";
            $sql .="('" . $user_id . "','" . $route . "','" . $id_asal . "','" . $id_tujuan . "')";
            $response['sql'] = $sql;
            if ($mysqli->query($sql)) {
                $response['code'] = "SUCCESS";
                $response['msg'] = "MENAMBAH POI SUKSES";
            } else {
                $response['msg'] = "MENAMBAH POI ERROR:" . $route ." ". mysql_error();
            }
            $response['sql'] = $sql;
            break;
        case 'edit':
            $sql = "";
            $sql = "UPDATE `poi` SET user_id='" . $user_id . "',vh_id='" . $vh_id . "',poi='" . $poi_name . "',descr='" . $descr . "',icon='" . $poi_icon . "',ogc_geom=GEOMFROMTEXT('POINT({$lng} {$lat})') WHERE id='" . $id . "'";
            $response['sql'] = $sql;
            if ($mysqli->query($sql)) {
                $response['code'] = "SUCCESS";
                $response['msg'] = "UPDATE POI SUKSES";
            } else {
                $response['msg'] = "UPDATE POI ERROR:" . $mysqli->error;
            }
            break;
        case 'delete':
            $arr = explode(",", $ids);
            $idss = "";
            for ($i = 0; $i < count($arr); $i++) {
                if ($idss != "") {
                    $idss .= " or id='" . $arr[$i] . "'";
                } else {
                    $idss = "id='" . $arr[$i] . "'";
                }
            }
            $sql = "delete from `poi` where " . $idss . " and user_id='" . $user_id . "'";
            $response['sql'] = $sql;
            if ($mysqli->query($sql)) {
                $response['code'] = "SUCCESS";
                $response['msg'] = "HAPUS POI SUKSES";
            } else {
                $response['msg'] = "HAPUS POI ERROR:" . $mysqli->error;
            }
            break;
    }
    $mysqli->close();
}
print json_encode($response);
?>