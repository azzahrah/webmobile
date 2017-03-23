<?php

require_once '../connection.php';

class Info {

    public $second;
    public $descr;

}

$from = isset($_POST['from_date']) ? $mysqli->real_escape_string($_POST['from_date']) : '';
$to = isset($_POST['to_date']) ? $mysqli->real_escape_string($_POST['to_date']) : '';
$vh_id = isset($_POST['vh_id']) ? $mysqli->real_escape_string($_POST['vh_id']) : '';

function calcdiff($from, $to) {
    $finish = new DateTime($to);
    $start = new DateTime($from);

    $diff = $finish->diff($start);
    $second = ($diff->s) + ($diff->i * 60) + ($diff->h * 3600) + ($diff->d * 3600 * 24);
    $totalSecond = $second;
    $str = "";
    $day = 3600 * 24;
    $hour = 60 * 60;
    $minute = 60;

    if ($second >= $day) {
        $str = intval(($second / $day)) . " Day ";
        $second = $second % $day;
    }
    if ($second >= $hour) {
        $str .= intval($second / $hour) . " Hour ";
        $second = $second % $hour;
    }
    if ($second >= $minute) {
        $str .=intval($second / $minute) . " Minute ";
        $second = $second % $minute;
    }
//    if ($second < $minute) {
//        $str .=$second. " Detik ";
//    }
    $arr = new Info();
    $arr->second = $totalSecond;
    $arr->descr = $str;
    return $arr;
}

function secToTime($sec) {
    $day = 60 * 60 * 24;
    $hour = 60 * 60;
    $minute = 60;

    $diffStr = "";
    if ($sec >= $day) {
        $diffStr = intval($sec / $day) . " Day ";
        $sec = intval($sec % $day);
    }
    if ($sec >= $hour) {
        $diffStr .= intval($sec / $hour) . " Hour ";
        $sec = intval($sec % $hour);
    }
    if ($sec >= $minute) {
        $diffStr .= intval($sec / $minute) . " Minute ";
    }
    return $diffStr;
}

function isNextStart($rows, $index) {
    $totalOn = 0;
    $totalOff = 0;
    $counter = 0;
    while (true) {
        $counter++;
        $row = $rows[++$index];
        if ($row == null) {
            break;
        }
        //|| ((int) $row['acc'] == 1)
        if ((int) $row['speed'] > 10) {
            $totalOn++;
        } else {
            $totalOff++;
        }
        if ($counter > 5) {
            break;
        }
    }
    if ($totalOn > $totalOff) {
        return true;
    } else {
        return false;
    }
}

function isNextStop($rows, $index) {
    $totalOn = 0;
    $totalOff = 0;
    $counter = 0;
    while (true) {
        $counter++;
        $row = $rows[++$index];
        if ($row == null) {
            break;
        }
        if (((int) $row['speed'] > 10) || ((int) $row['acc'] == 1)) {
            $totalOn++;
        } else {
            $totalOff++;
        }
        if ($counter > 5) {
            break;
        }
    }
    if ($totalOn < $totalOff) {
        return true;
    } else {
        return false;
    }
}

$data = array();
$data['total'] = 0;
$data['sql'] = '';
$data['msg'] = '';
$data['totalOn'] = 0;
$data['totalOff'] = 0;
$data['data'] = array();
$first = true;
$arrayStop = array();
$arrayStart = array();
$tempArrayStop = array();
$tempArrayStart = array();
$last_park = false;

$tempStop = array();

//echo $vh_id." ".$from ." ---- ". $to;
$query = "select * from track where tdate>='" . $from . "' AND tdate<='" . $to . "' AND vh_id='" . $vh_id . "' order by tdate";
$data['sql'] = $query;
$result = $mysqli->query($query);
if (!$result) {
    $root["msg"] = "Error:" . $mysqli->error();
    $mysqli->close();
    print json_encode($root);
    exit;
}

$temps = array();
$index = 0;

