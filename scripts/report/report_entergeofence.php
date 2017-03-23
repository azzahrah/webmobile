<?php

require_once '../connection.php';
$from = isset($_POST['from']) ? mysql_real_escape_string($_POST['from']) : '2999-01-01';
$to = isset($_POST['to']) ? mysql_real_escape_string($_POST['to']) : '2999-03-01';
$vh_id = isset($_POST['vh_id']) ? mysql_real_escape_string($_POST['vh_id']) : '0';
//$query = "select id,count(alarm) as jumlah,vh_id,DATE(tdate) as tdate,alarm,gf,gf_id,lat,lng from track where alarm=7 and tdate>='" . $from . "' AND tdate<='" . $to . "' AND vh_id='" . $vh_id . "' group by alarm,DATE(tdate)";
//$query = "select id,vh_id,tdate,alarm,lat,lng from track where (alarm='7' or alarm='8') and tdate>='" . $from . "' AND tdate<='" . $to . "' AND vh_id='" . $vh_id . "'";
$query = "select id,vh_id, tdate,alarm,gf,gf_id,lat,lng from track where  tdate>='" . $from . "' AND tdate<='" . $to . "' AND vh_id='" . $vh_id . "' ORDER BY tdate ASC";
$result = mysql_query($query);

$response = array();
$response['total'] = 0;
$response['sql'] = $query;
$response['ext'] = 'test data';

$response['data'] = array();
$data=array();
if ($result) {
    $last_gf_id = 0;
    $total=0;
    while ($row = mysql_fetch_assoc($result)) {
        $gfid = (int) $row['gf_id'];
        if ( ($gfid > 0) && ($last_gf_id == 0)) {
            $total++;
            $row['jumlah']=$total;
            $last_gf_id=(int) $row['gf_id'];
            $response['total'] ++;
            $row['enter_exit'] = "ENTER";
            $response['data'][] = $row;
        }else if ( ($gfid > 0) && ($last_gf_id > 0) && ($gfid !=$last_gf_id)) {
            $total++;
            $row['jumlah']=$total;
            $last_gf_id=(int) $row['gf_id'];
            $response['total'] ++;
            $row['enter_exit'] = "ENTER";
            $response['data'][] = $row;
        }
        if($gfid==0){
             $last_gf_id=0;
        }
    }
}
mysql_close($connection);
echo json_encode($response);
?>
