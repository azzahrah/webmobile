<?php
require_once 'connection.php';
$valid = true;
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$mode = isset($_POST['mode']) ? $mysqli->real_escape_string($_POST['mode']) : '';
$poi_name = isset($_POST['poi']) ? $mysqli->real_escape_string($_POST['poi']) : '';
$poi_icon = isset($_POST['icon']) ? $mysqli->real_escape_string($_POST['icon']) : '';
$descr = isset($_POST['descr']) ? $mysqli->real_escape_string($_POST['descr']) : '';
$lat = isset($_POST['lat']) ? $mysqli->real_escape_string($_POST['lat']) : '';
$lng = isset($_POST['lng']) ? $mysqli->real_escape_string($_POST['lng']) : '';
$vh_id = isset($_POST['vh_id']) ? intval($_POST['vh_id']) : 0;

$msgError = "";
$mode='add';
if ($mode == 'edit' || $mode == 'add') {
    if ($poi_name == '') {
        $valid = false;
        $msgError .="Nama POI Kosong\r\n";
    }
    if ($lat == '' || $lat == '0') {
        $valid = false;
        $msgError .="Latitude Kosong\r\n";
    }
    if ($lng == '' || $lng == '0') {
        $valid = false;
        $msgError .="Longitude Kosong\r\n";
    }
    if ($mode == '') {
        $msgError .="Unknow Mode Edit/Add/Delete Data...";
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
            $sql = "INSERT INTO `poi` (`user_id`,`vh_id`,`poi`,`descr`,`icon`,`ogc_geom`) ";
            $sql .=" VALUES('" . $user_id . "','" . $vh_id . "','" . $poi_name . "','" . $descr . "','" . $poi_icon . "',GEOMFROMTEXT('POINT(" . $lng . " " . $lat . ")'))";
            $response['sql'] = $sql;
            if ($mysqli->query($sql)) {
                $response['code'] = "SUCCESS";
                $response['msg'] = "MENAMBAH POI SUKSES";
            } else {
                $response['msg'] = "MENAMBAH POI ERROR:" . $poi_name; // mysql_error();
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