var server = "geagps";
var websocket_server = "";
init_vars = function () {
    //Generate Session ID
    var d = new Date().getTime();
    app.session = 'xxxx'.replace(/[xy]/g, function (c) {
        var r = (d + Math.random() * 16) % 16 | 0;
        d = Math.floor(d / 16);
        return (c === 'x' ? r : (r & 0x7 | 0x8)).toString(16);
    });

    //default load vehicle url
    app.vehicle.load_url = 'scripts/load_vehicle.php';

    switch (server) {
        case "geagps":
            app.vehicle.load_url = 'scripts/load_vehicle_multi.php';
            websocket_server = "ws://geagps.com:9020/websocket";
            break;
        case "shvtracker":
            websocket_server = "ws://shvtracker.net:7070/websocket";
            break;
        case "pusatgps":
            websocket_server = "ws://servergpstracker.com.net:7070/websocket";
            break;
    }
};

