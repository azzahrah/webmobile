<?php
require_once 'connection.php';
$user_level = isset($_SESSION['user_level']) ? $mysqli->real_escape_string($_SESSION['user_level']) : '';
$user_id = isset($_POST['user_id']) ? $mysqli->real_escape_string($_POST['user_id']) : 0;
if ($user_id == 0) {
    $user_id = isset($_SESSION['user_id']) ? $mysqli->real_escape_string($_SESSION['user_id']) : 0;
}
$id = isset($_POST['id']) ? $mysqli->real_escape_string($_POST['id']) : '';
$mode = isset($_POST['mode']) ? $mysqli->real_escape_string($_POST['mode']) : '';
$nama_driver = isset($_POST['driver']) ? $mysqli->real_escape_string($_POST['driver']) : '';
$kota = isset($_POST['kota']) ? $mysqli->real_escape_string($_POST['kota']) : '-';
$alamat = isset($_POST['alamat']) ? $mysqli->real_escape_string($_POST['alamat']) : '-';
$hp = isset($_POST['hp']) ? $mysqli->real_escape_string($_POST['hp']) : '-';
$hp2 = isset($_POST['hp2']) ? $mysqli->real_escape_string($_POST['hp2']) : '';
$keterangan = isset($_POST['keterangan']) ? $mysqli->real_escape_string($_POST['keterangan']) : '';

$response = array();
$response['code'] = 'ERROR';
$response['msg'] = 'Unauthorized user...';
$mode='add';
switch ($mode) {
    case 'add':
        $sql = "";
        $sql = "INSERT INTO driver (`user_id`,`driver`,`alamat`,`kota`,`hp`,`hp2`,`keterangan`) ";
        $sql .=" VALUES('{$user_id}','{$nama_driver}','{$alamat}','{$kota}','{$hp}','{$hp2}','{$keterangan}')";
        $result = $mysqli->query($sql);
        if ($result) {
            $response['code'] = 'SUCCESS';
            $response['msg'] = 'Sukses Tambah Data Driver';
        } else {
            $response['code'] = 'ERROR';
            $response['msg'] = "Tambah Data Driver Error :" . $mysqli->error;
        }
        break;
    case 'edit':
        $sql = "";
        $sql = "UPDATE driver SET user_id='{$user_id}', driver='{$nama_driver}',alamat='{$alamat}',kota='{$kota}',hp='{$hp}',hp2='{$hp2}',keterangan='{$keterangan}' WHERE id='{$id}' ";

        $result = $mysqli->query($sql);
        $response['sql'] = $sql;
        if ($result) {
            $response['code'] = 'SUCCESS';
            $response['msg'] = 'Sukses Edit Data Driver';
        } else {
            $response['code'] = 'ERROR';
            $response['msg'] = "Edit Data Driver Error :" . $mysqli->error;
        }
        break;
    case 'delete':
        $sql = "delete from driver where id='{$id}'  LIMIT 1";

        $result = $mysqli->query($sql);
        if ($result) {
            $response['code'] = 'SUCCESS';
            $response['msg'] = 'Sukses Hapus Data Driver';
        } else {
            $response['code'] = 'ERROR';
            $response['msg'] = "Hapus Data Driver Error :" . $mysqli->error;
        }
        break;
}
$mysqli->close();
print json_encode($response);
?>