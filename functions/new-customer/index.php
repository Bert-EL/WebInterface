<?php

/**
 * Created by PhpStorm.
 * User: developer
 * Date: 15/12/2015
 * Time: 9:29
 */

namespace WebServices;

    require_once dirname(dirname(__DIR__)) . '/classes/ZabbixAPI.php';
    require_once dirname(dirname(__DIR__)) . '/classes/CustomerParser.php';
    require_once dirname(dirname(__DIR__)) . '/classes/NewCustomer.php';

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

        <link href="functions.new-customers.style.css" rel="stylesheet" type="text/css"/>
        <link rel="shortcut icon" href="../../favicon.ico" />
    </head>
    <!-- END HEAD -->

    <body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white page-sidebar-fixed">

        <!-- BEGIN HEADER -->
        <div class="page-header navbar navbar-fixed-top">
            <!-- BEGIN HEADER INNER -->
            <div class="page-header-inner ">
                <!-- BEGIN LOGO -->
                <div class="page-logo">
                    <a href="../../">
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
                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-close-others="true">
                                <img alt="" class="img-rounded" src="../../assets/layouts/layout/img/avatar.png" />
                                <span class="username username-hide-on-mobile"><?php echo " User" ?></span>
                                <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-default">
                                <li>
                                    <a href="">
                                    <i class="icon-lock"></i> Lock Screen </a>
                                </li>
                                <li>
                                    <a href="">
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
                        <li class="heading">
                            <h3 class="uppercase">Monitoring</h3>
                        </li>
                        <li class="nav-item">
                            <a href="../../dashboard/" class="nav-link">
                                <i class="fa fa-dashboard"></i>
                                <span class="title">Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="javascript:;" class="nav-link nav-toggle">
                                <i class="fa fa-user"></i>
                                <span class="title">Gebruikers</span>
                                <span class="arrow"></span>
                            </a>
                            <ul class="sub-menu">
                                <li class="nav-item">
                                    <a href="../../user/create/" class="nav-link">
                                        <span class="title">Aanmaken</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="../../user/delete/" class="nav-link">
                                        <span class="title">Verwijderen</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="../../functions/new-customer/" class="nav-link">
                                        <span class="title">Overzicht</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="javascript:;" class="nav-link nav-toggle">
                                <i class="fa fa-users"></i>
                                <span class="title">Gebruikersgroepen</span>
                                <span class="arrow"></span>
                            </a>
                            <ul class="sub-menu">
                                <li class="nav-item">
                                    <a href="../../functions/new-customer/" class="nav-link">
                                        <span class="title">Aanmaken</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="../../functions/new-customer/" class="nav-link">
                                        <span class="title">Verwijderen</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="../../functions/new-customer/" class="nav-link">
                                        <span class="title">Overzicht</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="javascript:;" class="nav-link nav-toggle">
                                <i class="fa fa-gear"></i>
                                <span class="title">Hosts</span>
                                <span class="arrow"></span>
                            </a>
                            <ul class="sub-menu">
                                <li class="nav-item">
                                    <a href="../../functions/new-customer/" class="nav-link">
                                        <span class="title">Aanmaken</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="../../functions/new-customer/" class="nav-link">
                                        <span class="title">Verwijderen</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="../../functions/new-customer/" class="nav-link">
                                        <span class="title">Overzicht</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="javascript:;" class="nav-link nav-toggle">
                                <i class="fa fa-gears"></i>
                                <span class="title">Host groepen</span>
                                <span class="arrow"></span>
                            </a>
                            <ul class="sub-menu">
                                <li class="nav-item">
                                    <a href="../../functions/new-customer/" class="nav-link">
                                        <span class="title">Aanmaken</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="../../functions/new-customer/" class="nav-link">
                                        <span class="title">Verwijderen</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="../../functions/new-customer/" class="nav-link">
                                        <span class="title">Overzicht</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item active open">
                            <a href="javascript:;" class="nav-link nav-toggle">
                                <i class="fa fa-magic"></i>
                                <span class="title">Wizard</span>
                                <span class="selected"></span>
                                <span class="arrow open"></span>
                            </a>
                            <ul class="sub-menu">
                                <li class="nav-item active open">
                                    <a href="../../functions/new-customer/" class="nav-link">
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

                    <!-- BEGIN FORMS -->
                    <div class="col-md-6">

                        <!-- BEGIN USER(GROUP) & HOSTGROUP SETTINGS -->
                        <div class="portlet light bordered">
                            <div class="portlet-title">
                                <div class="caption font-red-sunglo">
                                    <i class="icon-user font-red-sunglo"></i>
                                    <span class="caption-subject bold uppercase">Gebruiker, gebruikersgroep en host groep</span>
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form class="form-horizontal" role="form" id="fInit" action="" method="post">
                                    <div class="form-body">
                                        <div class="form-group">
                                            <label class="col-md-3 control-label" for="cbox_Customer">Klant</label>
                                            <div class="col-md-9">
                                                <select name="name" class="form-control" id="cbox_Customer">
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
                                        <div class="form-group">
                                            <label class="col-md-3 control-label" for="tbox_User">Gebruiker</label>
                                            <div class="col-md-9">
                                                <input type="text" id="tbox_User" class="form-control" value="<?php echo $newUsername ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label" for="tbox_UserGroup">Gebruikersgroep</label>
                                            <div class="col-md-9">
                                                <input type="text" id="tbox_UserGroup" class="form-control" value="<?php echo $newUserGroupName ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label" for="tbox_HostGroup">Host groep</label>
                                            <div class="col-md-9">
                                                <input type="text" id="tbox_HostGroup" class="form-control" value="<?php echo $newHostGroupName ?>" readonly>
                                            </div>
                                        </div>
                                        <input name="code" type="hidden" id="hidden_code" value="<?php echo $customer->code ?>">
                                    </div>
                                    <div class="form-actions right">
                                        <button type="reset" class="btn default">Annuleren</button>
                                        <button type="submit" name="submit_fHost" class="btn blue">Aanmaken</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- END USER(GROUP) & HOSTGROUP SETTINGS-->

                        <!-- BEGIN HOST SETTINGS -->
                        <div class="portlet light bordered">
                            <div class="portlet-title">
                                <div class="caption font-red-sunglo">
                                    <i class="icon-settings font-red-sunglo"></i>
                                    <span class="caption-subject bold uppercase">Host</span>
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form role="form" id="fHost" action="" method="post" class="form-horizontal" novalidate="novalidate">
                                    <div class="form-body">
                                        <h4>Gegevens</h4>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label" for="cbox_HostGroup">Groep</label>
                                            <div class="col-md-9">
                                                <select name="cbox_HostGroup" id="cbox_HostGroup" class="form-control">
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
                                        <div class="form-group">
                                            <label class="col-md-3 control-label" for="tbox_Prefix">Prefix</label>
                                            <div class="col-md-9">
                                                <input type="text" name="tbox_Prefix" id="tbox_Prefix" class="form-control" onkeydown="PreviewHostname()" onkeyup="this.onkeydown()" onchange="this.onkeydown()">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label" for="tbox_Host">Host</label>
                                            <div class="col-md-9">
                                                <input type="text" name="tbox_Host" id="tbox_Host" class="form-control"  onkeydown="PreviewHostname()" onkeyup="this.onkeydown()" onchange="this.onkeydown()">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label" for="tbox_Preview">Voorbeeld</label>
                                            <div class="col-md-9">
                                                <input type="text" name="tbox_Preview" id="tbox_Preview" class="form-control" readonly>
                                            </div>
                                        </div>
                                        <input name="code" type="hidden" id="hidden_code" value="<?php echo $customer->code ?>">
                                        <h4>Interface</h4>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label"></label>
                                            <div class="col-md-9">
                                                <div class="checkbox-list">
                                                    <label>
                                                        <span><input type="checkbox" name="chbox_DefaultInterface" checked></span>Standaard interface
                                                    </label>
                                                    <label>
                                                        <span><input type="checkbox" name="chbox_UseIP" id="chbox_UseIP" onchange="UseIPCheckedChanged()" checked></span>Gebruik IP
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label" for="tbox_IP">IP</label>
                                            <div class="col-md-9">
                                                <input type="text" name="tbox_IP" id="tbox_IP" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label" for="tbox_DNS">DNS</label>
                                            <div class="col-md-9">
                                                <input type="text" name="tbox_DNS" id="tbox_DNS" class="form-control" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label" for="tbox_Port">Poort</label>
                                            <div class="col-md-9">
                                                <input type="text" name="tbox_Port" id="tbox_Port" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label" for="cbox_Type">Type</label>
                                            <div class="col-md-9">
                                                <select name="type" id="cbox_Type" class="form-control" >
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
                                    <div class="form-actions right">
                                        <button type="reset" class="btn default">Annuleren</button>
                                        <button type="submit" name="submit_fHost" class="btn blue">Aanmaken</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- END HOST SETTINGS -->

                    </div>
                    <!-- END FORMS -->
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
        <script src="../../assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->
        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <script src="../../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <!-- END THEME GLOBAL SCRIPTS -->
        <!-- BEGIN THEME LAYOUT SCRIPTS -->
        <script src="../../assets/layouts/layout/scripts/layout.min.js" type="text/javascript"></script>
        <script src="../../assets/layouts/layout/scripts/demo.min.js" type="text/javascript"></script>
        <script src="../../assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
        <!-- END THEME LAYOUT SCRIPTS -->

        <script src="functions.new-customer.scripts.js" type="text/javascript"></script>
    </body>
</html>