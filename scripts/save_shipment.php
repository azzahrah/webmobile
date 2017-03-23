<?php
require_once 'connection.php';
//$user_level = isset($_SESSION['user_level']) ? $mysqli->real_escape_string($_SESSION['user_level']) : '';

$id = isset($_POST['id']) ? intval($_POST['id']) : 0; //755
$vh_id = isset($_POST['vh_id']) ? intval($_POST['vh_id']) : 0; //755
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0; //755
$mode = isset($_POST['mode']) ? $mysqli->real_escape_string($_POST['mode']) : ''; //edit
$type_unit = isset($_POST['type_unit']) ? $mysqli->real_escape_string($_POST['type_unit']) : ''; //edit
$no_order = isset($_POST['no_order']) ? $mysqli->real_escape_string($_POST['no_order']) : ''; //edit
$no_sj = isset($_POST['no_sj']) ? $mysqli->real_escape_string($_POST['no_sj']) : ''; //edit
$no_sm = isset($_POST['no_sm']) ? $mysqli->real_escape_string($_POST['no_sm']) : ''; //edit
$kode_tax = isset($_POST['kode_tax']) ? $mysqli->real_escape_string($_POST['kode_tax']) : ''; //edit
$area = isset($_POST['area']) ? $mysqli->real_escape_string($_POST['area']) : ''; //edit
$tgl_transaksi = isset($_POST['tgl_transaksi']) ? $mysqli->real_escape_string($_POST['tgl_transaksi']) : ''; //edit
$nama_pengirim = isset($_POST['nama_pengirim']) ? $mysqli->real_escape_string($_POST['nama_pengirim']) : ''; //edit
$kernet = isset($_POST['kernet']) ? $mysqli->real_escape_string($_POST['kernet']) : ''; //edit
$route_id = isset($_POST['route_id']) ? intval($_POST['route_id']) : 0;
$id_asal = isset($_POST['id_asal']) ? intval($_POST['id_asal']) : 0;
$id_tujuan = isset($_POST['id_tujuan']) ? intval($_POST['id_tujuan']) : 0;
$id_driver1 = isset($_POST['id_driver1']) ? intval($_POST['id_driver1']) : 0;
$id_driver2 = isset($_POST['id_driver2']) ? intval($_POST['id_driver2']) : 0;
$status_id = isset($_POST['status_id']) ? intval($_POST['status_id']) : '0';
$start = isset($_POST['start']) ? $mysqli->real_escape_string($_POST['start']) : '0000-00-00';
$arrive_est = isset($_POST['arrive_est']) ? $mysqli->real_escape_string($_POST['arrive_est']) : '0000-00-00';
$descr = isset($_POST['descr']) ? $mysqli->real_escape_string($_POST['descr']) : '';

$destinations = isset($_POST['destinations']) ? $_POST['destinations'] : '';
$arrDestinations = json_decode($destinations);

