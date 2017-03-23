<?php
require_once '../connection.php';
$from = isset($_POST['from']) ? mysql_real_escape_string($_POST['from']) : '2999-01-01';
$to = isset($_POST['to']) ? mysql_real_escape_string($_POST['to']) : '2999-03-01';
$vh_id = isset($_POST['vh_id']) ? mysql_real_escape_string($_POST['vh_id']) : '0';
$query = "select id,tdate,poi_id,poi,lat,lng,address from track where  tdate>='" . $from . "' AND tdate<='" . $to . "' AND vh_id='" . $vh_id . "' ORDER BY tdate ASC";
$result = mysql_query($query);

$response = array();
$response['total'] = 0;
$response['sql'] = $query;
$response['ext'] = 'test data';

$response['data'] = array();
$data=array();
if ($result) {
    $last_poi_id = 0;
    $total=0;
    while ($row = mysql_fetch_assoc($result)) {
        $poi_id = (int) $row['poi_id'];
        if ( ($poi_id > 0) && ($last_poi_id == 0) ) 
        {
            $total++;
            $row['jumlah']=$total;
            $last_poi_id=(int) $row['poi_id'];
            $response['total'] ++;
            $row['jumlah']=$total;
            $row['enter_exit'] = "ENTER";
            $response['data'][] = $row;
        }else if(($poi_id > 0) && ($last_poi_id > 0) && ($poi_id != $last_poi_id)){
            $total++;
            $row['jumlah']=$total;
            $last_poi_id=(int) $row['poi_id'];
            $response['total'] ++;
            $row['jumlah']=$total;
            $row['enter_exit'] = "ENTER";
            $response['data'][] = $row;
        }        
        if($poi_id==0){
             $last_poi_id=0;
        }
    }
}
mysql_close($connection);
echo json_encode($response);
?>
