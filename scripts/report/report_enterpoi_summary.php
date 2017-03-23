<?php
require_once '../connection.php';
$from = isset($_POST['from']) ? mysql_real_escape_string($_POST['from']) : '2999-01-01';
$to = isset($_POST['to']) ? mysql_real_escape_string($_POST['to']) : '2999-03-01';
$vh_id = isset($_POST['vh_id']) ? mysql_real_escape_string($_POST['vh_id']) : '0';
$query = "select id,tdate,poi_id,poi,lat,lng,address from track where  tdate>='" . $from . "' AND tdate<='" . $to . "' AND vh_id='" . $vh_id . "' ORDER BY tdate ASC";
$result = mysql_query($query);

$response = array();
if ($result) {
    $last_poi = "";
    $total = "";
    while ($row = mysql_fetch_assoc($result)) {
        $poi = $row['poi'];
        if (($poi != "") && ($last_poi == "")) {
            $total++;
            $last_poi = $row['poi'];
            if (isset($response[$poi])) {
                $response[$poi] ++;
            } else {
                $response[$poi] = 1;
            }
        } else if (($poi != "") && ($last_poi != "") && ($poi != $last_poi)) {
            $total++;
            $row['jumlah'] = $total;
            $last_poi = $row['poi'];
            if (isset($response[$poi])) {
                $response[$poi] ++;
            } else {
                $response[$poi] = array();
                $response[$poi] = 1;
            }
        }
        if ($poi == "") {
            $last_poi = "";
        }
    }
}
$temp=array();
foreach ($response as $key => $value){
    $node=array();
    $node['poi']=$key;
    $node['jumlah']=$value;
    array_push($temp,$node);
}
mysql_close($connection);
echo json_encode($temp);
?>
