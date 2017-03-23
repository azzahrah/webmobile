var CONST = {
    GPS: 'gps',
    CUT_ENGINE: "cutengine",
    RESUME_ENGINE: "resumeengine",
    GET_LAST_POSITION: "position",
    OVER_SPEED: "overspeed",
    ENABLE_ALARM_PARK: "enable_alarm_park"
};
var BASE_PATH = '';
var PATH_ICON = '';
var PATH_IMAGE = '';
var vehicle = {
    data: [],
    data2: [],
    table: null,
    rows: [],
    popup: null,
    form: null,
    window: null,
    views: null,
    lbl_gps_all: null,
    lbl_gps_on: null,
    lbl_gps_off: null,
    num_gps_all: 0,
    num_gps_on: 0,
    num_gps_off: 0,
    progressbar: null,
    progressForm: null,
    load_url: 'scripts/load_vehicle.php',
    save_url: 'scripts/save_vehicle.php'
};
var user = {
    data: [],
    table: null,
    form: null,
    window: null,
    views: null,
    combo: null,
    lbl_total: null,
    progressbar: null,
    progressForm: null,
    load_url: 'scripts/load_user.php',
    save_url: 'scripts/save_user.php'
};
var playback = {
    index: 0,
    paused: false,
    speed: 1000,
    vh_id: 0,
    marker: null,
    markers: [],
    tracks: [],
    polyline: null,
    combo_vehicle: null,
    control_playback: null,
    timer: 0,
    download_url: BASE_PATH + 'scripts/report/report_trip.php'
};
var tabbar = {
    id: "main_tabbar",
    view: "tabbar", type: "bottom", multiview: true, options: [
        {value: "<span class='webix_icon fa-car'></span><span style='padding-left: 4px'>GPS</span>", id: 'views_vehicle'},
        {value: "<span class='webix_icon fa-map-marker'></span><span style='padding-left: 4px'>Map</span>", id: 'views_map'},
        {value: "<span class='webix_icon fa-user'></span><span style='padding-left: 1px'>User</span>", id: 'views_user'}
    ], height: 50
};
var views_map = {
    id: "views_map",
    view: "multiview",
    cells: [
        {
            id: "view_map",
            rows: [
                {
                    view: "toolbar", elements: [
                        {
                            id: "segmented_playback", view: "segmented", type: "form", value: "Stop", width: 200, hidden: true,
                            options: [{id: "btn_play", value: "Play"}, {id: "btn_pause", value: "Pause"}, {id: "btn_stop", value: "Stop"}, {id: "btn_close", value: "Close"}],
                            on: {"onAfterTabClick": function (id) {
                                    switch (id) {
                                        case "btn_play":
                                            app.playback.play();
                                            break;
                                        case "btn_stop":
                                            app.playback.stop();
                                            break;
                                        case "btn_pause":
                                            app.playback.pause();
                                            break;
                                        case "btn_close":
                                            app.playback.stop();
                                            app.playback.control_playback.hide();
                                            $$("btn_playback").show();
                                            $$("btn_clear_playback").show();
                                            break;
                                    }
                                }
                            }
                        },
                        {},
                        {id: "btn_playback", view: "button", type: "form", value: "Playback", width: 80, click: function () {
                                app.views_map.setValue("view_playback");
                            }
                        },
                        {id: "btn_clear_playback", view: "button", type: "form", value: "Clear", width: 80, click: function () {
                                app.playback.clear();
                            }
                        }
                    ]
                },
                {
                    id: "map_object",
                    view: "google-map",
                    key: "AIzaSyCobdKM8bzMG6EcmwE0ZSpz68z6JEgLGMY",
                    zoom: 6,
                    center: [-7.373783, 112.4747848],
                    ready: function () {
                        console.log("Google Map Ready");
                    }
                }
                , {gravity: 0.000001}
            ]
        },
        {
            id: "view_playback",
            rows: [
                {
                    id: "form_playback",
                    view: "form", elements: [
                        {id: "cbo_vh_playback", name: "vh_id", view: "combo", label: "Nopol", options: {}},
                        {view: "datepicker", name: "from_date", format: "%Y-%m-%d %H:%i:%s", timepicker: true, stringResult: true, label: "From"},
                        {view: "datepicker", name: "to_date", format: "%Y-%m-%d %H:%i:%s", timepicker: true, stringResult: true, label: "To"},
                        {
                            cols: [
                                {id: "btn_play_onmap", view: "button", type: "form", value: "Play", hidden: true, click: function () {
                                        //app.views_map.setValue("view_map");
                                        $$("views_map").setValue("view_map");
                                        $$("btn_playback").hide();
                                        $$("btn_clear_playback").hide();
                                        app.playback.play();
                                    }
                                },
                                {view: "button", type: "form", value: "Search", click: function () {
                                        app.playback.download();
                                    }
                                },
                                {view: "button", type: "danger", value: "Cancel", click: function () {
                                        // app.views_map.setValue("view_map");
                                        $$("views_map").setValue("view_map");
                                    }
                                }
                            ]
                        }
                    ]
                }, {
                    id: "tbl_result_playback",
                    view: "datatable", select: true,
                    columns: [
                        {id: "id", header: "#", width: 40, hidden: true},
                        {id: "status", header: "Status", width: 100, template: function (obj) {
                                if (obj.status === 'stop') {
                                    return "<span class='gps_stop'>STOP</span>";
                                } else if (obj.status === 'on') {
                                    return "<span class='gps_on'>ACC ON</span>";
                                }
                                return "<span class='gps_off'>OFF</span>";
                            }
                        },
                        {id: "tdate", header: "Date", width: 150},
                        {id: "speed", header: "Speed", width: 120},
                        {id: "park", header: "Park", width: 130},
                        {id: "address", header: "Address", width: 320}
                    ],
                    pager: "pager_trip"
                },
                {
                    cols: [
                        {paddingY: -5, id: "pager_trip", view: "pager", size: 10, group: 5},
                        //{view: "button", id: "trip_to_excel", type: "form", value: "Save To Excel", width: 120},
                        {width: 10},
                        {view: "combo", width: 70, options: ["10", "30", "50", "100", "500", "1000"],
                            on: {"onChange": function (newv, oldv) {
                                    //console.log(this.getValue());
                                    $$("tbl_result_playback").getPager().config.size = this.getValue() * 1;
                                    $$("tbl_result_playback").refresh();
                                }}
                        }]
                },
                {gravity: 0.000001}
            ]
        }, {gravity: 0.000001}]
};
var views_control = {
    id: "views_control",
    view: "multiview",
    cells: [
        {
            id: "view_control_toolbar",
            rows: [
                {
                    view: "toolbar", elements: [
                        {view: "label", label: "Current Nopol", type: "form"},
                        {view: "button", type: "danger", value: "Cut", width: 70},
                        {view: "button", type: "form", value: "Resume", width: 70},
                        {view: "button", type: "form", value: "Playback", width: 80}
                    ]
                },
                {
                }
                , {gravity: 0.000001}
            ]
        },
        {
            id: "view_control",
            rows: [
                {
                    view: "toolbar", elements: [
                        {view: "button", type: "form", value: "<<Back", width: 70},
                        {},
                        {view: "button", type: "form", value: "Playback", width: 80},
                        {view: "button", type: "danger", value: "Cancel", width: 70}
                    ]
                },
                {
                    view: "form", elements: [
                        {view: "combo", label: "Nopol", suggest: "scripts/combo_vehicle.php"},
                        {view: "datepicker", format: "%Y-%m-%d %H:%i:%s", timepicker: true, stringResult: true, label: "From"},
                        {view: "datepicker", format: "%Y-%m-%d %H:%i:%s", timepicker: true, stringResult: true, label: "To"},
                        {
                            cols: [
                                {view: "button", type: "form", value: "Search"},
                                {view: "button", type: "danger", value: "Cancel"}
                            ]
                        }
                    ]
                }, {gravity: 0.000001}
            ]
        }, {gravity: 0.000001}]
};
formatStatus = function (val, row) {
    if (row.status === "stop") {
        return {"text-color": "#2B65EC", "color": "#2B65EC"};
    } else if (row.status === "on") {
        return {"text-color": "#3EA055", "color": "#3EA055"};
    }
    return {"text-color": "red", "color": "red"};
};
var menu_vehicle = {
    view: "menu",
    data: [
        {id: "1", value: "Control", submenu: ["Add", "Edit", "Delete", "Refresh", "Cut Engine", "Resume Engine"]}
    ],
    css: "blue"
};
var views_vehicle = {
    id: "views_vehicle",
    view: "multiview",
    cells: [{
            id: "view_list_vehicle",
            rows: [
                {
                    view: "toolbar", elements: [
                        {id: "lbl_gps_all", view: "label", label: "Total 0"},
                        {id: "lbl_gps_on", view: "label", label: "On 0"},
                        {id: "lbl_gps_off", view: "label", label: "Off 0"},
                        {id: "btn_vehicle_control", view: "button", value: "Control", width: 70, popup: "popup_vehicle"}
                    ]
                }, {
                    view: "scrollview",
                    body: {
                        id: "tbl_vehicle",
                        view: "datatable",
                        subrow: "#details#",
                        subRowHeight: "auto",
                        autoheight: true,
                        autowidth: false,
                        select: true,
                        columns: [
                            {id: "id", header: "#", width: 60, hidden: true},
                            {id: "nopol", header: [{content: "textFilter", placeholder: "Search Nopol"}], cssFormat: formatStatus, template: "{common.subrow()} #nopol#", width: 160},
                            {id: "tdate", header: "Date", format: "%Y-%m-%d %H:%i:%s", fillspace: true}
                        ]
                    }
                }, {gravity: 0.00001}
            ]
        }, {
            id: "view_edit_vehicle",
            batch: "view_edit_vehicle",
            rows: [
                {view: "toolbar", elements: [
                        {id: "btn_vehicle_back", view: "button", value: "<< Back", align: 'left', width: 60},
                        {},
                        {id: "btn_vehicle_save", view: "button", type: "form", value: "Save", align: 'right', width: 60},
                        {id: "btn_vehicle_cancel", view: "button", type: "danger", value: "Cancel", align: 'right', width: 60}
                    ]
                }, {
                    id: "form_vehicle",
                    view: "form",
                    elements: [
                        {name: "nopol", view: "text", label: "Nopol", value: "", labelWidth: 100},
                        {name: "imei", view: "text", label: "Imei", value: "", labelWidth: 100},
                        {name: "phone", view: "text", label: "Phone", value: "", labelWidth: 100},
                        {name: "drv_name", view: "text", label: "Driver", labelWidth: 100},
                        {name: "drv_phone", view: "text", label: "Phone Driver", labelWidth: 100},
                        {name: "install_date", view: "datepicker", stringResult: true, format: "%Y-%m-%d", label: "Install Date", labelWidth: 100},
                        {name: "gps_brand", view: "combo", label: "Model", suggest: "scripts/combo_gps_brand.php", labelWidth: 100},
                        {name: "user_id", view: "combo", label: "User", suggest: "scripts/combo_user.php", labelWidth: 100},
                        {name: "timezone", view: "combo", label: "GMT", suggest: "scripts/combo_timezone.php", labelWidth: 100}

                    ]
                },
                {gravity: 0.0000001}
            ]
        }, {
            id: "view_control_vehicle",
            batch: "view_control_vehicle",
            rows: [{view: "toolbar", elements: [
                        {id: "btn_control_back", view: "button", value: "<< Back", align: 'left', width: 60, click: function () {
                                app.vehicle.views.setValue("view_list_vehicle");
                            }
                        },
                        {}

                    ]
                }, {
                    id: "form_control",
                    view: "form",
                    elements: [
                        {name: "nopol", view: "text", label: "Nopol", value: "", labelWidth: 100},
                        {name: "imei", view: "text", label: "Imei", value: "", labelWidth: 100},
                        {name: "phone", view: "text", label: "Phone", value: "", labelWidth: 100},
                        {cols: [
                                {view: "button", type: "danger", value: "Cut Engine", align: 'right'},
                                {view: "button", type: "form", value: "Resume Engin", align: 'right', click: function () {
                                        app.vehicle.views.setValue("view_list_vehicle");
                                    }
                                }
                            ]
                        },
                        {cols: [
                                {view: "button", type: "form", value: "Position", align: 'right'},
                                {view: "button", type: "danger", value: "Active Siren", align: 'right', click: function () {
                                        app.vehicle.views.setValue("view_list_vehicle");
                                    }
                                }
                            ]
                        },
                        {cols: [
                                {view: "button", type: "danger", value: "Lock Door", align: 'right'},
                                {view: "button", type: "form", value: "Open Door", align: 'right', click: function () {
                                        app.vehicle.views.setValue("view_list_vehicle");
                                    }
                                }
                            ]
                        }
                    ]
                },
                {gravity: 0.0000001}
            ]
        }
    ]
};
var views_user = {
    id: "views_user",
    view: "multiview",
    cells: [{
            id: "view_list_user",
            rows: [
                {view: "toolbar", elements: [
                        {id: "total_user", view: "label", value: "User:0"},
                        {id: "btn_user_add", view: "button", value: "Add"},
                        {id: "btn_user_edit", view: "button", value: "Edit"},
                        {id: "btn_user_delete", view: "button", type: "danger", value: "Delete"},
                        {id: "btn_user_refresh", view: "button", value: "Refresh"}
                    ]
                }, {
                    id: "tbl_user",
                    view: "datatable", select: true,
                    columns: [
                        {id: "id", header: "id", hidden: true},
                        {id: "status", header: "status", width: 90, template: function (obj) {
                                if (parseInt(obj.state, 10) == 1) {
                                    return "<span class='gps_on'>AKTIF</span>";
                                }
                                return "<span class='gps_off'>NOT AKTIF</span>";
                            }
                        },
                        {id: "real_name", header: [{content: "textFilter", placeholder: "Real Name"}], width: 150},
                        {id: "login", header: "Login", width: 130},
                        {id: "expired_date", header: "Expired Date", width: 120},
                        {id: "level", header: "Level", width: 300}
                    ]
                }
            ]
        }, {
            id: "view_edit_user",
            rows: [
                {view: "toolbar", elements: [
                        {id: "btn_user_back", view: "button", value: "<< Back", align: 'left', width: 60},
                        {},
                        {id: "btn_user_save", view: "button", type: "form", value: "Save", align: 'right', width: 60},
                        {id: "btn_user_cancel", view: "button", type: "danger", value: "Cancel", align: 'right', width: 60}
                    ]
                }, {
                    id: "form_user",
                    view: "form",
                    elements: [
                        {name: "state", view: "segmented", label: "Status", options: [{id: 1, value: "Aktif"}, {id: 0, value: "Not Aktif"}], labelWidth: 120},
                        {name: "real_name", view: "text", label: "Nama Lengkap", labelWidth: 120},
                        {name: "login", view: "text", label: "Login", labelWidth: 120},
                        {name: "passwordx", view: "text", type: "password", label: "Password", placeholder: "Ubah atau Biarkan Kosong", labelWidth: 120},
                        {name: "phone", view: "text", label: "Phone", labelWidth: 120},
                        {"view": "combo", "name": "level_id", label: "User Level", "labelWidth": 120, suggest: "scripts/combo_user_level.php",
                            on: {
                                onChange: function (newVal, oldVal) {
                                    if (newVal == "1" || newVal == "4") {
                                        $$("user_access").show();
                                    } else {
                                        $$("user_access").hide();
                                    }
                                }
                            }},
                        {"view": "multiselect", id: "user_access", "name": "user_access", label: "Hak Akses", "labelWidth": 120, suggest: "scripts/combo_user_access.php", hidden: true},
                        {name: "expired_date", view: "datepicker", stringResult: true, format: "%Y-%m-%d", label: "Expired Date", labelWidth: 120}

                    ]
                },
                {gravity: 0.0000001}
            ]
        }
    ]
};
var uiapp = {
    id: "main",
    rows: [
        {
            view: "toolbar",
            elements: [
                {id: "combo_user", view: "combo", placeholder: "Select User", options: {}},
                {id: "btn_logout", view: "button", type: "danger", value: "Logout", width: 100, click: function () {
                        webix.confirm("Logout?", function (result) {
                            if (result) {
                                window.location.href = 'scripts/do_logout.php';
                            }
                        });
                    }
                }

            ]
        },
        {
            id: "views_main",
            cells: [
                views_vehicle,
                views_map,
                views_user
            ]
        },
        tabbar,
        {gravity: 0.000001}
    ]
};

