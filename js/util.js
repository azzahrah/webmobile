var PATH_ICON = '';
var util = {};
util.mouse = {
    x: 0,
    y: 0
};
util.dialogSettingIO;
util.winIcon;
util.second = 1;
util.minute = 60;
util.hour = 1 * 60 * 60;
util.day = 1 * 60 * 60 * 24;//864000
util.month = 1 * 60 * 60 * 24 * 30;//2592000
util.year = 1 * 60 * 60 * 24 * 30 * 12;
util.getRandomDate = function () {
    var date = new Date();
    var str = date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate() + " " + date.getHours() + ":" + date.getMinutes() + ":" + date.getSeconds();

    return str;
};

util.debug = function (log_txt) {
    if (typeof window.console != 'undefined') {
        //console.log(log_txt);
    }
};
util.toRadians = function (degrees) {
    return (degrees * Math.PI) / 180;
};
util.distanceBetweenPoints = function (p1, p2) {
    var R = 6371, // mean earth radius in km
            lat1 = util.toRadians(p1[0]),
            lon1 = util.toRadians(p1[1]),
            lat2 = util.toRadians(p2[0]),
            lon2 = util.toRadians(p2[1]),
            dLat = lat2 - lat1,
            dLon = lon2 - lon1,
            a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.sin(dLon / 2) * Math.sin(dLon / 2) * Math.cos(lat1) * Math.cos(lat2),
            c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a)),
            d = R * c;
    return d;
};
util.distance = function (lat1, lng1, lat2, lng2) {
    var R = 6371; // Radius of the earth in km
    var dLat = util.deg2rad(lat2 - lat1); // deg2rad below
    var dLon = util.deg2rad(lng2 - lng1);
    var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(util.deg2rad(lat1)) * Math.cos(util.deg2rad(lat2)) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    var d = R * c; // Distance in km
    return d;
};
util.deg2rad = function (deg) {
    return deg * (Math.PI / 180);
};
util.insidePolygon = function (point, vs) {
    var x = point[0], y = point[1];
    var inside = false;
    for (var i = 0, j = vs.length - 1; i < vs.length; j = i++) {
        var xi = vs[i][0], yi = vs[i][1];
        var xj = vs[j][0], yj = vs[j][1];
        var intersect = ((yi > y) != (yj > y))
                && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
        if (intersect)
            inside = !inside;
    }

    return inside;
};
util.pagerFilter = function (data) {
    if (typeof data.length == 'number' && typeof data.splice == 'function') {    // is array
        data = {
            total: data.length,
            rows: data
        }
    }
    var dg = $(this);
    var opts = dg.datagrid('options');
    var pager = dg.datagrid('getPager');
    pager.pagination({
        onSelectPage: function (pageNum, pageSize) {
            opts.pageNumber = pageNum;
            opts.pageSize = pageSize;
            pager.pagination('refresh', {
                pageNumber: pageNum,
                pageSize: pageSize
            });
            dg.datagrid('loadData', data);
        }
    });
    if (!data.originalRows) {
        data.originalRows = (data.rows);
    }
    var start = (opts.pageNumber - 1) * parseInt(opts.pageSize);
    var end = start + parseInt(opts.pageSize);
    data.rows = (data.originalRows.slice(start, end));
    return data;
};
util.pagerFilterSummary = function (data) {
    if (typeof data.length == 'number' && typeof data.splice == 'function') {    // is array
        data = {
            total: data.length,
            rows: data
        }
    }
    var dg = $(this);
    var opts = dg.datagrid('options');
    var pager = dg.datagrid('getPager');
    pager.pagination({
        onSelectPage: function (pageNum, pageSize) {
            opts.pageNumber = pageNum;
            opts.pageSize = pageSize;
            pager.pagination('refresh', {
                pageNumber: pageNum,
                pageSize: pageSize
            });
            dg.datagrid('loadData', data);
        }
    });
    if (!data.originalRows) {
        data.originalRows = (data.rows);
    }
    var start = (opts.pageNumber - 1) * parseInt(opts.pageSize);
    var end = start + parseInt(opts.pageSize);
    data.rows = (data.originalRows.slice(start, end));
    return data;
};
util.convert = function (rows) {
    function exists(rows, reseller_id) {
        for (var i = 0; i < rows.length; i++) {
            if (rows[i].id === reseller_id)
                return true;
        }
        return false;
    }

    var nodes = [];
    // get the top level nodes
    for (var i = 0; i < rows.length; i++) {
        var row = rows[i];
        if (!exists(rows, row.reseller_id)) {
            nodes.push({
                id: row.id,
                text: row.text
            });
        }
    }

    var toDo = [];
    for (var i = 0; i < nodes.length; i++) {
        toDo.push(nodes[i]);
    }
    var index = 0;
    while (toDo.length) {
        var node = toDo.shift(); // the parent node
        // get the children nodes
        for (var i = 0; i < rows.length; i++) {
            var row = rows[i];
            if (row.reseller_id === node.id) {
                var child = {id: row.id, text: row.text};
                if (node.children) {
                    node.children.push(child);
                } else {
                    node.children = [child];
                }
                //toDo.push(child);
            }
        }
        ////console.log((index++)+"."+ toDo.length);
    }
    ////console.log(nodes);
    return nodes;
};
util.parseDateTime = function (d)
{
    if (d == undefined)
        return null;
    var p = d.split(' ');
    if (p.length < 2)
        return null;
    var q = p[0].split('-');
    if (q.length < 2)
        return null;
    var r = p[1].split(':');
    if (r.length < 2)
        return null;
    return new Date(q[0], q[1] - 1, q[2], r[0], r[1], r[2]);
};
util.parseDateTime2 = function (d)
{
    try {
        var p = d.split(' ');
        var q = p[0].split('-');
        var r = p[1].split(':');
        return new Date(q[0], q[1] - 1, q[2], r[0], r[1], r[2]);
    } catch (ex) {
        return null;
    }
};
util.diffMS = function (first, last) {
    var dateLast = util.parseDateTime(last);
    var dateFirst = util.parseDateTime(first);
    if (dateLast == null || dateFirst == null)
        return 0;
    return Math.abs(dateLast.getTime() - dateFirst.getTime());
};
util.msToDateDescr = function (ms) {
    var temp = ms;
    var day = (1000 * 3600 * 24);
    var hour = (1000 * 3600);
    var minute = (1000 * 60);
    var str = "";
    if (ms >= day) {
        str += (ms / day).toFixed(0) + " Hari ";
        ms = ms % day;
    }
    if (ms >= hour) {
        str += (ms / hour).toFixed(0) + " Jam ";
        ms = ms % hour;
    }
    if (ms >= minute) {
        str += (minute / minute).toFixed(0) + " Menit ";
    }
    return str;
};
util.create_info_park = function (totalSecond) {
    var temp = totalSecond;
    var day = (3600 * 24);
    var hour = (3600);
    var minute = (60);
    var str = "";
    if (totalSecond >= day) {
        str += (totalSecond / day).toFixed(0) + " Hari ";
        totalSecond = totalSecond % day;
    }
    if (totalSecond >= hour) {
        str += (totalSecond / hour).toFixed(0) + " Jam ";
        totalSecond = totalSecond % hour;
    }
    if (totalSecond >= minute) {
        str += (minute / minute).toFixed(0) + " Menit ";
    }
    return str;
};
util.calc_park = function (park_date) {
    if (park_date == "0000-00-00 00:00:00" || park_date == "") {
        return "";
    }
    var currDate = new Date();
    var parkDate = util.parseDateTime(park_date);
    if (parkDate == null)
        return "";
    var totalSecond = currDate - parkDate;
    if (totalSecond > 0) {
        totalSecond = totalSecond / 1000;
    }
    var str = "";
    if (totalSecond >= util.year) {
        str += (totalSecond / util.year).toFixed(0) + " Tahun ";
        totalSecond = totalSecond % year;
    }
    if (totalSecond >= util.month) {
        str += (totalSecond / util.month).toFixed(0) + " Bulan ";
        totalSecond = totalSecond % util.month;
    }
    if (totalSecond >= util.day) {
        str += (totalSecond / util.day).toFixed(0) + " Hari ";
        totalSecond = totalSecond % util.day;
    }
    if (totalSecond >= util.hour) {
        str += (totalSecond / util.hour).toFixed(0) + " Jam ";
        totalSecond = totalSecond % util.hour;
    }
    if (totalSecond >= 300) {
        str += (totalSecond / 60).toFixed(0) + " Menit ";
    }
    return str;
};
util.isPark = function (_parkDate) {
    var parkDate = util.parseDateTime(_parkDate);
    if (parkDate == null)
        return false;

    var currDate = new Date();
    var diff = currDate - parkDate;
    if (diff > (1000 * 60 * 10)) {
        return true;
    }
    return false;

};
util.parse_status = function (v)
{
    console.log('parse_status');
    var status_on_off = "off";
    var delay_time = "";
    var delay = 0;
    var currentDate = new Date();
    if (v.tdate == undefined || v.tdate == null) {
        v['status'] = ((v.acc == 1) || (v.speed >= 5)) ? "on" : "stop";
        v['delay'] = 0;
        v['info'] = '<a href="#" class="' + v['status'] + '">0 ' + lang.second + '</a>';
        console.log('v.tdate==null');
        return;
    }
    //  //console.log( myUtil.parseDateTime(v.tdate));
    var delay_second = currentDate - util.parseDateTime(v.tdate);
    if (delay_second == null)
        delay_second = 0;
    if (delay_second > 0) {
        delay_second = delay_second / 1000;
    }
    delay = delay_second;
    if (delay_second < 3600) {
        status_on_off = ((v.acc == 1) || (v.speed >= 5)) ? "on" : "stop";
        if (delay_second >= 60) {
            delay_second = delay_second / 60;
            delay_time = '<a href="#" class="on">' + delay_second.toFixed(0) + ' ' + lang.minute + '</a>';
        } else {
            delay_second = delay_second >= 0 ? delay_second.toFixed(0) : "0";
            delay_time = '<a href="#" class="on">' + delay_second + ' ' + lang.second + '</a>';
        }
    } else if (delay_second >= 3600) {  //2 jam
        delay_second = delay_second / 3600;
        delay_time = '<a href="#" class="off">' + delay_second.toFixed(0) + ' ' + lang.hour + '</a>';
    } else if (delay_second >= 2592000) {  //Bulan
        delay_second = delay_second / 2592000;
        delay_time = '<a href="#" class="off">' + delay_second.toFixed(0) + ' ' + lang.month + '</a>';
    } else if (delay_second >= 864000) {  //Hari
        delay_second = delay_second / 864000;
        delay_time = '<a href="#" class="off">' + delay_second.toFixed(0) + ' ' + lang.day + '</a>';
    }

    v['status'] = status_on_off;
    v['delay'] = delay;
    v['info'] = delay_time;
    return v;
};
util.hourToTime = function (time) {
    var str = '';
    if (time <= 0) {
        return "";
    }
    if (time >= 24) {
        str = parseInt((time / 24), 10) + ' Hari ';
        time = time % 24;
    }
    if (time > 0) {
        str += parseInt(time, 10) + ' Jam ';
    }
    return str;
};
util.calc_duration = function (from_date, to_date)
{

    var duration = "";
    var delay_second = to_date - from_date;
    if (delay_second > 0) {
        delay_second = delay_second / 1000;
    }
    // //console.log(delay_second +' - '+ v.nopol +' - '+ v.tdate);
    if (delay_second >= util.month) {  //Bulan
        delay_second = delay_second / util.month;
        duration = delay_second.toFixed(0) + ' ' + lang.month;
    }
    if (delay_second >= util.day) {  //Hari
        delay_second = delay_second / util.day;
        duration += delay_second.toFixed(0) + ' ' + lang.day;
    }
    if (delay_second >= util.hour) {  //2 jam
        delay_second = delay_second / util.hour;
        duration += delay_second.toFixed(0) + ' ' + lang.hour;
    }
    if (delay_second >= util.second) {
        delay_second = delay_second / util.second;
        duration += delay_second.toFixed(0) + ' ' + lang.second;
    } else {
        delay_second = delay_second >= 0 ? delay_second.toFixed(0) : "0";
        duration += delay_second + ' ' + lang.second;
    }
    return duration;
};
util.batt_level = function (level) {
    var str = "";
    switch (level) {
        case -1:
            str = "N/A";
            break;
        case 0:
            str = "Batt Off";
            break;
        case 1:
            str = "Battt Low Batt";
            break;
        case 2:
            str = "Very Low Batt";
            break;
        case 3:
            str = "Low Batt";
            break;
        case 4:
            str = "Medium Batt";
            break;
        case 5:
            str = "High Batt";
            break;
        case 6:
            str = "Full Batt";
            break;
    }
    return str;
};
util.acc_state = function (state) {
    return (state == 1) ? "ON" : "OFF";
};
util.charge_state = function (state) {
    return (state == 1) ? "ON" : "OFF";
};
util.stringDivider = function (str, width, spaceReplacer) {
    if (str == '') {
        return 'Alamat Kosong';
    }
    if (str.length > width) {
        var p = width
        for (; p > 0 && str[p] != ' '; p--) {
        }
        if (p > 0) {
            var left = str.substring(0, p);
            var right = str.substring(p + 1);
            return left + spaceReplacer + util.stringDivider(right, width, spaceReplacer);
        }
    }
    return str;
};
util.create_info = function (v) {
    var result = "<table class='tblview'>"
            + "<tr onClick=select_gps('" + v.id + "');><td><img src='themes/icon/" + v.status + ".png?v=0.2'  class='imgstate'/></td><td class='nopol'>" + v.nopol + "</td></tr>";
    if (v.alarm != "0" && v.alarm != "" && v.alarm != undefined) {
        result += "<tr><td>Alarm :</td><td>" + util.formatAlarm(v.alarm) + "</td></tr>";
    }
    result += "<tr><td>User :</td><td>" + v.real_name + "</td></tr>"
            + "<tr><td>Date:</td><td>" + v.tdate + "</td></tr>"
            + "<tr><td>Speed:</td><td>" + v.speed + " Km/Jam</td></tr>"
            + "<tr><td>Lat/Lng</td><td>" + v.lat + ',' + v.lng + "</td></tr>"
            + "<tr><td>POI:</td><td>" + v.poi + "</td></tr>"
            + "<tr><td>Position:</td><td>" + util.stringDivider(v.address, 60, "<br/>\n") + "</td></tr>"
            + "<tr><td>Lat/Lng:</td><td>" + v.lat + "," + v.lng + "</td></tr>"
            + "<tr><td>Info:</td><td>ACC: " + util.acc_state(v.acc) + ", Power: " + util.charge_state(v.charge_state) + ", Batt: " + util.batt_level(v.batt) + "</td></tr>"
            + "<tr><td>Driver</td><td>" + v.drv_name + "</td></tr>";
    if (parseInt(v.max_odo_oil, 10) > 0) {
        result += "<tr><td>Alarm Oli Aktif </td><td>:</td><td>" + v.max_odo_oil.toLocaleString() + " Km</td></tr>";
    }
    if (parseInt(v.max_park, 10) > 0) {
        result += "<tr><td>Alarm Parkir</td><td>:</td><td> " + v.max_park + " Jam</td></tr>";
        if (v.park != "") {
            result += "<tr><td>Parkir</td><td>:</td><td> " + v.park + "</td></tr>";
        }
    }

    result += "<tr><td>IMEI:</td><td>" + v.imei + ", Phone:" + v.phone + "</td></tr>"
            + "<table/>";
    return result;
};
util.create_marker_alarm = function (main, row) {
    var latLng = new google.maps.LatLng(parseFloat(row.lat), parseFloat(row.lng));
    var m = new google.maps.Marker({
        position: latLng,
        map: main.map,
        optimized: false,
        icon: 'icon/alarm/alarm.gif?v=0.2'// myUtil.create_icon_alarm(row.alarm)
    });
    google.maps.event.addListener(m, 'click', function () {
        if (main.iw != undefined) {
            main.iw.setMap(null);
        }
        main.iw = new google.maps.InfoWindow({content: util.create_info_alarm(row)});
        main.iw.setPosition(latLng);
        main.iw.open(main.map, m);
    });
    return m;
};
util.TileToQuadKey = function (x, y, zoom) {
    var quad = "";
    for (var i = zoom; i > 0; i--) {
        var mask = 1 << (i - 1);
        var cell = 0;
        if ((x & mask) != 0)
            cell++;
        if ((y & mask) != 0)
            cell += 2;
        quad += cell;
    }
    return quad;
};
util.rnd = function (min, max)
{
    return Math.floor(Math.random() * (max - min + 1) + min);
};
util.create_marker_poi = function (row) {
    var latLng = new google.maps.LatLng(parseFloat(row.lat), parseFloat(row.lng));
    var m = new google.maps.Marker({
        position: latLng,
        map: map,
        icon: util.create_icon_poi(row.icon)
    });
    google.maps.event.addListener(m, 'click', function () {
        if (iwMarker != undefined)
            iwMarker.setMap(null);
        iwMarker = new google.maps.InfoWindow({content: '<h1>' + row.poi + '</h1><br>' + row.descr});
        iwMarker.setPosition(latLng);
        iwMarker.open(map, m);
    });
    return m;
};
util.create_default_icon_poi = function () {
    var icon = L.icon({
        iconUrl: 'icon/poi/home.png',
        iconSize: [28, 32], // size of the icon
        //shadowSize: [50, 64], // size of the shadow
        iconAnchor: [22, 22], // point of the icon which will correspond to marker's location
        //shadowAnchor: [4, 62], // the same for the shadow
        popupAnchor: [-10, -22] // point from which the popup should open relative to the iconAnchor
    });
    return icon;
};
util.formatdate = function (date) {
    var y = date.getFullYear();
    var m = date.getMonth() + 1;
    var d = date.getDate();
    return y + '-' + (m < 10 ? ('0' + m) : m) + '-' + (d < 10 ? ('0' + d) : d);
};
util.formatdatetime = function (date) {
    var y = date.getFullYear();
    var m = date.getMonth() + 1;
    var d = date.getDate();
    var hh = date.getHours();
    var mm = date.getMinutes();
    var ss = date.getSeconds();
    var sf = y + '-' + (m < 10 ? ('0' + m) : m) + '-' + (d < 10 ? ('0' + d) : d) + ' ' + (hh < 10 ? ('0' + hh) : hh) + ':' + (mm < 10 ? ('0' + mm) : mm) + ':' + (ss < 10 ? ('0' + ss) : ss);
    console.log(sf);
    return sf;
};
util.parsedate = function (s) {
    if (!s || s == undefined)
        return new Date();
    var ss = (s.split('-'));
    var y = parseInt(ss[0], 10);
    var m = parseInt(ss[1], 10);
    var d = parseInt(ss[2], 10);
    if (!isNaN(y) && !isNaN(m) && !isNaN(d)) {
        return new Date(y, m - 1, d);
    } else {
        return new Date();
    }
};
util.parsedatetime = function (s) {
    if (!s || s == undefined)
        return new Date();
    var dt = s.split(' ');
    var ss = (dt[0].split('-'));
    var y = parseInt(ss[0], 10);
    var m = parseInt(ss[1], 10);
    var d = parseInt(ss[2], 10);
    var ss2 = (dt[1].split(':'));
    var hh = parseInt(ss2[0], 10);
    var mm = parseInt(ss2[1], 10);
    var ss = parseInt(ss2[2], 10);
    if (!isNaN(y) && !isNaN(m) && !isNaN(d) && !isNaN(hh) && !isNaN(mm) && !isNaN(ss)) {
        return new Date(y, m - 1, d, hh, mm, ss);
    } else {
        return new Date();
    }
};
util.formaticon = function (val, row) {
    return "<img src='icon/poi/'" + val + "'>";
};
util.formateditpoi = function (val, row) {
    return "<img src='icon/gear.png' onClick='edit_poi(" + row.id + ");'/>";
};
util.formateditgf = function (val, row) {
    return "<img src='icon/gear.png'/>";
};
util.formatStatus = function (val, row) {
    var str = "";
    switch (val) {
        case "on":
            str = "<div style='background:#0F0'>ACC ON</div>";
            break;
        case "stop":
            str = "<div style='background:yellow'>ACC OFF</div>";
            break;
        case "off":
            str = "<div style='background:orange;'>OFFLINE</div>";
            break;
    }
    return str;
};
util.formatLinkMap = function (val, row) {
////console.log(row);
    return '<a href="#" onClick="select_gps(' + row.id + ');">' + row.nopol + '</a>';
};
util.formatAlarm = function (val) {
    var str = "N/A";
    var alarm = parseInt(val, 10);
    switch (alarm) {
        case 1:
            str = "SOS ALARM";
            break;
        case 2:
            str = "POWER CUT ALARM";
            break;
        case 3:
            str = "LOW POWER";
            break;
        case 4:
            str = "SHOCK ALARM";
            break;
        case 5:
            str = "OVER SPEED ALARM";
            break;
        case 6:
            str = "LOW SPEED ALARM";
            break;
        case 7:
            str = "GEOFENCE IN";
            break;
        case 8:
            str = "GEOFENCE OUT";
            break;
        case 9:
            str = "OVERTIME PARK";
            break;
        case 10:
            str = "MOVE ALARM";
            break;
        case 11:
            str = "OVERTIME ALARM";
            break;
        case 12:
            str = "OVERTIME ALARM";
            break;
        case 13:
            str = "OUT OF ROUTE";
            break;
        case 14:
            str = "IO1_ACTIVE";
            break;
        case 15:
            str = "IO1_INACTIVE";
            break;
        case 16:
            str = "IO2_ACTIVE";
            break;
        case 17:
            str = "IO2_INACTIVE";
            break;
        case 18:
            str = "IO3_ACTIVE";
            break;
        case 19:
            str = "IO3_INACTIVE";
            break;
        case 20:
            str = "IO4_ACTIVE";
            break;
        case 21:
            str = "IO4_INACTIVE";
            break;
        case 22:
            str = "IO5_INACTIVE";
            break;
        case 23:
            str = "IO5_ACTIVE";
            break;
        case 24:
            str = "IO6_INACTIVE";
            break;
        case 25:
            str = "IO6_ACTIVE";
            break;
        case 26:
            str = "GSM_ANTENA_CUT";
            break;
        case 27:
            str = "GPS_ANTENA_CUT";
            break;
        case 28:
            str = "GPS_JAMMED";
            break;
        case 29:
            str = "GSM_JAMMED";
            break;
        case 30:
            str = "GSM_JAMMED_RELEASE";
            break;
        case 31:
            str = "GPS_JAMMED_RELEASE";
            break;
        case 32:
            str = "FATIGUE_DRIVING";
            break;
        case 33:
            str = "FATIGUE_RELIEVE";
            break;
        case 34:
            str = "ENTER_SLEEP_MODE";
            break;
        case 35:
            str = "EXIT_SLEEP_MODE";
            break;
        case 36:
            str = "HARS_ACCELERATION";
            break;
        case 37:
            str = "HARS_BREAKING";
            break;
        case 38:
            str = "EXTERNAL_POWER_RECONNECT";
            break;
        case 39:
            str = "EXTERNAL_POWER_LOW";
            break;
        case 40:
            str = "DOOR_OPEN";
            break;
        case 41:
            str = "DOOR_CLOSE";
            break;
    }
    return str;
};
util.getIconAlarm = function (id_alarm) {
    var image = "N/A";
    var alarm = parseInt(id_alarm, 10);
    switch (alarm) {
        case 1:
            image = "pushbutton32.png";
            break;
        case 2:
            image = "powercut32.png";
            break;
        case 3:
            image = "lowbatt32.png";
            break;
        case 4:
            image = "alarm32.png";
            break;
        case 5:
            image = "overspeed32.png";
            break;
        case 6:
            image = "lowspeed32.png";
            break;
        case 7:
            image = "alarm32.png";
            break;
        case 8:
            image = "alarm32.png";
            break;
        case 9:
            image = "overtime32.png";
            break;
        case 10:
            image = "move32.png";
            break;
        case 11:
            image = "overtime32.png";
            break;
        case 12:
            image = "overtime32.png";
            break;
        case 13:
            image = "alarm32.png";
            break;
        case 14:
            //image = "IO1 OPEN";
            image = "alarm32.png";
            break;
        case 15:
            //image = "IO2 OPEN";
            image = "alarm32.png";
            break;
        case 16:
            //image = "IO3 OPEN";
            image = "alarm32.png";
            break;
        case 17:
            //image = "IO4 OPEN";
            image = "alarm32.png";
            break;
        case 18:
            //image = "IO1 CLOSE";
            image = "alarm32.png";
            break;
        case 19:
            //image = "IO2 CLOSE";
            image = "alarm32.png";
            break;
        case 20:
            //image = "IO3 CLOSE";
            image = "alarm32.png";
            break;
        case 21:
            //image = "IO4 CLOSE";
            image = "alarm32.png";
            break;
        case 22:
            //image = "GSM ANTTENA CUT";
            image = "alarm32.png";
            break;
        case 23:
            //image = "GPS JAMMED";
            image = "alarm32.png";
            break;
        default:
            image = "alarm32.png";
            break;
    }
    return image;
};
util.formatAlarmPark = function (val, row) {
    val = parseInt(val, 10);
    if (isNaN(val) || val == 0) {
        return '<span style="background-color:#F00;color:#FFF;padding:2px;">Belum Diset</span>';
    } else {
        return '<span style="background-color:#0F0;color:#000;padding:2px;">' + val + ' Jam</span>';
    }

};
util.formatAlarmOil = function (val, row) {
// //console.log(val);
    val = parseInt(val, 10);
    if (isNaN(val) || val == 0) {
        return '<span style="background-color:#F00;color:#FFF;padding:2px;">Belum Diset</span>';
    } else {
        return '<span style="background-color:#0F0;color:#000;padding:2px;">' + val.toLocaleString() + ' Km</span>';
    }

};
util.formatAngle = function (val, row) {
    if (((val >= 0) && (val < 22)) || (val >= 337))
        return "Utara";
    if ((val >= 22) && (val < 67))
        return "Timur Laut";
    if ((val >= 67) && (val < 112))
        return "Timur";
    if ((val >= 112) && (val < 157))
        return "Tenggara";
    if ((val >= 157) && (val < 202))
        return "Selatan";
    if ((val >= 202) && (val < 247))
        return "Barat Daya";
    if ((val >= 247) && (val < 292))
        return "Barat";
    if ((val >= 292) && (val < 337))
        return "Barat Laut";
    return val;
};
util.formatSpeed = function (num, row) {
    if (num == undefined)
        return;
    var speed = parseInt(num, 10);
    if (speed > 0) {
        return num + " Km/Jam";
    }
    return "<strong style='color:#F00'>Berhenti</strong>";
};
util.formatOdometer = function (num, row) {
    var val = parseFloat(num);
    if (val >= 1000) {
        return (val / 1000).toFixed(2) + " Km";
    } else {
        return val + " Meter";
    }
};
util.formatOdometerKM = function (num, row) {
    if (num != undefined) {
        try {
            var d = parseFloat(num, 10);
            return d.toFixed(2) + " Km";
        } catch (ex) {
        }
    }
    return "0 Km";
};
util.formatStatusServiceOil = function (num, row) {
    var diff = parseInt(row.max_odo_oil, 10) - parseInt(row.odo, 10);
    return "Ganti Oli Kurang " + diff.toLocaleString() + " Km";
};
util.formatStatusOdometer = function (num, row) {
    //console.log(row);
};
util.formatBatt = function (val, row) {
    var str = "";
    switch (parseInt(val, 10)) {
        case -1:
            str = "N/A";
            break;
        case 0:
            //str = "Power Shutdown";
            str = "<span style='color:red;background:white;padding:1px;'>Batt Off</span>";
            break;
        case 1:
            //str = "Extremly Low Batt";
            str = "<span style='color:red;background:white;padding:1px;'>Low Batt</span>";
            break;
        case 2:
            //str = "Very Low Batt";
            str = "<span style='color:red;background:white;padding:1px;'>Low Batt</span>";
            break;
        case 3:
            //str = "Low Batt";
            str = "<span style='color:orange;background:white;padding:1px;'>Low Batt</span>";
            break;
        case 4:
            //str = "Medium Batt";
            str = "<span style='color:orange;background:white;padding:1px;'>Medium Batt</span>";
            break;
        case 5:
            //str = "High Batt";
            str = "<span style='color:green;background:white;padding:1px;'>High Batt</span>";
            break;
        case 6:
            //str = "Full Batt";
            str = "<span style='color:green;background:white;padding:1px;'>Full Batt</span>";
            break;
    }
    return str;
};
util.formatAcc = function (val, row) {
    if (val == 0) {
        return "<span style='color:red;'>OFF</span>";
    }
    return "<span style='color:green;'>ON</span>";
};
util.formatCharge = function (val, row) {
    if (val == 0) {
        return "<span style='color:red;'>OFF</span>";
    }
    return "<span style='color:green;'>ON</span>";
};
util.formatCutEngine = function (val, row) {
    if (val == 1) {
        return "<img src='icon/cut_engine.png'>";
    }
    return "";
};
util.formatFuelCut = function (val, row) {
    if (parseInt(val, 10) === 1) {
        return 'Supply BBM Off';
    } else {
        return 'Supply BBM On';
    }
    return 'N/A';
};
util.formatSignalGsm = function (val, row) {
    var str = "N/A";
    switch (parseInt(val, 10)) {
        case -1:
            str = "Info Tidak Tersedia";
            break;
        case 0:
            str = "No Signal";
            break;
        case 1:
            str = "Very Weak Signal";
            break;
        case 2:
            str = "Weak Signal";
            break;
        case 3:
            str = "Good Signal";
            break;
        case 4:
            str = "Strong Signal";
            break;
        case 5:
            break;
        case 6:
            break;
    }
    return str;
};
util.formatStatusUser = function (val, row) {
    if (val === 'AKTIF') {
        return '<div style="background-color:#0f0;padding:2px;">AKTIF</div>';
    } else {
        return '<div style="background-color:#F00;padding:2px;">TIDAK AKTIF</div>';
    }

};
util.formatStatusGps = function (val, row) {
    if (parseInt(val, 10) === 1) {
        return '<div style="background-color:#0F0;color:#FFFFFF;padding:2px;">Aktif</div>';
    } else {
        return '<div style="background-color:#F00;color:#FFFFFF;padding:2px;">Tidak Aktif</div>';
    }

};
util.formatStatusIO = function (val, row) {
    if (row.acc == undefined)
        return;
    var table = '<table><tr>';
//        if (parseInt(row.acc, 10) == 1) {
//            table += '<td><img src="icon/16/on.png" alt="ACC ON"/></td>';
//        } else {
//            table += '<td><img src="icon/16/off.png" alt="ACC Off"/></td>';
//        }
    if (parseInt(row.fcut, 10) == 1) {
        table += '<td><img src="icon/16/cut.png"  alt="Cut Engine"/></td>';
    }
    if (parseInt(row.charge, 10) == 0) {
        table += '<td><img src="icon/16/unplugged.png"  alt="Power GPS Unplugged"/></td>';
    }
    if (parseInt(row.batt, 10) <= 5) {
        table += '<td><img src="icon/16/lowbatt.png"  alt="Low Batt"/></td>';
    }
    table += '</tr></table>';
    return table;
};
util.formatStatus = function (val, row) {
    var str = "";
    switch (val) {
        case "on":
            str = "<div style='background:#0F0'>ACC ON</div>";
            break;
        case "stop":
            str = "<div style='background:yellow'>ACC OFF</div>";
            break;
        case "off":
            str = "<div style='background:orange;'>OFFLINE</div>";
            break;
    }
    return str;
};
util.to_mysql_date = function (val) {
    return val;
};
util.create_info_gps = function (v) {
    var nopol = v.nopol;
    if (v.status == 'stop') {
        nopol = "<b class='gps_stop'>" + v.nopol + "</b>";
    } else if (v.status == 'on') {
        nopol = "<b class='gps_on'>" + v.nopol + "</b>";
    } else {
        nopol = "<b class='gps_off'>" + v.nopol + "</b>";
    }
    var html = "<table class='tbl_info'>" +
            "<tr><td class='w100'>" + lang.license + "</td><td class='w10'>:</td><td>" + nopol + "</td></tr>" +
            "<tr><td>" + lang.gps_date + "</td><td>:</td><td>" + v.tdate + ' (' + v.info + ")</td></tr>" +
            "<tr><td>" + lang.server_date + "</td><td>:</td><td>" + v.sdate + "</td></tr>" +
            "<tr><td>" + lang.speed + "</td><td>:</td><td>" + v.speed + " Km/Jam</td></tr>" +
            "<tr><td>" + lang.addr + "</td><td>:</td><td>" +v.poi + ',' + v.address + "</td></tr>" +
            "<tr><td>Status</td><td>:</td><td>ACC:" + util.formatAcc(v.acc) + ',Charge:' + util.formatCharge(v.charge) + " " + util.formatCutEngine(v.fcut) + "," + util.formatBatt(v.batt) + "</td></tr>";
    //  "<tr><td>Batt</td><td>:</td><td>" + myutil.formatBatt(v.batt) + "</td></tr>";
    if (parseInt(v.max_odo_oil, 10) > 0) {
        html += "<tr><td>" + lang.alarm_oil_active + "</td><td>:</td><td><b style='color:#F00;'>" + v.max_odo_oil.toLocaleString() + " Km</b></td></tr>";
    }
    html += "<tr><td>Odometer</td><td>:</td><td><b style='color:#F00;'>" + parseInt(v.odo, 10) + " Km</b></td></tr>";
    if (parseInt(v.max_park, 10) > 0) {
        html += "<tr><td>" + lang.alarm_park_active + "</td><td>:</td><td><b style='color:#F00;'> " + v.max_park + " Jam</b></td></tr>";
    }
    if (v.park != "" && v.park != "MOVE") {
        if (v.park != undefined) {
            html += "<tr><td>" + lang.park + "</td><td>:</td><td><b style='color:#F00;'> " + v.park + "</b></td></tr>";
        }
    }
    if (v.alarm > 0) {
        html += "<tr><td>" + lang.alarm + "</td><td>:</td><td><b style='color:#f00;'>" + util.formatAlarm(v.alarm) + "</b></td></tr>";
    }
    html += "<tr><td>" + lang.phone + "</td><td>:</td><td>" + v.phone + "/" + v.imei + "</td></tr>";
    html += "<tr><td>Driver</td><td>:</td><td>" + v.drv_name + "</td></tr>";
    "</table>";
    return html;
};
util.create_iw = function (v) {
    var nopol = v.nopol;
    if (v.status == 'stop') {
        nopol = "<b class='vhstop'>" + v.nopol + "</b>";
    } else if (v.status == 'on') {
        nopol = "<b class='vhon'>" + v.nopol + "</b>";
    } else {
        nopol = "<b class='vhoff'>" + v.nopol + "</b>";
    }
    var html = "<table>" +
            "<tr><td>Nopol</td><td>:</td><td style='width:200px;'><a href='#' onClick='select_gps(" + v.id + ");'>" + nopol + "</a></td></tr>" +
            "<tr><td>Tgl GPS</td><td>:</td><td>" + v.tdate + "</td></tr>" +
            "<tr><td>Tgl Server</td><td>:</td><td>" + v.sdate + "</td></tr>" +
            "<tr><td>Kecepatan</td><td>:</td><td>" + v.speed + " Km/Jam</td></tr>" +
            "<tr><td>Alamat</td><td>:</td><td>" + util.stringDivider(v.poi + ',' + v.address, 30, "<br/>\n") + "</td></tr>" +
            "<tr><td>Status</td><td>:</td><td>ACC:" + util.formatAcc(v.acc) + ',Charge:' + util.formatCharge(v.charge) + " " + util.formatCutEngine(v.fcut) + "</td></tr>";
    if (v.park != '') {
        html += "<tr><td>Parkir</td><td>:</td><td>" + v.park + "</td></tr>";
        if (v.tdate2 != undefined) {
            html += "<tr><td>From</td><td>:</td><td>" + v.tdate + ' S/D ' + v.tdate2 + "</td></tr>";
        }
    }
    if (v.alarm > 0) {
        html += "<tr><td>Alarm Info</td><td>:</td><td><b style='color:#f00;'>" + util.formatAlarm(v.alarm) + "</b></td></tr>";
    }
    "</table>";
    return html;
};
util.create_descr = function (v) {
    var nopol = v.nopol;
    if (v.status == 'stop') {
        nopol = "<div class='gps_stop'>" + v.nopol + "</div>";
    } else if (v.status == 'on') {
        nopol = "<div class='gps_on'>" + v.nopol + "</div>";
    } else {
        nopol = "<div class='gps_off'>" + v.nopol + "</div>";
    }
    var html = nopol +
            "<div class='clean'>Tgl GPS:" + v.tdate + "</div>" +
            "<div>Tgl Server:" + v.sdate + "</div>" +
            "<div>Kecepatan:" + v.speed + " Km/Jam</div>" +
            "<div>" + util.stringDivider(v.poi + ',' + v.address, 50, "<br/>\n") + "</div>" +
            "<div>" + util.formatAcc(v.acc) + ',Charge:' + util.formatCharge(v.charge) + " " + util.formatCutEngine(v.fcut) + "</div>" +
            "<div>Imei:" + v.imei + ',Phone:' + v.phone + "</div>";
    if (v.park != '') {
        html += "<div>Parkir:" + v.park + "</div>";
    }
    return html + "<br>";
};
util.create_info_alarm = function (v) {
    var nopol = v.nopol;
    if (v.status == 'stop') {
        nopol = "<b class='vhstop'>" + v.nopol + "</b>";
    } else if (v.status == 'on') {
        nopol = "<b class='vhon'>" + v.nopol + "</b>";
    } else {
        nopol = "<b class='vhoff'>" + v.nopol + "</b>";
    }
    var html = "<table>" +
            "<tr><td>Nopol</td><td>:</td><td style='width:200px;'><a href='#' onClick='select_gps(" + v.id + ");'>" + nopol + "</a></td></tr>" +
            "<tr><td>Tgl GPS</td><td>:</td><td>" + v.tdate + ' (' + v.info + ")</td></tr>" +
            "<tr><td>Tgl Server</td><td>:</td><td>" + v.sdate + "</td></tr>" +
            "<tr><td>Kecepatan</td><td>:</td><td>" + v.speed + " Km/Jam</td></tr>" +
            "<tr><td>Alamat</td><td>:</td><td>" + util.stringDivider(v.poi + ',' + v.address, 40, "<br/>\n") + "</td></tr>" +
            "<tr><td>Status</td><td>:</td><td>ACC:" + util.formatAcc(v.acc) + ',Charge:' + util.formatCharge(v.charge) + "</td></tr>" +
            "<tr><td>Batt</td><td>:</td><td>" + util.formatBatt(v.batt) + "</td></tr>";
    if (v.park != 'MOVE' && v.park != '' && v.park != null) {
        html += "<tr><td>Info Parkir</td><td>:</td><td><b style='color:#f00;'>" + v.park + "</b></td></tr>";
    }
    if (v.alarm > 0) {
        html += "<tr><td>Alarm Info</td><td>:</td><td><b style='color:#f00;'>" + util.formatAlarm(v.alarm) + "</b></td></tr>";
    }
    html += "<tr><td>Menu GPS</td><td>:</td><td><div class='linkmenu' onClick='util.showContextMenu(" + v.id + ");'>Control & Setting</div></td></tr>" +
            "</table>";
    return html;
};
util.create_infowindow = function (v) {
    var nopol = v.nopol;
    if (v.status == 'stop') {
        nopol = "<b class='vhstop'>" + v.nopol + "</b>";
    } else if (v.status == 'on') {
        nopol = "<b class='vhon'>" + v.nopol + "</b>";
    } else {
        nopol = "<b class='vhoff'>" + v.nopol + "</b>";
    }
    return "<div style='width:260px;'><table>" +
            "<tr><td style='width:100px;'>Nopol</td><td>:</td><td><a href='#' onClick='select_gps(" + v.id + ");'>" + nopol + "</a></td></tr>" +
            "<tr><td>Tgl GPS</td><td>:</td><td>" + v.tdate + ' (' + v.info + ")</td></tr>" +
            "<tr><td>Tgl Server</td><td>:</td><td>" + v.sdate + "</td></tr>" +
            "<tr><td>Kecepatan</td><td>:</td><td>" + v.speed + " Km/Jam</td></tr>" +
            "<tr><td>Alamat</td><td>:</td><td>" + util.stringDivider(v.poi + ',' + v.address, 40, "<br/>\n") + "</td></tr>" +
            "<tr><td>ACC,Charge</td><td>:</td><td>" + util.formatAcc(v.acc) + ',' + util.formatCharge(v.charge) + "</td></tr>" +
            "<tr><td>Batt</td><td>:</td><td>" + util.formatBatt(v.batt) + "</td></tr>" +
            "<tr><td>Driver</td><td>:</td><td>" + v.drv_name + "</td></tr>" +
            "</table></div>";
};
util.change_poi = function (icon) {
    var form = document.forms['formVehicle'];
    form.elements["icon"].value = icon;
    form.elements["icon_image"].src = "icon/objects/" + icon;
    $("#winListVehicleIcon").window('close');
};
util.show_form_password = function () {
    util.win = $("<div style='padding:5px;'></div>").dialog({
        title: 'Update Password',
        width: 310, height: 150,
        href: 'php_script/content_change_password.php',
        buttons: [
            {
                text: 'Cancel', iconCls: 'icon-cancel',
                handler: function () {
                    util.win.dialog('close');
                }
            }, {
                text: 'Save', iconCls: 'icon-save',
                handler: function () {
                    util.update_password();
                    util.win.dialog('close');
                }
            }
        ]
    });
    win.dialog('open').dialog('center');
};
util.show_form_profile = function () {

    util.win = $("<div style='padding:5px;'></div>").dialog({
        title: 'Update Profile',
        width: 310, height: 240,
        href: 'php_script/content_profile.php',
        buttons: [
            {
                text: 'Cancel', iconCls: 'icon-cancel',
                handler: function () {
                    util.win.dialog('close');
                }
            }, {
                text: 'Save', iconCls: 'icon-save',
                handler: function () {
                    util.update_profile();
                    util.win.dialog('close');
                }
            }
        ]
    });
    util.win.dialog('open').dialog('center');
};
util.show_form_upload_logo = function () {

    util.win = $("<div style='padding:5px;'></div>").dialog({
        title: 'Update Load',
        width: 340, height: 210,
        href: 'php_script/content_upload_logo.php',
        buttons: [
            {
                text: 'Cancel', iconCls: 'icon-cancel',
                handler: function () {
                    util.win.dialog('close');
                }
            }, {
                text: 'Save', iconCls: 'icon-save',
                handler: function () {
                    util.update_logo();
                    util.win.dialog('close');
                }
            }
        ]
    });
    util.win.dialog('open').dialog('center');
};
util.update_password = function () {
    var data = $("#formPassword").serialize();
    var pass1 = $("#password").val();
    var pass2 = $("#password2").val();
    if (pass1 != pass2) {
// alert("Password Tidak Sama...");
        $.messager.show({
            title: 'Error',
            msg: 'Password Tidak Sama...'
        });
        return;
    }
    if (pass1 == "" || pass2 == "") {
//  alert("Password Tidak Boleh Kosong...");
        $.messager.show({
            title: 'Error',
            msg: 'Password Tidak Boleh Kosong...'
        });
        return;
    }
////console.log(data);
    $.ajax({
        url: 'php_script/update_password.php',
        //dataType: 'json',
        type: 'post',
        data: data,
        success: function (r) {
            var result = eval('(' + r + ')');
            ////console.log(result);
            if (result.code == 'SUCCESS') {
                var win = $("#winEditPassword");
                win.dialog('close');
            }
            $.messager.show({
                title: result.code,
                msg: result.msg
            });
        }
    });
};
util.update_profile = function () {
    $.ajax({
        url: 'php_script/update_profile.php',
        //dataType: 'json',
        type: 'post',
        data: $("#formProfile").serialize(),
        success: function (r) {
            var result = eval('(' + r + ')');
            //console.log(result);
            if (result.code == 'SUCCESS') {
                var win = $("#winEditProfile");
                win.dialog('close');
            }
            $.messager.show({
                title: result.code,
                msg: result.msg
            });
        }
    });
};
util.update_logo = function () {
    //console.log('Update Logo');
    $("#form_logo").form('submit', {
        url: 'php_script/save_logo.php',
        success: function (result) {
            var result = eval('(' + result + ')');
            //console.log(result);
            if (result.code == 'SUCCESS') {
                util.win.dialog('close');
            }
            $.messager.show({
                title: result.code,
                msg: result.msg
            });
        }
    });
};
util.exit_web = function () {
    if (confirm('Exit Web') == true) {
        window.location.href = 'php_script/do_logout.php';
    }
};
util.show_playback = function (index, vh_id) {
    var v = map.vehicles[vh_id];
    var currDate = new Date();
    var prevDate = new Date();
    var from = '';
    var from_time = '';
    var to = '';
    var to_time = '';
    switch (index) {
        case 1: //1 Jam lalu
            prevDate.setHours(currDate.getHours() - 1);
            from = util.formatdate(prevDate);
            from_time = prevDate.getHours() + ':' + prevDate.getMinutes();
            to = util.formatdate(currDate);
            to_time = currDate.getHours() + ':' + currDate.getMinutes();
            break;
        case 2: //Hari Ini
            from = util.formatdate(prevDate);
            from_time = '00:00';
            to = util.formatdate(currDate);
            to_time = '23:59';
            break;
        case 3: //Kemarin
            prevDate.setDate(currDate.getDate() - 1);
            from = util.formatdate(prevDate);
            from_time = '00:00';
            to = util.formatdate(prevDate);
            to_time = '23:59';
            break;
        case 4: //2 Hari Lalu
            prevDate.setDate(currDate.getDate() - 2);
            from = util.formatdate(prevDate);
            from_time = '00:00';
            to = util.formatdate(prevDate);
            to_time = '23:59';
            break;
        case 5: //3 Hari Lalu
            prevDate.setDate(currDate.getDate() - 3);
            from = util.formatdate(prevDate);
            from_time = '00:00';
            to = util.formatdate(prevDate);
            to_time = '23:59';
            break;
    }
    var myWindow = window.open("playback.php?id=" + v.id + "&nopol=" + v.nopol + "&from=" + from + "&from_time=" + from_time + "&to=" + to + "&to_time=" + to_time, "Playback - " + v.nopol, "height=500,width=800,resizable=yes,status=yes");
};
//    myutil.show_icon_object = function () {
//        myutil.winIcon = $("<div style='padding:5px;'></div>").dialog({
//            title: 'Icon Object',
//            width: 310, height: 320,
//            href: 'php_script/load_icon_object.php',
//            buttons: [
//                {
//                    text: 'Close', iconCls: 'icon-exit',
//                    handler: function () {
//                        myutil.winIcon.dialog('close');
//                    }
//                }
//            ]
//        }).dialog('open');
//    };
util.change_icon_object = function (icon) {
    $("#formVehicle img[name=icon_image]").attr('src', 'icon/objects/' + icon);
    $("#formVehicle input[name=icon]").val(icon);
    if ((marker != undefined) && (marker != null)) {
        var param = 'icon/objects/' + icon;
        var iconObject = util.create_icon_poi(param);
        marker.setIcon(iconObject);
    }
};
util.change_icon_poi = function (icon) {
    // $("#formPoi input[id=icon_image]").attr('src', 'icon/poi/' + icon);
    $("#icon_image").attr('src', 'icon/poi/' + icon);
    $("#formPoi input[id=poi_icon]").val(icon);
    //var f = document.forms['formPoi'];
    //f.elements['icon_image'].src = 'icon/poi/' + icon;
    //f.elements['poi_icon'].value = icon;
    if (map.poi != undefined) {
        // var param = 'icon/poi/' + icon;
        var icn = util.create_icon_poi(icon);
        map.poi.poi.setIcon(icn);
    }
};
util.parsePointsFromRow = function (row) {
    if (row == null || row == undefined)
        return;
    var points = row.points.split(",");
    var path = [];
    for (var i in points)
    {
        var p = points[i];
        var latlngs = p.split(" ");
        path.push(new google.maps.LatLng(latlngs[1], latlngs[0]));
    }
    return path;
};
util.fitMapToGeofence = function (map, geofence) {
    var bounds = new google.maps.LatLngBounds();
    var points = geofence.getPath().getArray();
    for (var i in points) {
        var latlng = new google.maps.LatLng(points[i].lat(), points[i].lng());
        bounds.extend(latlng);
    }
    map.fitBounds(bounds);
};
util.createBoundFromGeofenc = function (geofence) {
    var bounds = new google.maps.LatLngBounds();
    var points = geofence.getPath().getArray();
    for (var i in points) {
        var latlng = new google.maps.LatLng(points[i].lat(), points[i].lng());
        bounds.extend(latlng);
    }
    return bounds;
};
util.createGeofence = function (map, latlngs) {
    return new google.maps.Polygon({paths: latlngs,
        editable: true, strokeColor: "#FF0000", strokeOpacity: 0.8, strokeWeight: 2, fillColor: "#FF0000",
        fillOpacity: 0.35, map: map
    });
};
util.createBounds = function (latlngs) {
    var bounds = new google.maps.LatLngBounds();
    for (var i in latlngs) {
        bounds.extend(latlngs);
    }
    return bounds;
};
util.create_icon_poi = function (image_url) {
    var image = {
        url: 'icon/poi/' + image_url,
        // This marker is 20 pixels wide by 32 pixels tall.
        size: new google.maps.Size(40, 40),
        anchor: new google.maps.Point(22, 22)
    };
    return image;
};
util.create_icon_alarm = function (id_alarm) {
    var image = {
        url: 'icon/alarm/' + util.getIconAlarm(id_alarm),
        // This marker is 20 pixels wide by 32 pixels tall.
        size: new google.maps.Size(32, 32),
        anchor: new google.maps.Point(22, 22)
    };
    return image;
};
util.create_icon_object = function (img_icon) {
    try {
        var image = {
            url: 'icon/objects/' + img_icon + '?v=0.3',
            size: new google.maps.Size(40, 40),
            anchor: new google.maps.Point(0, 32)
        };
        return image;
    } catch (ex) {
        var image = {
            url: 'icon/objects/1.png?v=0.2',
            size: new google.maps.Size(40, 40),
            anchor: new google.maps.Point(0, 32)
        };
        return image;
    }
};
util.create_icon_park = function (img_icon) {
    var image = {
        url: img_icon,
        size: new google.maps.Size(40, 40),
        anchor: new google.maps.Point(0, 32)
    };
    return image;
};
util.create_icon = function (angle, status)
{

    var image = {
        url: 'icon/gps/' + status + '/nav_0.png?v=0.2',
        size: new google.maps.Size(20, 41),
        anchor: new google.maps.Point(0, 32)
    };
    if ((angle >= 337.0) || ((angle >= 0.0) && (angle <= 22.0))) {
        image.url = 'icon/gps/' + status + '/nav_0.png?v=0.2';
        image.size = new google.maps.Size(20, 41);
        image.anchor = new google.maps.Point(10, 20);
    } else if ((angle >= 22.0) && (angle <= 67.0)) {
        image.url = 'icon/gps/' + status + '/nav_45.png?v=0.2';
        image.size = new google.maps.Size(41, 41);
        image.anchor = new google.maps.Point(20, 20);
    } else if ((angle >= 67.0) && (angle <= 112.0)) {
        image.url = 'icon/gps/' + status + '/nav_90.png?v=0.2';
        image.size = new google.maps.Size(41, 20);
        image.anchor = new google.maps.Point(20, 10);
    } else if ((angle >= 112.0) && (angle <= 157.0)) {
        image.url = 'icon/gps/' + status + '/nav_135.png?v=0.2';
        image.size = new google.maps.Size(41, 41);
        image.anchor = new google.maps.Point(20, 20);
    } else if ((angle >= 157.0) && (angle <= 202.0)) {
        image.url = 'icon/gps/' + status + '/nav_180.png?v=0.2';
        image.size = new google.maps.Size(20, 41);
        image.anchor = new google.maps.Point(10, 20);
    } else if ((angle >= 202.0) && (angle <= 247.0)) {
        image.url = 'icon/gps/' + status + '/nav_225.png?v=0.2';
        image.size = new google.maps.Size(41, 41);
        image.anchor = new google.maps.Point(20, 20);
    } else if ((angle >= 247.0) && (angle <= 292.0)) {
        image.url = 'icon/gps/' + status + '/nav_270.png?v=0.2';
        image.size = new google.maps.Size(41, 20);
        image.anchor = new google.maps.Point(20, 10);
    } else if ((angle >= 292.0) && (angle <= 337.0)) {
        image.url = 'icon/gps/' + status + '/nav_315.png?v=0.2';
        image.size = new google.maps.Size(41, 41);
        image.anchor = new google.maps.Point(20, 20);
    }
    return image;
};

