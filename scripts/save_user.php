<?php
require_once 'session.php';
require_once 'connection.php';
$user_level = isset($_SESSION['user_level']) ? $mysqli->real_escape_string($_SESSION['user_level']) : '';
$add_user = isset($_SESSION['add_user']) ? $mysqli->real_escape_string($_SESSION['add_user']) : '';
$edit_user = isset($_SESSION['edit_user']) ? $mysqli->real_escape_string($_SESSION['edit_user']) : '';
$delete_user = isset($_SESSION['delete_user']) ? $mysqli->real_escape_string($_SESSION['delete_user']) : '';

$user_id = isset($_SESSION['user_id']) ? $mysqli->real_escape_string($_SESSION['user_id']) : '';
$id = isset($_POST['id']) ? $mysqli->real_escape_string($_POST['id']) : '';
$mode = isset($_POST['mode']) ? $mysqli->real_escape_string($_POST['mode']) : '';
$login = isset($_POST['login']) ? $mysqli->real_escape_string($_POST['login']) : '';
$real_name = isset($_POST['real_name']) ? $mysqli->real_escape_string($_POST['real_name']) : '';
$address = isset($_POST['address']) ? $mysqli->real_escape_string($_POST['address']) : '-';
$phone = isset($_POST['phone']) ? $mysqli->real_escape_string($_POST['phone']) : '-';
$email = isset($_POST['email']) ? $mysqli->real_escape_string($_POST['email']) : '-';
$password = isset($_POST['passwordx']) ? $mysqli->real_escape_string($_POST['passwordx']) : '';
$description = isset($_POST['description']) ? $mysqli->real_escape_string($_POST['description']) : '';
$expired_date = isset($_POST['expired_date']) ? $mysqli->real_escape_string($_POST['expired_date']) : '2099-10-10';
$state = isset($_POST['state']) ? intval($_POST['state']):0;
$level_id = isset($_POST['level_id']) ? $mysqli->real_escape_string($_POST['level_id']) : '';
$reseller_id = isset($_POST['reseller_id']) ? $mysqli->real_escape_string($_POST['reseller_id']) : '0';
$user_access = isset($_POST['user_access']) ? $mysqli->real_escape_string($_POST['user_access']) : '';

$response=array();
$response['code'] = 'ERROR';
$response['msg'] = 'Unauthorized user...';

//$mode='edit';
switch ($mode) {
    case 'add':
        $sql = "";
        if ($user_level == 'admin') {
            $sql = "INSERT INTO user (`login`,`real_name`,`address`,`phone`,`email`,`reseller_id`,`description`,`level_id`,`expired_date`,`state`,`password`,`user_access`) ";
            $sql .=" VALUES('{$login}','{$real_name}','{$address}','{$phone}','{$email}','{$reseller_id}','{$description}','{$level_id}','{$expired_date}','{$state}',PASSWORD('{$password}'),'{$user_access}')";            
        } else if ($user_level == 'reseller') {
            $sql = "INSERT INTO user (`login`,`real_name`,`address`,`phone`,`email`,`reseller_id`,`description`,`level_id`,`expired_date`,`state`,`password`) ";
            $sql .=" VALUES('{$login}','{$real_name}','{$address}','{$phone}','{$email}','{$user_id}','{$description}','{$level_id}','{$expired_date}','{$state}',PASSWORD('{$password}'))";            
        }
        $result = $mysqli->query($sql);
        if ($result) {
            $response['code'] = 'SUCCESS';
            $response['msg'] = 'Sukses Tambah Data User';
        } else {
            $response['code'] = 'ERROR';
            $response['msg'] = "Tambah Data User Error :" . $mysqli->error;
        }
        break;
    case 'edit':
        $sql = "";
        $sqlPass = "";
        if ($password != '') {
            $sqlPass = ",password=PASSWORD('" . $password . "') ";
        }
        switch ($user_level) {
            case 'admin':
                $sql = "UPDATE user SET state='$state', reseller_id='{$reseller_id}',login='{$login}',real_name='{$real_name}',user_access='{$user_access}',address='{$address}',phone='{$phone}',email='{$email}',description='{$description}',level_id='{$level_id}',expired_date='{$expired_date}' " . $sqlPass . " WHERE id='{$id}' ";
                break;
            case 'reseller':
                $sql = "UPDATE user SET state='$state',reseller_id='{$reseller_id}',login='{$login}',real_name='{$real_name}',user_access='{$user_access}',address='{$address}',phone='{$phone}',email='{$email}',description='{$description}',expired_date='{$expired_date}' " . $sqlPass . "  WHERE id='{$id}' ";
                break;
            case 'user':
                $sql = "UPDATE user SET real_name='{$real_name}',address='{$address}',phone='{$phone}',email='{$email}',description='{$description}' " . $sqlPass . "  WHERE id='{$id}' ";
                break;
        }

        $result = $mysqli->query($sql);
        $response['sql']=$sql;       
        if ($result) {
            $response['code'] = 'SUCCESS';
            $response['msg'] = 'Sukses Edit Data User';
        } else {
            $response['code'] = 'ERROR';
            $response['msg'] = "Edit Data User Error :" . $mysqli->error;
        }
        break;
    case 'delete':
        switch ($user_level) {
            case 'admin':
                $sql = "delete from user where id='{$id}' and id<>'{$_SESSION['user_id']}' LIMIT 1";
                break;
            case 'reseller':
                $sql = "delete from user where id='{$id}' and reseller_id='{$_SESSION['user_id']}' and id<>'{$_SESSION['user_id']}' LIMIT 1";
                break;
        }
        $result = $mysqli->query($sql);
        if ($result) {
            $response['code'] = 'SUCCESS';
            $response['msg'] = 'Sukses Hapus Data User';
        } else {
            $response['code'] = 'ERROR';
            $response['msg'] = "Hapus Data User Error :" . $mysqli->error;
        }
        break;
}
print json_encode($response);
?>