var app = {
    name: "mobile",
    views: null,
    views_map: null,
    user_id: 0,
    vh_id: 0,
    iw: null,
    map: null,
    marker: null,
    markers: [],
    num_new_data: 0,
    polyline: null,
    hReconnect: null,
    vehicle: vehicle,
    user: user,
    playback: playback,
    progress: null,
    gps_off: 0,
    gps_on: 0,
    debug: false, //comment for production
    init: function () {
        webix.ui(uiapp);
        //inside vars.js
        init_vars();
        app.init_window();
        app.init_selector();
        app.init_events();
//        //$$("view_list_vehicle")
        webix.extend(app.progress, webix.ProgressBar);
//
////        webix.extend(app.vehicle.progressbar, webix.ProgressBar);
////        webix.extend(app.vehicle.progressForm, webix.ProgressBar);
////
////        webix.extend(app.user.progressbar, webix.ProgressBar);
////        webix.extend(app.user.progressForm, webix.ProgressBar);
////
////        webix.extend(app.playback.progressbar, webix.ProgressBar);
////        webix.extend(app.playback.progressForm, webix.ProgressBar);
//
//
        app.getMapObject();
        app.trigger("initData");
        app.loop();
    },
    trigger: function (name) {
        app.callEvent(name, arguments);
    },
    on: function (name, code) {
        app.attachEvent(name, code);
    }
};
webix.extend(app, webix.EventSystem);
app.on("initData", function () {
    app.user.load();
});
var timerLoop = 0;
app.loop = function () {
    if (timerLoop)
        clearTimeout(timerLoop);
    if (app.num_new_data > 0) {
        app.num_new_data = 0;
        app.vehicle.table.refresh();
    }
    app.vehicle.lbl_gps_on.setValue("On:" + app.vehicle.num_gps_on);
    app.vehicle.lbl_gps_off.setValue("Off:" + app.vehicle.num_gps_off);
    timerLoop = setTimeout('app.loop();', 10000);
};
app.init_window = function () {
    webix.ui({
        view: "popup",
        id: "popup_vehicle",
        body: {
            rows: [
                {id: "btn_vehicle_add", view: "button", value: "Add"},
                {id: "btn_vehicle_edit", view: "button", value: "Edit"},
                {id: "btn_vehicle_delete", view: "button", value: "Delete"},
                {id: "btn_vehicle_refresh", view: "button", value: "Refresh"},
                {id: "btn_vehicle_cut", view: "button", value: "Cut Engine"},
                {id: "btn_vehicle_resume", view: "button", value: "Resume Engine"}
            ]
        }
    })
};
app.init_selector = function () {
    app.progress = $$("main");
    app.views = $$("views_main");
    app.vehicle.popup = $$("popup_vehicle");
    app.vehicle.table = $$("tbl_vehicle");
    app.vehicle.form = $$("form_vehicle");
    app.vehicle.views = $$("views_vehicle");
    app.vehicle.lbl_gps_all = $$("lbl_gps_all");
    app.vehicle.lbl_gps_on = $$("lbl_gps_on");
    app.vehicle.lbl_gps_off = $$("lbl_gps_off");
    //app.vehicle.progressbar = $$("views_vehicle");// $$("view_list_vehicle");
    //app.vehicle.progressForm = $$("form_vehicle");

    app.user.table = $$("tbl_user");
    app.user.form = $$("form_user");
    app.user.views = $$("views_user");
    app.user.lbl_total = $$("total_user");
    //app.user.progressbar = $$("views_user");
    //app.user.progressForm = $$("form_user");

    app.user.combo = $$("combo_user");
    app.views_map = $$("views_map");
    app.playback.table = $$("tbl_result_playback");
    app.playback.form = $$("form_playback");
    app.playback.combo_vehicle = $$("cbo_vh_playback");
    app.playback.control_playback = $$("segmented_playback");
};
app.init_events = function () {
    //Form Vehicle 
    app.user.combo.attachEvent("onChange", function (newv, oldv) {
        console.log('change_user:' + newv);
        app.change_user(newv);
    });
    $$("btn_vehicle_add").attachEvent("onItemClick", function () {
        app.vehicle.popup.hide();
        app.vehicle.add();
    });
    $$("btn_vehicle_edit").attachEvent("onItemClick", function () {
        app.vehicle.popup.hide();
        app.vehicle.edit();
    });
    $$("btn_vehicle_delete").attachEvent("onItemClick", function () {
        app.vehicle.popup.hide();
        app.vehicle.delete();
    });
    $$("btn_vehicle_refresh").attachEvent("onItemClick", function () {
        app.vehicle.popup.hide();
        app.vehicle.load();
    });
    $$("btn_vehicle_cut").attachEvent("onItemClick", function () {
        app.vehicle.popup.hide();
        app.vehicle.cut();
    });
    $$("btn_vehicle_resume").attachEvent("onItemClick", function () {
        app.vehicle.popup.hide();
        app.vehicle.resume();
    });
    $$("btn_vehicle_save").attachEvent("onItemClick", function () {
        console.log("btn_vehicle_save");
        app.vehicle.save();
    });
    $$("btn_vehicle_cancel").attachEvent("onItemClick", function () {
        app.vehicle.views.setValue("view_list_vehicle");
    });
    $$("btn_vehicle_back").attachEvent("onItemClick", function () {
        app.vehicle.views.setValue("view_list_vehicle");
    });
    app.vehicle.table.attachEvent("onItemDblClick", function (id, e) {
        console.log("onItemDblClick");
        //app.views.setValue("views_map");
        $$("main_tabbar").setValue("views_map");
        try {
            var v = this.getSelectedItem();
            var latLng = new google.maps.LatLng(parseFloat(v.lat), parseFloat(v.lng));
            app.map.setCenter(latLng);
            app.map.setZoom(16);
            app.marker = app.markers[v.id];
            if (app.marker == null || app.marker == undefined)
                return;
            app.vh_id = v.id;
            google.maps.event.trigger(app.marker, 'click');
        } catch (e) {
            console.log(e);
        }
    });
    app.vehicle.table.attachEvent("onBeforeLoad", function () {
        console.log("onBeforeLoad");
        //app.progress.showProgress();
    });
    app.vehicle.table.attachEvent("onAfterLoad", function () {
        console.log("onAfterLoad");
    });
    /* User */
    $$("btn_user_add").attachEvent("onItemClick", function () {
        app.user.form.setValues({mode: "add"});
        app.user.views.setValue("view_edit_user");
    });
    $$("btn_user_edit").attachEvent("onItemClick", function () {
        var row = app.user.table.getSelectedItem();
        if (row == null || row == undefined) {
            webix.message({type: "error", text: "Pilih User"});
            return;
        }
        row['mode'] = 'edit';
        app.user.form.setValues(row);
        app.user.views.setValue("view_edit_user");
    });
    $$("btn_user_refresh").attachEvent("onItemClick", function () {
        console.log("btn_user_save");
        app.user.load();
    });
    $$("btn_user_delete").attachEvent("onItemClick", function () {
        var row = app.user.table.getSelectedItem();
        if (row == null || row == undefined) {
            webix.message({type: "error", text: "Pilih User"});
            return;
        }
        row['mode'] = 'delete';
        webix.confirm("Delete User?", function (result) {
            if (result) {
                webix.ajax().post(app.user.save_url, row, function (text, xml, xhr) {
                    var json = xml.json();
                    webix.message({type: "error", text: json.msg});
                    if (json.code == 'SUCCESS') {
                        app.user.load();
                    }
                });
            }
        });
    });
    $$("btn_user_save").attachEvent("onItemClick", function () {
        console.log("btn_user_save");
        app.user.save();
    });
    $$("btn_user_cancel").attachEvent("onItemClick", function () {
        app.user.views.setValue("view_list_user");
    });
    $$("btn_user_back").attachEvent("onItemClick", function () {
        app.user.views.setValue("view_list_user");
    });
};
app.log = function (msg) {
    //console.log(msg)
};
var hndGetMap;
app.getMapObject = function () {
    if (hndGetMap)
        clearTimeout(hndGetMap);
    try {
        app.map = $$("map_object").map;
    } catch (e) {
        console.log(e);
    }
    if (app.map == null || app.map == undefined) {
        hndGetMap = setTimeout('app.getMapObject()', 1000);
    }

};
app.clear_marker = function () {
    for (var i in app.markers) {
        if (app.markers[i]) {
            app.markers[i].setMap(null);
        }
    }
};
app.change_user = function (user_id) {
    app.user_id = user_id;
    app.clear_map();
    app.vehicle.load();
    app.init_websocket();
};
app.clear_map = function () {
    if (app.marker) {
        app.marker.setMap(null);
    }
    if (app.polyline) {
        app.polyline.setMap(null);
    }
    if (app.markers) {
        for (var i in app.markers) {
            if (app.markers[i]) {
                app.markers[i].setMap(null);
            }
        }
        app.markers = [];
        app.markers.length = 0;
    }
};
app.polyline_init = function () {
    if (app.polyline != undefined) {
        app.polyline.setMap(null);
    }
    app.polyline = new google.maps.Polyline({
        geodesic: true,
        strokeColor: '#00FF00',
        strokeOpacity: 10.0,
        strokeWeight: 3
    });
    app.polyline.setMap(app.map);
};
var hndDrawMarker;
app.draw_markers = function () {
    //console.log('app.draw_markers');
    if (hndDrawMarker) {
        clearTimeout(hndDrawMarker);
    }
    //console.log(app.map);
    if (app.map == null || app.map == undefined) {
        hndDrawMarker = setTimeout('app.draw_markers()', 1000);
        return;
    }

    for (var i in app.vehicle.data) {
        var v = app.vehicle.data[i];
        //console.log(v);
        if (typeof v !== 'undefined') {
            var marker = app.create_marker(v);
            app.markers[v.id] = marker;
        }
    }
};
app.create_marker = function (v) {
    ////console.log('status:' + v.status +',icon:'+v.icon +',icon_map:'+v.icon_map);
    //var icon = (v.icon_map == 'panah' || v.icon == undefined) ? util.create_icon(v.angle, v.status) : util.create_icon_object(v.icon);
    var icon = util.create_icon(v.angle, v.status);
    var latLng = new google.maps.LatLng(parseFloat(v.lat), parseFloat(v.lng));
    var m = new google.maps.Marker({
        position: latLng,
        map: app.map,
        icon: icon
    });
    google.maps.event.addListener(m, 'click', function () {
        if (app.vh_id != v.vh_id) {
            app.polyline_init();
        }

        app.marker = m;
        app.vh_id = v.vh_id;
        if (app.iw != undefined)
            app.iw.setMap(null);
        app.iw = new google.maps.InfoWindow({content: util.create_iw(v)});
        app.iw.setPosition(latLng);
        app.iw.open(app.map, m);
    });
    return m;
};
/* App Vehicle */
app.vehicle.clear = function () {
    app.vehicle.num_gps_all = 0;
    app.vehicle.num_gps_on = 0;
    app.vehicle.num_gps_off = 0;
    if (app.vehicle.data) {
        app.vehicle.data = [];
        app.vehicle.data.length = 0;
    }
    if (app.vehicle.table) {
        app.vehicle.table.clearAll();
    }
}
app.vehicle.load = function () {
    console.log('app.vehicle.load');
    app.vehicle.clear();
    app.progress.showProgress({delay: 60000, hide: true});
    webix.ajax().post(app.vehicle.load_url, {user_id: app.user_id}, function (text, xml, xhr) {
        try {
            var result = xml.json();
            console.log(result);
            for (var i in result.rows) {
                try {
                    var v = result.rows[i];
                    v['details'] = util.create_info_gps(v);
                    util.parse_status(v);
                    app.vehicle.num_gps_all++;
                    if (v.status == 'on' || v.status == 'stop') {
                        app.vehicle.num_gps_on++;
                    } else {
                        app.vehicle.num_gps_off++;
                    }
                    app.vehicle.data[v.id] = v;
                } catch (e) {
                    console.log(e);
                }
            }
            var rows = app.vehicle.getArray();
            app.vehicle.table.parse(rows);
            app.vehicle.table.refresh();
            setTimeout(function () {
                app.vehicle.lbl_gps_all.setValue("Total:" + app.vehicle.num_gps_all);
                app.vehicle.lbl_gps_on.setValue("On:" + app.vehicle.num_gps_on);
                app.vehicle.lbl_gps_off.setValue("Off:" + app.vehicle.num_gps_off);
                app.draw_markers();
                app.playback.prepare_form();
                app.progress.hideProgress();
            }, 1000);
        } catch (e) {
            console.log(e);
            app.progress.hideProgress();
        }
    });
};
app.vehicle.getArray = function () {
    var temp = [];
    app.vehicle.rows = [];
    app.vehicle.rows.length = 0;
    var index = 0;
    for (var i in app.vehicle.data) {
        var v = app.vehicle.data[i];
        v['index'] = index++;
        temp.push(v);
        app.vehicle.rows[v.id] = v;
    }
    return temp;
};
app.vehicle.getList = function () {
    var temp = [];
    for (var i in app.vehicle.data) {
        var v = app.vehicle.data[i];
        temp.push({id: v.id, value: v.nopol});
    }
    return temp;
};
app.vehicle.save = function () {
    var values = app.vehicle.form.getValues();
    console.log(values);
    console.log(app.vehicle.save_url);
    if (values == null || values == undefined) {
        cosnole.log('values null');
        webix.message({type: "Error", text: "Form Tidak Boleh Kosong"});
        return;
    }
    app.progress.showProgress({delay: 60000, hide: true});
    webix.ajax().post(app.vehicle.save_url, values, function (text, xml, xhr) {
        //console.log(text);
        try {
            var result = xml.json();
            webix.message({type: "error", text: result.msg});
            app.progress.hideProgress();
        } catch (e) {
            webix.message({type: "error", text: e});
            app.progress.hideProgress();
        }
    });
};
app.vehicle.add = function () {
    app.vehicle.form.setValues({mode: "add", id: 0});
    app.vehicle.views.setValue("view_edit_vehicle");
};
app.vehicle.edit = function () {
    var row = app.vehicle.table.getSelectedItem();
    if (row === null || row === undefined) {
        webix.message({type: 'error', text: 'Select GPS'});
        return;
    }
    app.vehicle.views.setValue("view_edit_vehicle");
    row['mode'] = 'edit';
    app.vehicle.form.setValues(row);
};
app.vehicle.delete = function () {
    var row = app.vehicle.table.getSelectedItem();
    if (row === null || row === undefined) {
        webix.message({type: 'error', text: 'Select GPS'});
        return;
    }
    row['mode'] = 'delete';
    console.log(app.vehicle.save_url);
    console.log(row);
    webix.confirm("Delete Vehicle " + row.nopol + " ?", function (result) {
        if (result) {
            webix.ajax().post(app.vehicle.save_url, row, function (text, xml, xhr) {
                console.log(text);
                try {
                    var json = xml.json();
                    webix.message({type: 'error', text: json.msg});
                    if (json.code === 'SUCCESS') {
                        app.vehicle.load();
                    }
                } catch (e) {
                    webix.message({type: 'error', text: e});
                }
            });
        }
    });
};
app.vehicle.cut = function () {
    webix.confirm("Cut Engine?", function (result) {
        if (result) {
            app.send_command(CONST.CUT_ENGINE);
        }
    });
};
app.vehicle.resume = function () {
    webix.confirm("Resume Engine?", function (result) {
        if (result) {
            app.send_command(CONST.RESUME_ENGINE);
        }
    });
};
/* App User */
app.user.clear = function () {
    if (app.user.table) {
        app.user.table.clearAll();
    }
    if (app.user.data) {
        app.user.data = [];
        app.user.data.length = 0;
    }
};
app.user.load = function () {
    app.user.clear();
    app.progress.showProgress({delay: 60000, hide: true});
    //app.user.progressbar.showProgress({delay: 60000, hide: true});
    webix.ajax().post(app.user.load_url, {}, function (text, xml, xhr) {
        try {
            var result = xml.json();
            // console.log(result.data);
            var options = [];
            for (var i in result) {
                var u = result[i];
                u.password = '';
                app.user.data.push(u);
                options.push({id: parseInt(u.id, 10), value: u.real_name});
            }
            setTimeout(function () {
                app.user.lbl_total.setValue("User:" + app.user.data.length);
                var listUser = app.user.combo.getPopup().getList();
                listUser.clearAll();
                listUser.parse(options);
                app.user.table.parse(app.user.data);
                app.progress.hideProgress();
                for (var i in options) {
                    app.user.combo.setValue(options[i].id);
                    break;
                }
            }, 1000);
        } catch (e) {
            console.log(e);
            app.progress.hideProgress();
        }
    });
};
app.user.save = function () {
    var values = app.user.form.getValues();
    if (values == null || values == undefined) {
        cosnole.log('values null');
        webix.message({type: "Error", text: "Form Tidak Boleh Kosong"});
        return;
    }
    console.log(values);
    app.progress.showProgress({delay: 60000, hide: true});
    webix.ajax().post(app.user.save_url, values, function (text, xml, xhr) {
        //console.log(text);
        try {
            var result = xml.json();
            webix.message({type: "error", text: result.msg});
            if (result.code == 'SUCCESS') {
                app.user.load();
            }
            app.progress.hideProgress();
        } catch (e) {
            webix.message({type: "error", text: e});
            app.progress.hideProgress();
        }
    });
};
app.user.delete = function () {
    var row = app.user.table.getSelectedItem();
    if (row === null || row === undefined) {
        webix.message({type: 'error', text: 'Select GPS'});
        return;
    }
    row['mode'] = 'delete';
    console.log(app.user.save_url);
    console.log(row);
    webix.confirm("Delete Vehicle " + row.nopol + " ?", function (result) {
        if (result) {
            webix.ajax().post(app.user.save_url, row, function (text, xml, xhr) {
                console.log(text);
                try {
                    var json = xml.json();
                    webix.message({type: 'error', text: json.msg});
                    if (json.code === 'SUCCESS') {
                        app.user.load();
                    }
                } catch (e) {
                    webix.message({type: 'error', text: e});
                }
            });
        }
    });
};
app.update_position = function (newPos) {
    var id = newPos.vh_id;
    var oldPos = app.vehicle.data[id];
    if (oldPos == undefined) {
        return;
    }
    if (oldPos.status == 'off') {
        app.vehicle.num_gps_off--;
        app.vehicle.num_gps_on++;
    }
    oldPos.park_date = newPos.park_date != undefined ? newPos.park_date : '0000-00-00 00:00:00';
    oldPos.tdate = newPos.tdate;
    oldPos.sdate = newPos.sdate;
    oldPos.alarm = newPos.alarm;
    oldPos.user_id = parseInt(newPos.user_id, 10);
    oldPos.speed = parseFloat(newPos.speed); //.toFixed(2);
    oldPos.odo = parseFloat(newPos.odo); //.toFixed(2);
    oldPos.angle = parseFloat(newPos.angle);
    oldPos.lat = parseFloat(newPos.lat);
    oldPos.lng = parseFloat(newPos.lng);
    oldPos.acc = newPos.acc;
    oldPos.charge = newPos.charge;
    oldPos.batt = newPos.batt;
    oldPos.sat = newPos.sat;
    oldPos.fcut = newPos.fcut;
    oldPos.poi = newPos.poi;
    oldPos.address = newPos.address;
    oldPos.status = newPos.status;
    oldPos.info = newPos.info;
    oldPos['details'] = util.create_info_gps(oldPos);
    //Update Marker
    var m = app.markers[id];
    if ((m === undefined) || (m === null)) {
        return;
    }
    //app.log('Marker Found');
    var newLatLng = new google.maps.LatLng(oldPos.lat, oldPos.lng);
    m.setPosition(newLatLng);
    if (oldPos.icon_map == 'panah') {
        m.setIcon(util.create_icon(oldPos.angle, oldPos.status));
    }
    if (app.vh_id != id)
        return;
    if (app.polyline == null) {
        app.polyline_init();
    }
    var path = app.polyline.getPath();
    path.push(newLatLng);
    app.map.setCenter(newLatLng);
    if (app.iw) {
        app.iw.setContent(util.create_iw(oldPos));
    }
};
app.init_websocket = function () {
    console.log('init_websocket');
    app.socket = new WebSocket(websocket_server);
    app.socket.onopen = function (evt) {
        app.log('onopen');
        if (app.hReconnect) {
            clearTimeout(app.hReconnect);
        }
        app.hReconnect = setTimeout(function () {
            var xml = "login," + app.user_id + "," + app.session;
            app.log(xml);
            app.socket.send(xml);
        }, 3000);
    };
    app.socket.onmessage = function (evt) {
        var data = JSON.parse(evt.data);
        app.log(data);
        if (data.type == undefined)
            return;
        switch (data.type) {
            case CONST.GPS:
                app.num_new_data++;
                util.parse_status(data);
                if (data.alarm > 0) {
                    app.log("Alarm Aktif:" + util.formatAlarm(data.alarm));
                }
                //console.log(data);
                app.update_position(data);
                break;
            case CONST.CUT_ENGINE:
                webix.message({type: 'error', text: data.msg});
                break;
            case CONST.RESUME_ENGINE:
                webix.message({type: 'error', text: data.msg});
                break;
            case CONST.OVER_SPEED:
                webix.message({type: 'error', text: data.msg});
                break;
            case 'enable_alarm_park':
                $.messager.alert(data.state, data.msg);
                break;
            case 'disable_alarm_park':
                $.messager.alert(data.state, data.msg);
                break;
            case 'enable_oil_alarm':
                $.messager.alert(data.state, data.msg);
                break;
            case 'disable_oil_alarm':
                $.messager.alert(data.state, data.msg);
                break;
            case 'set_odometer':
                $.messager.alert(data.state, data.msg);
                break;
            case 'broadcast':
                $.messager.alert(data.state, data.msg);
                break;
            case 'set_io':
                $.messager.alert(data.state, data.msg);
                break;
            default:
                console.log(data);
                break;
        }

    };
    app.socket.onclose = function (evt) {
        app.log('onclose');
        if (app.hReconnect) {
            clearTimeout(app.hReconnect);
        }
        app.hReconnect = setTimeout(function () {
            app.init_websocket();
        }, 10000);
    };
    app.socket.onerror = function (evt) {
        app.log('onerror');
        if (app.hReconnect) {
            clearTimeout(app.hReconnect);
        }
        app.hReconnect = setTimeout(function () {
            app.init_websocket();
        }, 10000);
    };
};
app.send_command = function (command) {
    var v = app.vehicles[app.vh_id];
    console.log(command);
    console.log(v);
    switch (command) {
        case CONST.CUT_ENGINE:
            var msg = "cutengine," + v.imei + "," + v.gps_brand;
            ////console.log("Pesan:" + msg);
            app.socket.send(msg);
            break;
        case CONST.RESUME_ENGINE:
            var msg = "resumeengine," + v.imei + "," + v.gps_brand;
            ////console.log("Pesan:" + msg);
            app.socket.send(msg);
            break;
        case 'overspeed':
            $.messager.prompt('Overspeed Alarm (Hanya Untuk GT06)', 'Masukkan Kecepatan, 0== Disable', function (r) {
                if (r) {
                    var msg = "overspeed," + v.imei + "," + v.gps_brand + "," + r;
                    ////console.log("Pesan:" + msg);
                    app.socket.send(msg);
                }
            });
            break;
        case 'enable_alarm_park':
            webix.confirm('Alarm Parkir', 'Masukkan Batas Parkir (Jam)', function (r) {
                if (r) {
                    var msg = CONST.ENABLE_ALARM_PARK + "," + v.imei + "," + r;
                    ////console.log("Pesan:" + msg);
                    app.socket.send(msg);
                }
            });
            break;
        case 'disable_alarm_park':
            webix.confirm('Alarm Parkir', 'Nonaktifkan Alarm Parkir', function (r) {
                if (r) {
                    var msg = CONST.ENABLE_ALARM_PARK + "," + v.imei + ",0";
                    app.socket.send(msg);
                }
            });
            break;
        case 'set_center_number':
            webix.confirm('Setting Center Number', 'Example: 08133038444,081474848', function (r) {
                if (r) {
                    var msg = "set_center_number," + v.imei + "," + r;
                    app.socket.send(msg);
                }
            });
            break;
        case 'enable_alarm_oil':
            webix.confirm('Setting Alarm Ganti Oli', 'Masukkan Jarak Ganti Oli, format: Max_Odometer, Curr_Odometer', function (r) {
                if (r) {
                    var msg = "enable_alarm_oil," + v.imei + "," + r;
                    app.socket.send(msg);
                }
            });
            break;
        case 'disable_alarm_oil':
            webix.confirmm('Disable Alarm Ganti Oli', 'Disable Alarm Ganti Oli', function (r) {
                if (r) {
                    var msg = "disable_alarm_oil," + v.imei;
                    app.socket.send(msg); //$.messager.alert("Info", "Setting Sudah Dikirim Keserver, Silahkan Tunggu Response");
                }
            });
            break;
        case 'set_odometer':
            webix.confirm('Setting Jarak Tempuh', 'Masukkan Angka Jarak Tempuh', function (r) {
                if (r) {
                    var msg = "set_odometer," + v.imei + "," + r;
                    app.socket.send(msg);
                }
            });
            break;
    }
};
app.create_custom_marker = function (item, icon) {
    console.log('create_marker_park');
    var latlng = new google.maps.LatLng(parseFloat(item.lat), parseFloat(item.lng));
    var marker = new google.maps.Marker({
        title: item.toString(),
        position: latlng,
        map: app.map,
        icon: BASE_PATH + 'images/' + icon
    });
    google.maps.event.addListener(marker, 'click', function () {
        if (app.iw !== null)
            app.iw.close();
        var html = "<div'>";
        html += "<table>";
        html += "<tr><td>Date</td><td>:</td><td>" + item.tdate + "</td></tr>";
        if (item.park !== '') {
            html += "<tr><td>Park</td><td>:</td><td>" + item.park + "</td></tr>";
        }
        if (item.alarm > 0) {
            html += "<tr><td>Alarm</td><td>:</td><td>" + util.formatAlarm(item.alarm) + "</td></tr>";
        }
        html += "<tr><td>POI</td><td>:</td><td>" + item.poi + "</td></tr>";
        html += "<tr><td>Address</td><td>:</td><td>" + item.address + "</td></tr>";
        html += "</table>";
        html += "</div>";
        app.iw = new google.maps.InfoWindow({content: html});
        app.iw.setPosition(latlng);
        app.iw.open(app.map, marker);
    });
    return marker;
};
/* App Playback */
app.on("onDownloadStart", function () {
    app.playback.clear();
    app.playback.control_playback.hide();
    try {
        $$("btn_play_onmap").hide();
    } catch (e) {
    }
    app.progress.showProgress({delay: 300000, hide: true});
});
app.on("onDownloadFinish", function () {
    try {
        if (app.playback.tracks.length > 0) {
            app.playback.table.parse(app.playback.tracks);
            app.playback.control_playback.show();
            $$("btn_play_onmap").show();
        }
    } catch (e) {
    }
    app.progress.hideProgress();
    webix.message({type: "error", text: "Total:" + app.playback.tracks.length});
});
app.playback.download = function () {
    app.trigger("onDownloadStart");
    var values = app.playback.form.getValues();
    webix.ajax().post(app.playback.download_url, values, function (text, xml, xhr) {
        console.log(text);
        try {
            if (xhr.readyState === 4) {
                var json = xml.json();
                app.playback.parse(json);
                app.trigger("onDownloadFinish");
            } else {
                webix.message({type: "error", text: text});
                app.trigger("onDownloadFinish");
            }
        } catch (e) {
            webix.message({type: "error", text: e});
            app.trigger("onDownloadFinish");
        }
    });
};
app.playback.prepare_form = function () {
    try {
        var list = app.playback.combo_vehicle.getPopup().getList();
        list.clearAll();
        list.parse(app.vehicle.getList());
        app.playback.combo_vehicle.refresh();
    } catch (e) {
        console.log(e)
    }
};
app.playback.clear_form = function () {
    try {
        var list = app.playback.combo_vehicle.getPopup().getList();
        list.clearAll();
        app.playback.combo_vehicle.refresh();
    } catch (e) {

    }
};
app.playback.clear = function () {
    console.log('playback.clear');
    for (var i in app.playback.markers) {
        if (app.playback.markers[i]) {
            console.log("Clear");
            app.playback.markers[i].setMap(null);
        }
    }
    app.playback.markers = [];
    app.playback.markers.length = 0;
    try {
        if (app.playback.marker) {
            app.playback.marker.setMap(null);
        }
    } catch (e) {
    }
    try {
        if (app.playback.polyline) {
            app.playback.polyline.setMap(null);
        }
    } catch (e) {
    }
    try {
        app.playback.tracks = [];
        app.playback.tracks.length = 0;
    } catch (e) {
    }

    try {
        app.playback.table.clearAll();
        app.playback.table.refresh();
    } catch (e) {

    }
    app.playback.paused = false;
};
app.playback.loop = function () {
    if (app.playback.timer) {
        clearTimeout(app.playback.timer);
    }
    var t = app.playback.tracks[app.playback.index++];
    if (t == null || t == undefined) {
        webix.message({type: "error", text: "Playback Finish"});
        return;
    }
    var icon = util.create_icon(t.angle, t.status);
    var latlng = new google.maps.LatLng(parseFloat(t.lat), parseFloat(t.lng));
    app.playback.marker.setIcon(icon);
    app.playback.marker.setPosition(latlng);
    app.map.setCenter(latlng);
    if (app.iw) {
        app.iw.setContent(util.create_iw(t));
    }

    app.playback.timer = setTimeout('app.playback.loop()', app.playback.speed);
};
app.playback.play = function () {
    app.playback.loop();
};
app.playback.pause = function () {
    if (app.playback.timer) {
        clearTimeout(app.playback.timer);
    }
    app.playback.paused = true;
};
app.playback.stop = function () {
    if (app.playback.timer) {
        clearTimeout(app.playback.timer);
    }
    app.playback.index = 0;
};
app.playback.parse = function (result) {
    try {
        var total = parseInt(result.total, 10);
        if (total <= 0) {
            webix.message({type: "error", text: "Data Kosong\r\n" + result.msg});
            return;
        }
    } catch (e) {
        console.log(e);
        webix.message({type: "error", text: e});
        return;
    }

    var path = [];
    for (var i in result.data) {
        var item = result.data[i];
        console.log(item);
        item = util.parse_status(item);
        path.push(new google.maps.LatLng(parseFloat(item.lat), parseFloat(item.lng)));
        if (item.park != '') {
            var marker = app.create_custom_marker(item, 'park.png');
            app.playback.markers.push(marker);
        }
        app.playback.tracks.push(item);
    }
    //Create Polyline
    app.playback.polyline = new google.maps.Polyline({
        path: path,
        map: app.map,
        strokeColor: "#9797ff",
        strokeOpacity: 0.7,
        strokeWeight: 5
    });
    //Create Marker
    for (var i in app.playback.tracks) {
        console.log(app.playback.tracks[i]);
        app.playback.marker = app.create_marker(app.playback.tracks[i]);
        app.map.setCenter(app.playback.marker.getPosition());
        app.map.setZoom(16);
        break;
    }
    //Clear raw data
    result = [];
    result.length = 0;
};