while ($r = $result->fetch_assoc()) {
    $temps[$index++] = $r;
}
$result->free();
$mysqli->close();
for ($i = 0; $i < count($temps); $i++) {
    $row = $temps[$i];
    if ($first == false) {
        if ($last_park) {
            //|| (int) $row['speed'] <= 5
            if ((int) $row['acc'] == 0) { //from stop to stop
                array_push($arrayStop, $row);
            } else { //from stop to start
                //check next track, if acc==0, detect still on
                $next = $temps[$i + 1];
                if ($next != null) {
                    //|| (int) $next['speed'] <= 5
                    if ((int) $next['acc'] == 0) { //still stop
                        array_push($arrayStop, $row);
                        continue;
                    }
                }
                array_push($arrayStop, $row);
                $first = reset($arrayStop);
                $last = end($arrayStop);
                $diff = calcdiff($first['tdate'], $last['tdate']);
                $item = array();
                $item['status'] = 'Stop';
                 $item['nopol'] = $first['nopol'];
                $item['tdate'] = $first['tdate'];
                $item['tdate2'] = $last['tdate'];
                $item['lat'] = $first['lat'];
                $item['lng'] = $first['lng'];
                $item['poi'] = $first['poi'];
                $item['address'] = $last['address'];
                $item['second'] = $diff->second;
                $item['duration'] = $diff->descr;
                $item['park'] = true;
                array_push($data['data'], $item);

                $last_park = false;
                $arrayStart = array();
                array_push($arrayStart, $row);
                unset($arrayStop);
            }
        } else {
            if ((int) $row['acc'] == 1) {
                array_push($arrayStart, $row);
            } else {
                $next = $temps[$i + 1];
                if ($next != null) {
                    if ((int) $next['acc'] == 1) { //still stop
                        array_push($arrayStart, $row);
                        continue;
                    }
                }
                array_push($arrayStart, $row);
                $first = reset($arrayStart);
                $last = end($arrayStart);
                $diff = calcdiff($first['tdate'], $last['tdate']);
                $item = array();
                $item['status'] = 'Start';
                 $item['nopol'] = $first['nopol'];
                $item['tdate'] = $first['tdate'];
                $item['tdate2'] = $last['tdate'];
                $item['lat'] = $first['lat'];
                $item['lng'] = $first['lng'];
                $item['poi'] = $first['poi'];
                $item['address'] = $last['address'];
                $item['second'] = $diff->second;
                $item['duration'] = $diff->descr;
                $item['park'] = false;
                array_push($data['data'], $item);

                $last_park = true;
                $arrayStop = array();
                array_push($arrayStop, $row);
                unset($arrayStart);
            }
        }
    } else { //first
        $first = false;
        if ((int) $row['acc'] == 0) {
            $last_park = true;
            array_push($arrayStop, $row);
        } else {
            array_push($arrayStart, $row);
        }
    }
}

if ($last_park == false) {
    if (isset($arrayStart)) {
        $first = reset($arrayStart);
        $last = end($arrayStart);
        $diff = calcdiff($first['tdate'], $last['tdate']);

        $item = array();
        $item['status'] = 'Start';
         $item['nopol'] = $first['nopol'];
        $item['tdate'] = $first['tdate'];
        $item['tdate2'] = $last['tdate'];
        $item['lat'] = $first['lat'];
        $item['lng'] = $first['lng'];
        $item['poi'] = $first['poi'];
        $item['address'] = $last['address'];
        $item['second'] = $diff->second;
        $item['duration'] = $diff->descr;
        $item['park'] = false;
        array_push($data['data'], $item);
    }
} else {
    if (isset($arrayStop)) {
        $first = reset($arrayStop);
        $last = end($arrayStop);
        $diff = calcdiff($first['tdate'], $last['tdate']);
        $item = array();
        $item['status'] = 'Stop';
         $item['nopol'] = $first['nopol'];
        $item['tdate'] = $first['tdate'];
        $item['tdate2'] = $last['tdate'];
        $item['lat'] = $first['lat'];
        $item['lng'] = $first['lng'];
        $item['poi'] = $first['poi'];
        $item['address'] = $last['address'];
        $item['second'] = $diff->second;
        $item['duration'] = $diff->descr;
        $item['park'] = true;
        array_push($data['data'], $item);
    }
}
$totalOn = 0;
$totalOff = 0;
foreach ($data['data'] as $key => $value) {
    $data['total'] ++;
    if ($value['park'] == false) {
        $totalOn+=$value['second'];
    } else {
        $totalOff+=$value['second'];
    }
}
$data['totalOn'] = secToTime($totalOn);
$data['totalOff'] = secToTime($totalOff);
print json_encode($data);
?>
