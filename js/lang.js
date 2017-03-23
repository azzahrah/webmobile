var id_language = readCookie("id_language");
if (id_language == null || id_language == undefined) {
    id_language = 1;
}
function createCookie(name, value, days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        var expires = "; expires=" + date.toGMTString();
    } else
        var expires = "";
    document.cookie = name + "=" + value + expires + "; path=/";
}

function readCookie(name) {
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
}

function eraseCookie(name) {
    createCookie(name, "", -1);
}
function selected_language() {

    var id_language = $("#id_language").val();
    console.log(id_language);
    if (isNaN(parseInt(id_language, 10))) {
        id_language = 1;
    }
    var c = readCookie("id_language");
    console.log('readCookie 1:' + c);
    if (c != null) {
        eraseCookie("id_language");
    }
    createCookie("id_language", id_language, 10000);
    c = readCookie("id_language");
    console.log('readCookie 2:' + c);
}

var languages = [];

function Language() {
    var lang = this;
    lang.id = 'id'; //Indonesia
    lang.second = 'Detik';
    lang.minute = 'Menit';
    lang.hour = 'Jam';
    lang.day = 'Hari';
    lang.month = 'Bulan';
    lang.year = 'Tahun';
    lang.date = 'Tanggal';
    lang.gps_date = 'GPS Date';
    lang.server_date = 'Server Date';
    lang.alarm_oil_active = 'ALM Oil';
    lang.alarm_park_active = 'ALM Parkir';
    lang.price_bbm="Harga BBM";
    lang.dist_perliter="Harga/Liter";    
    lang.dist = 'Jarak';
    lang.user = 'Nama User';
    lang.license = 'Nopol';
    lang.addr = 'Alamat';
    lang.park = 'Parkir';
    lang.alarm = 'Alarm';
    lang.poi = 'POI';
    lang.geofence = 'Geofence';
    lang.speed = 'Kecepatan';
    lang.imei = 'Imei';
    lang.phone = 'Phone';
    lang.lat = 'Latitude';
    lang.lng = 'Longitude';
    lang.alt = 'Altitude';
    lang.direction = 'Arah';
    lang.acc = 'ACC';
    lang.charger = 'Charge';
    lang.batt = 'Battery';
    lang.fcut = 'Cut Enggine';
    lang.route = 'Rute';
    lang.driver = 'Supir';
    lang.menu_cutengine = 'Cut Engine';
    lang.menu_resumeengine = 'Resume Engine';
    lang.menu_gps_management = 'Pengaturan GPS';
    lang.menu_user_management = 'Pengaturan User';
    lang.menu_driver_management = 'Pengaturan Driver';
    lang.menu_gps_map = 'Map';
    lang.menu_report = 'Laporan GPS';
    lang.menu_logo = 'Update Logo';
    lang.menu_service = 'Data Jadwal Service';
    lang.menu_alarm = 'Manajemen Alarm';
    lang.menu_change_password = 'Ganti Password';
    lang.menu_show_history = 'Tampilkan Riwayat';
    lang.menu_send_command = 'Kirim Perintah';
    lang.menu_map = 'Map';
    lang.search = 'Cari';
    lang.excel = 'Excel';
    lang.play = 'Play';
    lang.pause = 'Pause';
    lang.stop = 'Stop';
    lang.trip = 'Trip';
    lang.from_date = 'Dari';
    lang.to_date = 'S/D';
    lang.avg_speed = 'Kecepatan Rata-rata';
    lang.dist = 'Jarak Tempuh';
    lang.driver = 'Driver';
    lang.list_vehicle = 'Daftar kendaraan';
    lang.download_title = 'Download...';
    lang.download_msg = 'Sedang Download';
    lang.report_summary = 'Ringkasan Laporan';
    lang.report_trip = 'Trip';
    lang.report_park = 'Park';
    lang.report_hour = 'Hour';
    lang.report_alarm = 'Alarm';
    lang.report_poi = 'POI';
    lang.report_poi_sum = 'POI Summary';
    lang.report_geofence = 'Geofence';
    lang.report_geofence_sum = 'Geofence Summary';
    lang.grid_user = 'User';
    lang.grid_license = 'Nopo';
    lang.grid_date = 'Tanggal';
    lang.grid_speed = 'Speed';
    lang.grid_direction = 'Arah';
    lang.grid_distance = 'Jarak Tempuh';
    lang.grid_addr = 'Alamat';
    lang.grid_lat = 'Latitude';
    lang.grid_lng = 'Longitude';
    lang.grid_poi = 'POI';
    lang.grid_geofence = 'Geofence';
    lang.grid_park = 'Parkir';
    lang.grid_acc = 'ACC';
    lang.grid_fcut = 'Cut Engine';
    lang.grid_charger = 'Charger';
    lang.grid_alarm = 'Alarm';
    lang.grid_descr = 'Keterangan';
    lang.grid_icon = 'Icon';
    lang.grid_icon_map = 'Icon Map';
    lang.grid_gps_brand = 'Merk GPS';
    lang.grid_install_date = 'Tanggal Install';
    lang.grid_vehicle_brand = 'Merk Kendaraan';
    lang.grid_phone = 'Phone';
    lang.grid_imei = 'Imei';
    lang.grid_driver = 'Driver';
    lang.grid_driver_phone = 'Phone Driver';
    lang.opt_panah = 'Panah';
    lang.opt_image = 'Gambar';
    lang.tab_summary = 'Info GPS';
    lang.tab_alarm = 'Info Alarm';
    lang.tab_command = 'Status Perintah';
    lang.tab_unit = 'Unit';
    lang.tab_poi = 'POI';
    lang.tab_geofence = 'Geofence';
    lang.tab_route = 'Rute';
    lang.sb_prompt = 'Ketik untuk mencari';
    lang.data_empty = 'Data Kosong';
    lang.warn_select_gps = 'Pilih GPS';
    lang.wait_download = 'Silahkan Ditunggu..';
    lang.user_name = 'Nama User';
    lang.user_address = 'Alamat';
    lang.user_city = 'Kota';
    lang.user_hp = 'Nomor HP';
    lang.user_hp2 = 'Nomor HP2';
}
var eng = new Language();
{
    eng.addr = 'Address';
    eng.date = 'Date';
    eng.day = 'Day';
    eng.direction = 'Direction';
    eng.dist = 'Distance';
    eng.grid_addr = 'Address';
    eng.grid_date = 'Date';
    eng.grid_direction = 'Direction';
    eng.grid_distance = 'Distance (Km)';
    eng.grid_alarm = 'Alarm';
    eng.grid_park = 'Parking';
    eng.grid_gps_brand = 'GPS Brand';
    eng.grid_vehicle_brand = 'Vehicle Brand';
    eng.grid_driver = 'Driver';
    eng.grid_driver_phone = 'Phone Driver';
    eng.hour = 'Hour';
    eng.month = 'Month';
    eng.year = 'Year';
    eng.minute = 'Minute';
    eng.trip = 'Trip';
    eng.tab_summary = 'GPS Summary';
    eng.tab_alarm = 'GPS Alarm';
    eng.tab_command = 'Command Status';
    eng.tab_unit = 'Unit';
    eng.tab_poi = 'POI';
    eng.tab_geofence = 'Geofence';
    eng.tab_route = 'Route';
    eng.sb_prompt = 'Input for Search';
    eng.opt_panah = 'Arrow';
    eng.opt_image = 'Image';
    eng.avg_speed = 'AVG Speed';
    eng.dist = 'Distance';
    eng.list_vehicle = 'List Vehicle';
    eng.download_title = 'Download...';
    eng.download_msg = 'Downlod Progress...';
    eng.menu_report = 'GPS Report';
    eng.menu_cutengine = 'Cut Engine';
    eng.menu_resumeengine = 'Resume Engine';
    eng.menu_gps_management = 'GPS Management';
    eng.menu_user_management = 'User Management';
    eng.menu_driver_management = 'Driver Management';
    eng.menu_gps_map = 'Map';
    eng.menu_show_history = 'Show History';
    eng.menu_send_command = 'Send Command';
    eng.license = "License Number";
    eng.speed = "Speed";
    eng.data_empty = 'Data Empty';
}
languages.push(new Language());
languages.push(eng);
id_language=0;
var lang = languages[id_language];