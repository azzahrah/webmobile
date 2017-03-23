<?php

require_once '../connection.php';
$from = isset($_POST['from']) ? mysql_real_escape_string($_POST['from']) : '2999-01-01';
$to = isset($_POST['to']) ? mysql_real_escape_string($_POST['to']) : '2999-03-01';
$vh_id = isset($_POST['vh_id']) ? mysql_real_escape_string($_POST['vh_id']) : '0';
$query = "select id,tdate,gf_id,gf,lat,lng,address from track where  tdate>='" . $from . "' AND tdate<='" . $to . "' AND vh_id='" . $vh_id . "' ORDER BY tdate ASC";
$result = mysql_query($query);

$response = array();
$response2 = array();
if ($result) {
    $last_gf = "";
    $total = "";
    while ($row = mysql_fetch_assoc($result)) {
        $gf = $row['gf'];
        if (($gf != "") && ($last_gf == "")) {
            $total++;
            $last_gf = $row['gf'];
            if (isset($response[$gf])) {
                $response[$poi] ++;
            } else {
                $response[$poi] = 1;
            }
        } else if (($gf != "") && ($last_gf != "") && ($gf != $last_gf)) {
            $total++;
            $row['jumlah'] = $total;
            $last_gf = $row['gf'];
            if (isset($response[$gf])) {
                $response[$gf] ++;
            } else {
                $response[$gf] = array();
                $response[$gf] = 1;
            }
        }
        if ($gf == "") {
            $last_gf = "";
        }
    }
    foreach ($response as $key => $value) {
        $item=array("gf"=>$key,"jumlah"=>$value);
        array_push($response2, $item);
    }
}
mysql_close($connection);
echo json_encode($response2);
?>