util.decodeLine = function (_encoded) {
    var encoded = _encoded;
    var len = encoded.length;
    var index = 0;
    var array = [];
    var lat = 0;
    var lng = 0;

    while (index < len)
    {
        var b;
        var shift = 0;
        var result = 0;
        do {
            b = encoded.charCodeAt(index++) - 63;
            result |= (b & 0x1f) << shift;
            shift += 5;
        } while (b >= 0x20);
        var dlat = ((result & 1) ? ~(result >> 1) : (result >> 1));
        lat += dlat;

        shift = 0;
        result = 0;
        do {
            b = encoded.charCodeAt(index++) - 63;
            result |= (b & 0x1f) << shift;
            shift += 5;
        } while (b >= 0x20);
        var dlng = ((result & 1) ? ~(result >> 1) : (result >> 1));
        lng += dlng;
        array.push([lat * 1e-5, lng * 1e-5]);
    }
    return array;
};

util.createCookie = function (name, value, days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        var expires = "; expires=" + date.toGMTString();
    } else
        var expires = "";
    document.cookie = name + "=" + value + expires + "; path=/";
};

util.readCookie = function (name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ')
            c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0)
            return c.substring(nameEQ.length, c.length);
    }
    return null;
};

util.eraseCookie = function (name) {
    util.createCookie(name, "", -1);
};