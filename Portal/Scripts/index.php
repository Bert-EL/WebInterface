<?php

/**
 * Created by PhpStorm.
 * User: developer
 * Date: 15/12/2015
 * Time: 9:29
 */

namespace WebServices;

    require_once dirname(dirname(__DIR__)) . '/myphp/ZabbixAPI.php';
    require_once dirname(dirname(__DIR__)) . '/myphp/CustomerParser.php';
    require_once dirname(dirname(__DIR__)) . '/mysites/NewCustomer.php';

    $zapi = new ZabbixAPI();
    $zapi->Authenticate();

    $parser = new CustomerParser();

    var_dump($_REQUEST);

    $customer = new Customer();

    $userID = 0;
    $newUsername = "";

    $userGroupID = 0;
    $newUserGroupName = "";

    $hostGroupID = 0;
    $newHostGroupName = "";

    $isNameSelected = !empty($_POST["name"]);

    if ($zapi->IsValidAuthToken())
    {
        if ($isNameSelected)
        {
            $customer->name = $_POST["name"];
            $customer->code = $parser->GetCodeByName($customer->name);

            $newUsername = $zapi->GetNewUsername($customer->code);
            $newUserGroupName = $zapi->GetNewUserGroupName($customer);
            $newHostGroupName = $zapi->GetNewHostGroupName($customer);
        }

        if (!empty($_POST["submit_fInit"]))
        {
            $hostGroupID = $zapi->CreateHostGroupByCode($customer);
            $userGroupID = $zapi->CreateUserGroupByCode($customer, $hostGroupID, Permission::ReadOnlyAccess);
            $userID = $zapi->CreateUserByCode($customer->code, NewCustomer::DEFAULT_PASSWORD, $userGroupID);
            $zapi->CreateActionForNewHost($customer, $newHostGroupName, array(10163, 10211));
        }

        if (!empty($_POST["submit_fHost"]))
        {
            $ip = $_POST["ip"];
            $dns = $_POST["dns"];
            $type = $_POST["type"];
            $port = $_POST["port"];
            $hostname = $_POST["hostname"];
            $useIP = ($_POST["useip"] === "on");
            $isDefault = ($_POST["default"] === "on");

            $interface = new HostInterface(InterfaceType::GetValue($type), $isDefault, $useIP);
            $interface->port = $port;
            $interface->ip = ($useIP) ? ($ip) : ("");
            $interface->dns = (!$useIP) ? ($dns) : ("");

            $zapi->CreateHost($hostname, $hostGroupID, $interface);
        }
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
        <link href="../../assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="../../assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="../../assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../../assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css" />
        <link href="../../assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
        <!-- END GLOBAL MANDATORY STYLES -->

        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="../../assets/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="../../assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME GLOBAL STYLES -->

        <!-- BEGIN THEME LAYOUT STYLES -->
        <link href="../../assets/layouts/layout/css/layout.min.css" rel="stylesheet" type="text/css" />
        <link href="../../assets/layouts/layout/css/themes/light.min.css" rel="stylesheet" type="text/css" id="style_color" />
        <link href="../../assets/layouts/layout/css/custom.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME LAYOUT STYLES -->

        <link href="portal.scripts.style.css" rel="stylesheet" type="text/css"/>
        <link rel="shortcut icon" href="../../favicon.ico" /> </head>
    <!-- END HEAD -->

    <body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white">

    <!-- BEGIN HEADER -->
    <div class="page-header navbar navbar-fixed-top">

        <!-- BEGIN HEADER INNER -->
        <div class="page-header-inner ">

            <!-- BEGIN LOGO -->
            <div class="page-logo">
                <a href="../../Portal/index.php">
                    <img src="../../assets/layouts/layout/img/logo.png" alt="logo" class="logo-default"/></a>
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
                            <img alt="" class="img-circle" src="../../assets/layouts/layout/img/avatar.png" />
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
                        <a href="../../Dashboard/index.php" class="nav-link nav-toggle">
                            <i class="icon-home"></i>
                            <span class="title">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item active open">
                        <a href="javascript:;" class="nav-link nav-toggle">
                            <i class="icon-settings"></i>
                            <span class="title">Scripts</span>
                            <span class="selected"></span>
                            <span class="arrow open"></span>
                        </a>
                        <ul class="sub-menu">
                            <li class="nav-item active open">
                                <a href="../Scripts/index.php" class="nav-link ">
                                    <span class="title">Nieuwe klant aanmaken</span>
                                    <span class="selected"></span>
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
                <h3 class="page-title">Voeg een nieuwe klant toe aan de monitoring omgeving</h3>
                <!-- END PAGE TITLE-->

                <!-- END PAGE HEADER-->
                <div class="note note-info">
                    <p>
                        Gebruik deze pagina om een nieuwe gebruiker toe te voegen aan de monitoring omgeving.<br/>
                        De gegevens die gebruikt worden om de nieuwe klant aan te maken zijn afkomstig van een XML.
                    </p>
                </div>
                
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption font-red-sunglo">
                            <i class="icon-settings font-red-sunglo"></i>
                            <span class="caption-subject bold uppercase">Gebruiker, gebruikersgroep en host groep</span>
                        </div>
                    </div>
                    <div class="portlet form">
                        <form role="form" id="fInit" action="#" method="post">
                            <div class="form-body">
                                <div class="table">
                                    <div class="tr">
                                        <div class="th"><label for="cbox_Customer">Klant</label></div>
                                        <div class="td">

                                            <select name="name" class="field" id="cbox_Customer">

                                                <option value=""></option>

                                                <?php
                                                foreach ($parser->GetCustomers() as $item)
                                                {
                                                    if ($isNameSelected && $customer->name == $item->name)
                                                    {
                                                        echo "<option selected value='" . $item->name . "'>" . $item->name . "</option>";
                                                    }
                                                    else
                                                    {
                                                        echo "<option value='" . $item->name . "'>" . $item->name . "</option>";
                                                    }
                                                }
                                                ?>
                                            </select>

                                        </div>
                                    </div>
                                    <div class="tr">
                                        <div class="td"><label for="tbox_User">User</label></div>
                                        <div class="td">
                                            <input class=field id="tbox_User" type="text" value="<?php echo $newUsername ?>" disabled>
                                            <input type="hidden" name="newUser" value="<?php echo $newUsername ?>">
                                        </div>
                                    </div>
                                    <div class="tr">
                                        <div class="td"><label for="tbox_UserGroup">User group</label></div>
                                        <div class="td">
                                            <input class=field id="tbox_UserGroup" type="text" value="<?php echo $newUserGroupName ?>" disabled>
                                            <input type="hidden" name="newUserGroup" value="<?php echo $newUserGroupName ?>">
                                        </div>
                                    </div>
                                    <div class="tr">
                                        <div class="td"><label for="tbox_HostGroup">Host group</label></div>
                                        <div class="td">
                                            <input class=field id="tbox_HostGroup" type="text" value="<?php echo $newHostGroupName ?>" disabled>
                                            <input type="hidden" name="newHostGroup" value="<?php echo $newHostGroupName ?>">
                                        </div>
                                    </div>

                                    <input name="code" type="hidden" id="hidden_code" value="<?php echo $customer->code ?>">
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" name="submit_fHost" class="btn blue">Aanmaken</button>
                                <button type="button" class="btn default">Annuleren</button>
                            </div>
                        </form>
                    </div>
                </div>

                <h4>Host</h4><br/>

                <h5>Gegevens</h5><br/>

                <form id="fHost" action="#" method="post">
                    <div class="table">
                        <div class="tr">
                            <div class="th"><label for="cbox_HostGroup">Groep</label></div>
                            <div class="td">

                                <select name="hostgroup" id="cbox_HostGroup" class="field">

                                    <option value=""></option>

                                    <?php
                                    foreach ($zapi->GetHostGroups() as $item)
                                    {
                                        if ($isNameSelected && $newHostGroupName == $item->name)
                                        {
                                            echo "<option selected value='" . $item->name . "'>" . $item->name . "</option>";
                                        }
                                        else
                                        {
                                            echo "<option value='" . $item->name . "'>" . $item->name . "</option>";
                                        }
                                    }
                                    ?>
                                </select>

                            </div>
                        </div>
                        <div class="tr">
                            <div class="td"><label for="tbox_Prefix">Prefix</label></div>
                            <div class="td"><input class="field" id="tbox_Prefix" type="text" onkeydown="PreviewHostname()" onkeyup="this.onkeydown()" onchange="this.onkeydown()"></div>
                        </div>
                        <div class="tr">
                            <div class="td"><label for="tbox_Host">Host</label></div>
                            <div class="td"><input class="field" id="tbox_Host" type="text" onkeydown="PreviewHostname()" onkeyup="this.onkeydown()" onchange="this.onkeydown()"></div>
                        </div>
                        <div class="tr">
                            <div class="td"><label for="tbox_Preview">Voorbeeld</label></div>
                            <div class="td"><input name="newHost" class="field" id="tbox_Preview" type="text" disabled></div>
                            <input type="hidden" name="hostname" id="hidden_hostname">
                        </div>
                    </div>

                    <h5>Interface</h5><br/>

                    <input type="checkbox" name="default" id="chbox_Interface" checked><label for="chbox_Interface" class="font">Standaard interface</label> <br/>
                    <input type="checkbox" name="useip" id="chbox_UseIP" onchange="UseIPCheckedChanged()" checked><label for="chbox_UseIP" class="font">Gebruik IP</label><br/>
                    <br/>

                    <div class="table">
                        <div class="tr">
                            <div class="th"><label for="tbox_IP">IP</label></div>
                            <div class="td">
                                <input type="text" class="field" id="tbox_IP">
                                <input type="hidden" name="ip" id="hidden_IP">
                            </div>
                        </div>
                        <div class="tr">
                            <div class="td"><label for="tbox_DNS">DNS</label></div>
                            <div class="td">
                                <input type="text" class="field" id="tbox_DNS" disabled>
                                <input type="hidden" name="dns" id="hidden_DNS">
                            </div>
                        </div>
                        <div class="tr">
                            <div class="td"><label for="tbox_Port">Port</label></div>
                            <div class="td"><input name="port" type="text" class="field" id="tbox_Port" ></div>
                        </div>
                        <div class="tr">
                            <div class="td"><label for="cbox_Type">Type</label></div>
                            <div class="td">

                                <select name="type" class="field" id="cbox_Type">
                                    <?php
                                    foreach (InterfaceType::GetNames() as $item)
                                    {
                                        echo "<option value='" . $item . "'>" . $item . "</option>";
                                    }
                                    ?>
                                </select>

                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="submit_fHost" class="btn blue">Aanmaken</button>
                        <button type="button" class="btn default">Annuleren</button>
                    </div>
                </form>

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
    <script src="../../assets/global/plugins/respond.min.js"></script>
    <script src="../../assets/global/plugins/excanvas.min.js"></script>
    <![endif]-->
    <!-- BEGIN CORE PLUGINS -->
    <script src="../../assets/global/plugins/jquery.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
    <!-- END CORE PLUGINS -->
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="../../assets/global/scripts/app.min.js" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="../../assets/layouts/layout/scripts/layout.min.js" type="text/javascript"></script>
    <script src="../../assets/layouts/layout/scripts/demo.min.js" type="text/javascript"></script>
    <script src="../../assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
    <!-- END THEME LAYOUT SCRIPTS -->

    </body>
</html>