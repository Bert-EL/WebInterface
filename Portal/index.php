<?php

namespace WebServices;

    require_once dirname(__DIR__) . '/myphp/ZabbixAPI.php';
    require_once dirname(__DIR__) . '/myphp/CustomerParser.php';
    require_once dirname(__DIR__) . '/mysites/NewCustomer.php';

    $zapi = new ZabbixAPI();
    $zapi->Authenticate();

    var_dump($_REQUEST);

    if (!isset($_REQUEST["username"]) || !isset($_REQUEST["password"]) || !$zapi->AuthenticateUser($_REQUEST["username"], $_REQUEST["password"]))
    {
//        header("Location: /projects/WebInterface/Authentication/index.php");
//        die();
    }
    else
    {

    }
?>

<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->

    <!-- BEGIN HEAD -->
    <head>
        <meta charset="utf-8" />
        <title>Electro-Line | Monitring Portal</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="" name="description" />
        <meta content="" name="author" />

        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
        <!-- END GLOBAL MANDATORY STYLES -->

        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="../assets/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="../assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME GLOBAL STYLES -->

        <!-- BEGIN THEME LAYOUT STYLES -->
        <link href="../assets/layouts/layout/css/layout.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/layouts/layout/css/themes/light.min.css" rel="stylesheet" type="text/css" id="style_color" />
        <link href="../assets/layouts/layout/css/custom.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME LAYOUT STYLES -->

        <link rel="shortcut icon" href="../favicon.ico" /> </head>
    <!-- END HEAD -->

    <body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white">

        <!-- BEGIN HEADER -->
        <div class="page-header navbar navbar-fixed-top">

            <!-- BEGIN HEADER INNER -->
            <div class="page-header-inner ">

                <!-- BEGIN LOGO -->
                <div class="page-logo">
                    <a href="index.php">
                        <img src="../assets/layouts/layout/img/logo.png" alt="logo" class="logo-default"/></a>
                    <div class="menu-toggler sidebar-toggler"></div>
                </div>
                <!-- END LOGO -->

                <!-- BEGIN RESPONSIVE MENU TOGGLER -->
                <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse"> </a>
                <!-- END RESPONSIVE MENU TOGGLER -->

                <!-- BEGIN TOP NAVIGATION MENU -->
                <div class="top-menu">
                    <ul class="nav navbar-nav pull-right">

                        <!-- BEGIN USER LOGIN DROPDOWN -->
                        <li class="dropdown dropdown-user">
                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                <img alt="" class="img-circle" src="../assets/layouts/layout/img/avatar.png" />
                                <span class="username username-hide-on-mobile"><?php echo " User" ?></span>
                                <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-default">
                                <li>
                                    <a href="page_user_lock_1.html">
                                        <i class="icon-lock"></i> Lock Screen </a>
                                </li>
                                <li>
                                    <a href="page_user_login_1.html">
                                        <i class="icon-key"></i> Log Out </a>
                                </li>
                            </ul>
                        </li>
                        <!-- END USER LOGIN DROPDOWN -->

                    </ul>
                </div>
                <!-- END TOP NAVIGATION MENU -->

            </div>
            <!-- END HEADER INNER -->

        </div>
        <!-- END HEADER -->

        <!-- BEGIN HEADER & CONTENT DIVIDER -->
        <div class="clearfix"> </div>
        <!-- END HEADER & CONTENT DIVIDER -->

        <!-- BEGIN CONTAINER -->
        <div class="page-container">

            <!-- BEGIN SIDEBAR -->
            <div class="page-sidebar-wrapper">
                <!-- BEGIN SIDEBAR -->
                <div class="page-sidebar navbar-collapse collapse">
                    <!-- BEGIN SIDEBAR MENU -->
                    <ul class="page-sidebar-menu  page-header-fixed " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" style="padding-top: 20px">
                        <li class="sidebar-toggler-wrapper hide">
                            <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
                            <div class="sidebar-toggler"> </div>
                            <!-- END SIDEBAR TOGGLER BUTTON -->
                        </li>
                        <li class="heading">
                            <h3 class="uppercase">Monitoring</h3>
                        </li>
                        <li class="nav-item start">
                            <a href="../Dashboard/index.php" class="nav-link">
                                <i class="icon-home"></i>
                                <span class="title">Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="javascript:;" class="nav-link nav-toggle">
                                <i class="icon-settings"></i>
                                <span class="title">Scripts</span>
                                <span class="arrow"></span>
                            </a>
                            <ul class="sub-menu">
                                <li class="nav-item">
                                    <a href="Scripts/index.php" class="nav-link">
                                        <span class="title">Nieuwe klant aanmaken</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <!-- END SIDEBAR MENU -->
                </div>
                <!-- END SIDEBAR -->
            </div>
            <!-- END SIDEBAR -->

            <!-- BEGIN CONTENT -->
            <div class="page-content-wrapper">

                <!-- BEGIN CONTENT BODY -->
                <div class="page-content">

                    <!-- BEGIN PAGE TITLE-->
                    <h3 class="page-title">Welkom</h3>
                    <!-- END PAGE TITLE-->

                    <!-- END PAGE HEADER-->
                    <div class="note note-info">
                        <p>Uitleg over de monitoring tool en dat Bert Delbaere, namelijke de Developer, geen schuld treft.</p>
                    </div>
                </div>
                <!-- END CONTENT BODY -->
            </div>
            <!-- END CONTENT -->
        </div>
        <!-- END CONTAINER -->

        <!-- BEGIN FOOTER -->
        <div class="page-footer">
            <div class="page-footer-inner"> 2015 &copy;
                <a href="http://www.electro-line.be/" target="_blank">Electro-Line</a>
            </div>
            <div class="scroll-to-top">
                <i class="icon-arrow-up"></i>
            </div>
        </div>
        <!-- END FOOTER -->

        <!--[if lt IE 9]>
        <script src="../assets/global/plugins/respond.min.js"></script>
        <script src="../assets/global/plugins/excanvas.min.js"></script>
        <![endif]-->

        <!-- BEGIN CORE PLUGINS -->
        <script src="../assets/global/plugins/jquery.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->

        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <!-- END THEME GLOBAL SCRIPTS -->

        <!-- BEGIN THEME LAYOUT SCRIPTS -->
        <script src="../assets/layouts/layout/scripts/layout.min.js" type="text/javascript"></script>
        <script src="../assets/layouts/layout/scripts/demo.min.js" type="text/javascript"></script>
        <script src="../assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
        <!-- END THEME LAYOUT SCRIPTS -->
    </body>
