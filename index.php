<?php require_once 'session.php'; ?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="codebase/webix.css" type="text/css" media="screen" charset="utf-8">
        <link rel="stylesheet" href="css/mobile.css?v=1.0.1" type="text/css" media="screen" charset="utf-8">
        <script src="codebase/webix.js" type="text/javascript" charset="utf-8"></script>
        <script src="js/util.js?v=1.0.3" type="text/javascript" charset="utf-8"></script>
        <script src="js/lang.js?v=1.0.3" type="text/javascript" charset="utf-8"></script>
        <script src="js/vars.js?v=1.0.5" type="text/javascript" charset="utf-8"></script>
        <script src="js/m.js?v=1.0.5" type="text/javascript" charset="utf-8"></script>
        <title>GPS Tracker Mobile</title>
    </head>
    <style type="text/css">
        .custom_item{
            height:auto;
            width: 600px;
            text-align: left;
            border-bottom:1px solid #009966;
            border-radius:4px;
            background-color:#ffffee;
            padding:10px;

        }
        .newtime{
            background-color:#DDFFDD;
        }
        .oldtime{
            background-color:#DDDDFF;
        }
        .oldtime, .newtime{
            border-radius:4px;
        }    
        .multiline{
            line-height: 25px !important;
        }
    </style>
    <body>       
        <script type="text/javascript">
            webix.ready(function () {
                webix.ui.fullScreen();
                app.init();
                webix.Touch.limit(true);
            });
        </script>
    </body>
</html>