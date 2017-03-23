<?php
require_once 'session.php';
require_once 'connection.php';
$user_level = isset($_SESSION['user_level']) ? $mysqli->real_escape_string($_SESSION['user_level']) : '';
$id = isset($_POST['id']) ? intval($_POST['id']) : 0; //755
$mode = isset($_POST['mode']) ? $mysqli->real_escape_string($_POST['mode']) : ''; //edit
$nopol = isset($_POST['nopol']) ? $mysqli->real_escape_string($_POST['nopol']) : '';
$imei = isset($_POST['imei']) ? $mysqli->real_escape_string($_POST['imei']) : '';
$phone = isset($_POST['phone']) ? $mysqli->real_escape_string($_POST['phone']) : '';
$timezone = isset($_POST['timezone']) ? intval($_POST['timezone']) : 7;
if ($timezone == '' || $timezone == 0) {
    $timezone = 7;
}
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
if ($user_id == 0) {
    $user_id = 1;
}
$gps_brand = isset($_POST['gps_brand']) ? $mysqli->real_escape_string($_POST['gps_brand']) : 'GT06N';
$vh_group = isset($_POST['vh_group']) ? intval($_POST['vh_group']) : 0;
$vh_brand = isset($_POST['vh_brand']) ? $mysqli->real_escape_string($_POST['vh_brand']) : 'N/A';
$descr = isset($_POST['descr']) ? $mysqli->real_escape_string($_POST['descr']) : '';
$icon_map = isset($_POST['icon_map']) ? $mysqli->real_escape_string($_POST['icon_map']) : 'panah';
$icon = isset($_POST['icon']) ? $mysqli->real_escape_string($_POST['icon']) : '';
$drv_name = isset($_POST['drv_name']) ? $mysqli->real_escape_string($_POST['drv_name']) : '-';
$drv_phone = isset($_POST['drv_phone']) ? $mysqli->real_escape_string($_POST['drv_phone']) : '-';
$install_date = isset($_POST['install_date']) ? $mysqli->real_escape_string($_POST['install_date']) : '2000-10-10';
$guaranty = isset($_POST['guaranty_date']) ? $mysqli->real_escape_string($_POST['guaranty_date']) : '2000-10-10';
$expedisi = isset($_POST['expedisi']) ? $mysqli->real_escape_string($_POST['expedisi']) : 'N/A';
$max_park = isset($_POST['max_park']) ? intval($_POST['max_park']) : 0;
$center_number = isset($_POST['center_number']) ? $mysqli->real_escape_string($_POST['center_number']) : '';
$response = array();
$response['code'] = 'ERROR';
$response['msg'] = '';
$response['mode'] = $mode;

$sql = "";
switch ($mode) {
    case "add":
        $response['msg'] = 'Mode Add ';
        if (((int) $user_id <= 0) || (trim($imei) == '') || (trim($nopol) == '')) {
            $response['msg'] = 'Nama User,Imei,Nopol Tidak Boleh Kosong';
        } else {
            $exist = false;
            $result = $mysqli->query("select * from view_last_track_byuser where imei='" . $imei . "'");
            if ($result) {
                if ($result->num_rows <= 0) {
                    $sql = "INSERT INTO vehicle (`nopol`,`user_id`,`icon_map`,`icon`,`imei`,`phone`,`timezone`,`vh_brand`,`gps_brand`,`guaranty_date`,`install_date`,`drv_name`,`drv_phone`,`descr`,`center_number`,`max_park`) ";
                    $sql .=" VALUES(";
                    $sql .="'" . $nopol . "','" . $user_id . "',";
                    $sql .="'" . $icon_map . "','" . $icon . "','" . $imei . "',";
                    $sql .="'" . $phone . "','" . $timezone . "','" . $vh_brand . "',";
                    $sql .="'" . $gps_brand . "','" . $guaranty . "',";
                    $sql .="'" . $install_date . "',";
                    $sql .="'" . $drv_name . "','" . $drv_phone . "',";
                    $sql .="'" . $descr . "',";
                    $sql .="'" . $center_number . "',";
                    $sql .="'" . $max_park . "')";
                    if ($mysqli->query($sql)) {
                        $response['code'] = 'SUCCESS';
                        $response['msg'] = 'Add Data GPS Sukses';
                    } else {
                        $response['msg'] = "CALL add_gps failed: (" . $mysqli->errno . ") " . $mysqli->error;
                    }
                } else {
                    $exist = true;
                    $response['msg'] = 'IMEI sudah digunakan ' . mysql_result($result, 0, "real_name") . ", Nopol:" . mysql_result($result, 0, "nopol");
                    $result->free();
                }
            } else {
                $response['msg'] = 'Check Imei Error....';
            }
            $mysqli->close();
        }
        break;
    case 'edit':
        $sql = "UPDATE vehicle SET nopol='" . $nopol . "',user_id='" . $user_id . "',";
        $sql .="icon_map='" . $icon_map . "',icon='" . $icon . "',imei='" . $imei . "',";
        $sql .="phone='" . $phone . "',timezone='" . $timezone . "',vh_brand='" . $vh_brand . "',";
        $sql .="gps_brand='" . $gps_brand . "',";
        $sql .="install_date='" . $install_date . "',";
        $sql .="drv_name='" . $drv_name . "',drv_phone='" . $drv_phone . "',";
        $sql .="descr='" . $descr . "',center_number='" . $center_number . "' WHERE id='" . $id . "'";
        $response['data'] = $sql;
        if ($mysqli->query($sql)) {
            $response['code'] = 'SUCCESS';
            $response['msg'] = 'Edit Data GPS Sukses';
        } else {
            $response['msg'] = $mysqli->error;
        }
        $mysqli->close();
        break;
    case 'delete':
        if (($user_level == 'admin') || ($user_level == 'reseller')) {
            $ids = isset($_POST['ids']) ? $mysqli->real_escape_string($_POST['ids']) : '';
            $arr = explode(",", $ids);
            $idss = "";
            for ($i = 0; $i < count($arr); $i++) {
                if ($idss != "") {
                    $idss .= " or id='" . $arr[$i] . "'";
                } else {
                    $idss = "id='" . $arr[$i] . "'";
                }
            }
            $sql = "DELETE FROM vehicle where " . $idss;
            if ($mysqli->query($sql)) {
                $response['code'] = 'SUCCESS';
                $response['msg'] = 'Delete Data GPS Sukses';
            } else {
                $response['msg'] = "CALL failed: (" . $mysqli->errno . ") " . $mysqli->error;
            }
            $mysqli->close();
        } else {
            $response['msg'] = 'Delete Error:does not have privileges to delete data';
        }
        break;
}
print json_encode($response);
?>