$response = array();
$response['code'] = 'ERROR';
$response['msg'] = '';
$sql = "";
$error = false;
$mode='add';
switch ($mode) {
    case "add":
        $mysqli->autocommit(FALSE);
        $sql = "INSERT INTO shipment (";
        $sql .= "`vh_id`,`user_id`,`route_id`,`id_asal`,`id_tujuan`,`id_driver1`,";
        $sql .= "`id_driver2`,`start`,`descr`,`type_unit`,";
        $sql .= "`no_order`,`no_sj`,`no_sm`,`kode_tax`,`area`,`tgl_transaksi`,";
        $sql .= "`nama_pengirim`,`kernet`) ";

        $sql .= "values('" . $vh_id . "','" . $user_id . "','" . $route_id . "','" . $id_asal . "','" . $id_tujuan . "',";
        $sql .= "'" . $id_driver1 . "','" . $id_driver2 . "','" . $start . "','" . $descr . "','" . $type_unit . "',";
        $sql .= "'" . $no_order . "','" . $no_sj . "','" . $no_sm . "',";
        $sql .= "'" . $kode_tax . "','" . $area . "','" . $tgl_transaksi . "',";
        $sql .= "'" . $nama_pengirim . "','" . $kernet . "')";
        $response['sql'] = $sql;
        if (!$mysqli->query($sql)) {
            $response['msg'] = $mysqli->error;
            $error = true;
        } else {
            $response['code'] = 'SUCCESS';
            $response['msg'] = 'Add Shipment Sukses';
            $lastId = $mysqli->insert_id;
//            foreach ($arrDestinations as $result) {
//                $sql = "INSERT INTO shipment_dest (`shipment_id`,`nama_penerima`,`poi_id`,`poi`,`lat`,`lng`,`dist`,`arrive_est`,`duration`) values(";
//                $sql .="'" . $lastId . "',";
//                $sql .="'" . $result->nama_penerima . "',";
//                $sql .="'" . intval($result->poi_id) . "',";
//                $sql .="'" . $result->poi . "',";
//                $sql .="'" . floatval($result->lat) . "',";
//                $sql .="'" . floatval($result->lng) . "',";
//                $sql .="'" . floatval($result->dist) . "',";
//                $sql .="'" . $result->arrive_est . "',";
//                $sql .="'" . $result->duration . "')";
//
//                if (!$mysqli->query($sql)) {
//                    $response['code'] = 'ERROR';
//                    $response['msg'] = $mysqli->error;
//                    $response['sql'] = $sql;
//                    $error = true;
//                } else {
//                    $response['code'] = 'SUCCESS';
//                    $response['msg'] = "INSERT DATA SUCCESS";
//                }
//            }
        }
        if (!$error) {
            $mysqli->commit();
        }
        break;
    case 'edit':
        $mysqli->autocommit(FALSE);
        $sql = "UPDATE shipment SET ";
        $sql .= " vh_id='" . $vh_id . "',user_id='" . $user_id . "',route_id='" . $route_id . "',dist='" . $dist . "',";
        $sql .= "poi_origin='" . $poi_origin . "',poi_origin_lat='" . $poi_origin_lat . "',poi_origin_lng='" . $poi_origin_lng . "',driver1_id='" . $driver1_id . "',";
        $sql .= "driver2_id='" . $driver2_id . "',start='" . $start . "',arrive_est='" . $arrive_est . "',descr='" . $descr . "',type_unit='" . $type_unit . "',";
        $sql .= "no_order='" . $no_order . "',no_sj='" . $no_sj . "',no_sm='" . $no_sm . "',";
        $sql .= "kode_tax='" . $kode_tax . "',area='" . $area . "',tgl_transaksi='" . $tgl_transaksi . "',";
        $sql .= "nama_pengirim='" . $nama_pengirim . "',kernet='" . $kernet . "' WHERE id='" . $id . "'";
        $response['sql'] = $sql;
        if (!$mysqli->query($sql)) {
            $response['msg'] = $mysqli->error;
            $error = true;
        } else {
            $response['code'] = 'SUCCESS';
            $response['msg'] = 'Update Shipment Sukses';
            $lastId = $id;
            $mysqli->query("DELETE FROM shipment_dest where shipment_id='" . $lastId . "'");
            foreach ($arrDestinations as $result) {
                $sql = "INSERT INTO shipment_dest (`shipment_id`,`nama_penerima`,`poi_id`,`poi`,`lat`,`lng`,`dist`,`arrive_est`,`duration`) values(";
                $sql .="'" . $lastId . "',";
                $sql .="'" . $result->nama_penerima . "',";
                $sql .="'" . intval($result->poi_id) . "',";
                $sql .="'" . $result->poi . "',";
                $sql .="'" . floatval($result->lat) . "',";
                $sql .="'" . floatval($result->lng) . "',";
                $sql .="'" . floatval($result->dist) . "',";
                $sql .="'" . $result->arrive_est . "',";
                $sql .="'" . $result->duration . "')";

                if (!$mysqli->query($sql)) {
                    $response['code'] = 'ERROR';
                    $response['msg'] = $mysqli->error;
                    $response['sql'] = $sql;
                    $error = true;
                } else {
                    $response['code'] = 'SUCCESS';
                    $response['msg'] = "INSERT DATA SUCCESS";
                }
            }
        }
        if (!$error) {
            $mysqli->commit();
        }
        break;
    case 'delete':
        $sql = "DELETE FROM shipment where id='" . $id . "' LIMIT 1";
        if ($mysqli->query($sql)) {
            $response['code'] = 'SUCCESS';
            $response['msg'] = 'Delete Shipment Sukses';
        } else {
            $response['msg'] = "Delete Shipment Error :" . $mysqli->error;
        }
        $mysqli->close();
        break;
}
print json_encode($response);
?>