</html>


<!--<!DOCTYPE html>-->
<!--<!---->
<!--To change this license header, choose License Headers in Project Properties.-->
<!--To change this template file, choose Tools | Templates-->
<!--and open the template in the editor.-->
<!---->
<!---->
<!--<html>-->
<!--    -->
<!--    <head>-->
<!--        <meta charset="UTF-8">-->
<!--        <title>Test pagina</title>-->
<!--        <link rel="stylesheet" type="text/css" href="eline.css">-->
<!--        <script type="text/javascript" src="js/jquery/jquery.js"></script>-->
<!---->
<!--        <script type="text/javascript">-->
<!---->
<!--            function Browse(location)-->
<!--            {-->
<!--                document.getElementById('navigation').src = String(location);-->
<!--            }-->
<!---->
<!--        </script>-->
<!--    </head>-->
<!---->
<!--<!--    <body>-->
<!--    <body onload="Browse('http://172.16.252.1//zabbix/index.php')">-->
<!--        -->
<!--        <table>-->
<!--            <tr>-->
<!--                <td style="width: 10%">-->
<!--                    <div id="MenuBar">-->
<!---->
<!--//-->
<!--//                            $buttons = array-->
<!--//                            (-->
<!--//                                array-->
<!--//                                (-->
<!--//                                    "id"    =>  "btn_NewUser",-->
<!--//                                    "name"  =>  "Nieuwe gebruiker",-->
<!--//                                    "src"   =>  "http://localhost/projects/WebInterface/mysites/NewCustomerPage.php",-->
<!--//                                    "ajax"  =>  false-->
<!--//                                ),-->
<!--//                                array-->
<!--//                                (-->
<!--//                                    "id"    =>  "btn_NewCustomer",-->
<!--//                                    "name"  =>  "Nieuwe klant",-->
<!--//                                    "src"   =>  "http://172.16.252.1//zabbix/index.php",-->
<!--//                                    "ajax"  =>  true-->
<!--//                                ),-->
<!--//                                array-->
<!--//                                (-->
<!--//                                    "id"    =>  "btn_Test",-->
<!--//                                    "name"  =>  "Test",-->
<!--//                                    "src"   =>  "http://172.16.252.1//zabbix/index.php",-->
<!--//                                    "ajax"  =>  false-->
<!--//                                )-->
<!--//                            );-->
<!--//-->
<!--//                            foreach ($buttons as $item)-->
<!--//                            {-->
<!--//                                echo '<div class="button" id=' . $item["id"] . ' onclick="Browse(\'' . $item["src"] . '\')">' . $item["name"]  . '</div>' . "\n";-->
<!--//                            }-->
<!---->
<!--                    </div>      -->
<!--                </td>-->
<!--                <td style="width: 90%">-->
<!--                    <div class="ZabbixInterface">-->
<!--                        <iframe id="navigation" class="ZabbixInterface"></iframe>-->
<!--                    </div>-->
<!--                </td>-->
<!--            </tr>-->
<!--        </table>-->
<!--     -->
<!--        <script type="text/javascript">-->
<!---->
<!--//-->
<!--//                foreach ($buttons as $item)-->
<!--//                {-->
<!--//                    if($item["ajax"])-->
<!--//                    {-->
<!--//                        $s =-->
<!--//                            "$('#" . $item["id"] . "').click(function()-->
<!--//                            {-->
<!--//                                $.ajax(-->
<!--//                                {-->
<!--//                                    method:         'POST',-->
<!--//                                    url:            'eline_scripts.php',-->
<!--//                                    data:           {action: '" . $item["id"] . "'},-->
<!--//                                    success:        function(output)-->
<!--//                                    {-->
<!--//                                       alert(output);-->
<!--//                                    }-->
<!--//                                });-->
<!--//                            });";-->
<!--//-->
<!--//                        echo $s;-->
<!--//                        echo "\r\n\r\n\t";-->
<!--//                    }-->
<!--//                }-->
<!--//-->
<!--//            $(document).ready(function()-->
<!--//            {-->
<!--//                $.ajax(-->
<!--//                {-->
<!--//                    method:         'POST',-->
<!--//                    url:            'eline_scripts.php',-->
<!--//                    data:           {action: 'Authenticate'},-->
<!--//                    success:        function(output)-->
<!--//                    {-->
<!--////                        alert(output);-->
<!--//                    }-->
<!--//                });-->
<!--//            });-->
<!--//-->
<!--////            $("#btn_Test").click(function()-->
<!--////            {-->
<!--////                var frame = document.getElementById("navigation");-->
<!--////                alert();-->
<!--////                alert($("#name").val());-->
<!--////-->
<!--////                $("#request").val("");-->
<!--////                $("#name").val("Developer");-->
<!--////                $("#password").val("BeDe159-+Line");-->
<!--////                $("#autologin").attr("checked", true);-->
<!--////-->
<!--////                alert("ok");-->
<!--////                $("#enter").click();-->
<!--////            });-->
<!--//-->
<!--//        </script>-->
<!--//-->
<!--//    </body>-->
<!--//</html>-->
