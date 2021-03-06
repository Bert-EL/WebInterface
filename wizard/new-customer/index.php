<?php

/**
 * Created by PhpStorm.
 * User: developer
 * Date: 24/12/2015
 * Time: 10:11
 */

namespace WebServices;

    require_once dirname(dirname(__DIR__)) . "/include/ZabbixAPI.php";
    require_once dirname(dirname(__DIR__)) . "/include/ZabbixClasses.php";
    require_once dirname(dirname(__DIR__)) . "/include/CustomerParser.php";
    require_once "create-customer.php";

    $parser = new CustomerParser();
    $zapi = new ZabbixAPI();
    $zapi->Authenticate();

    $messageToUser = "";
    $showMessageToUser = false;

    if (isset($_REQUEST["cbox_Customer"]))
    {
        $customer = new Customer();
        $customer->name = $_REQUEST["cbox_Customer"];
        $customer->code = $parser->GetCodeByName($customer->name);

        $user = new User();
        $user->name = $zapi->GetNewUsername($customer->code);

        $userGroup = new UserGroup();
        $userGroup->name = $zapi->GetNewUserGroupName($customer);

        $hostGroup = new HostGroup();
        $hostGroup->name = $zapi->GetNewHostGroupName($customer);

        $action = new Action();
        $action->name = $zapi->GetNewActionName($customer);

        $template = new Template();
        $template->name = $zapi->GetNewTemplateName($customer);

        if (isset($_REQUEST["btn_Submit"]))
        {
            if (!is_null($customer))
            {
                $host = new Host();
                $CreateCustomer = new CreateCustomer($zapi);
                $hostGroup->id = $zapi->CreateHostGroupByCode($customer);

                if ($hostGroup->id !== 0)
                {
                    $userGroup->id = $zapi->CreateUserGroupByCode($customer, $hostGroup->id, Permission::ReadOnlyAccess);
                    $action->id = $zapi->CreateActionForNewHost($customer, $hostGroup->id, array(10163, 10211));

                    if ($userGroup->id !== 0)
                    {
                        $user->id = $zapi->CreateUserByCode($customer->code, CustomerSettings::DEFAULT_PASSWORD, $userGroup->id);
                    }

                    $useIP = ($_REQUEST["rb_IpDns"] === "on");
                    $isDefault = ($_REQUEST["chbox_DefaultInterface"] === "on");

                    $interface = new HostInterface(InterfaceType::GetValue($_REQUEST["cbox_Type"]), $isDefault, $useIP);
                    $interface->port = $_REQUEST["tbox_Port"];
                    $interface->ip = ($useIP) ? ($_REQUEST["tbox_IP"]) : ("");
                    $interface->dns = (!$useIP) ? ($_REQUEST["tbox_DNS"]) : ("");

                    $host->name = $_REQUEST["tbox_Preview"];
                    $host->id = $zapi->CreateHost($host->name, $hostGroup->id, $interface);

                    if ($host->id !== 0)
                    {
                        $alias = $zapi->GetTemplateAlias($customer);
                        $template->id = $zapi->CreateTemplate($template->name, $alias, $hostGroup->id, $host->id);
                    }
                }

                $showMessageToUser = true;
                $messageToUser .= $CreateCustomer->EvaluateElement(NameType::User, $user);
                $messageToUser .= $CreateCustomer->EvaluateElement(NameType::UserGroup, $userGroup);
                $messageToUser .= $CreateCustomer->EvaluateElement(NameType::Host, $host);
                $messageToUser .= $CreateCustomer->EvaluateElement(NameType::HostGroup, $hostGroup);
                $messageToUser .= $CreateCustomer->EvaluateElement(NameType::Template, $template);
                $messageToUser .= $CreateCustomer->EvaluateElement(NameType::Action, $action);
            }

            unset($customer);
            unset($user);
            unset($userGroup);
            unset($host);
            unset($hostGroup);
            unset($template);
            unset($action);
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
        <title>Electro-Line | Monitoring Portal</title>
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

        <link rel="shortcut icon" href="../../favicon.ico" />
    </head>
    <!-- END HEAD -->

    <!-- BEGIN BODY -->
    <body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white page-sidebar-fixed">

        <!-- BEGIN HEADER -->
        <div class="page-header navbar navbar-fixed-top">

            <!-- BEGIN HEADER INNER -->
            <div class="page-header-inner">

                <!-- BEGIN LOGO -->
                <div class="page-logo">
                    <a href="../../">
                        <img src="../../assets/layouts/layout/img/logo.png" alt="logo" class="logo-default"/>
                    </a>
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
                                    <a href=""><i class="icon-lock"></i> Lock Screen </a>
                                </li>
                                <li>
                                    <a href=""><i class="icon-key"></i> Log Out </a>
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
                    <ul class="page-sidebar-menu page-header-fixed" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" style="padding-top: 20px">
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
                                    <a href="" class="nav-link">
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
                                    <a href="" class="nav-link">
                                        <span class="title">Aanmaken</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="" class="nav-link">
                                        <span class="title">Verwijderen</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="" class="nav-link">
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
                                    <a href="" class="nav-link">
                                        <span class="title">Aanmaken</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="" class="nav-link">
                                        <span class="title">Verwijderen</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="" class="nav-link">
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
                                    <a href="" class="nav-link">
                                        <span class="title">Aanmaken</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="" class="nav-link">
                                        <span class="title">Verwijderen</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="" class="nav-link">
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
                                    <a href="../../wizard/new-customer/" class="nav-link">
                                        <span class="title">Klant aanmaken</span>
                                        <span class="selected"></span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="../../wizard/delete-customer/" class="nav-link">
                                        <span class="title">Klant verwijderen</span>
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

                    <!-- BEGIN PAGE BAR -->
                    <div class="page-bar">
                        <ul class="page-breadcrumb">
                            <li>
                                <a href="../../">Home</a>
                                <i class="fa fa-circle"></i>
                            </li>
                            <li>
                                <span>Klant aanmaken</span>
                            </li>
                        </ul>
                    </div>
                    <!-- END PAGE BAR -->

                    <!-- BEGIN PAGE TITLE-->
                    <h3 class="page-title">Voeg een nieuwe klant toe aan de monitoring omgeving</h3>
                    <!-- END PAGE TITLE-->
<?php
    if ($showMessageToUser)
    {
?>
                        <!-- BEGIN USER MESSAGE -->
                        <div class="col-md-12">
                            <div class="alert alert-info alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                                <h4 class="alert-heading"><b>Info</b></h4>
                                <p>
                                    <?php echo $messageToUser ?>
                                </p>
                            </div>
                        </div>
                        <!-- END USER MESSAGE -->
<?php
    }
?>
                    <!-- BEGIN FORMS -->
                    <div class="col-md-12">
                        <div class="portlet light bordered" id="customerWizard">
                            <div class="portlet-title">
                                <div class="caption font-red-sunglo">
                                    <i class="icon-layers font-red-sunglo"></i>
                                    <span class="caption-subject bold uppercase">
                                        Klanten Wizard - <span class="step-title"> Stap 1 van 3</span>
                                    </span>
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form id="form-create" class="form-horizontal" role="form" action="" method="post">
                                    <div class="form-wizard">
                                        <div class="form-body">
                                            <ul class="nav nav-pills nav-justified steps">
                                                <li class="active">
                                                    <a href="#tab1" data-toggle="tab" class="step" aria-expanded="true">
                                                        <span class="number">1</span>
                                                        <span class="desc">
                                                            <i class="fa fa-check"></i>
                                                            Initialisatie van de klant
                                                        </span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#tab2" data-toggle="tab" class="step">
                                                        <span class="number">2</span>
                                                        <span class="desc">
                                                            <i class="fa fa-check"></i>
                                                            Host gegevens
                                                        </span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#tab3" data-toggle="tab" class="step">
                                                        <span class="number">3</span>
                                                        <span class="desc">
                                                            <i class="fa fa-check"></i>
                                                            Host interface
                                                        </span>
                                                    </a>
                                                </li>
                                            </ul>
                                            <div id="bar" class="progress progress-striped" role="progressbar">
                                                <div class="progress-bar progress-bar-success"></div>
                                            </div>
                                            <div class="tab-content">

                                                <!-- BEGIN USER(GROUP) & HOSTGROUP SETTINGS -->
                                                <div class="tab-pane active" id="tab1">
                                                    <div class="form-group">
                                                        <label class=" control-label col-md-3" for="cbox_Customer">
                                                            Klant
                                                            <span class="required" aria-required="true"> * </span>
                                                        </label>
                                                        <div class="col-md-4">
                                                            <select name="cbox_Customer" id="cbox_Customer" class="form-control" >
                                                                <option value=""></option>
                                                                <?php
                                                                    foreach ($parser->GetUnprocessedCustomers() as $item)
                                                                    {
                                                                        if (isset($customer) && $customer->name == $item->name)
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
                                                        <label class="control-label col-md-3" for="tbox_User">Gebruiker</label>
                                                        <div class="col-md-4">
                                                            <input type="text" id="tbox_User" class="form-control" value="<?php echo(isset($user) ? ($user->name) : ("")); ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3" for="tbox_UserGroup">Gebruikersgroep</label>
                                                        <div class="col-md-4">
                                                            <input type="text" id="tbox_UserGroup" class="form-control" value="<?php echo(isset($userGroup) ? ($userGroup->name) : ("")); ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label" for="tbox_HostGroup">Host groep</label>
                                                        <div class="col-md-4">
                                                            <input type="text" id="tbox_HostGroup" class="form-control" value="<?php echo(isset($hostGroup) ? ($hostGroup->name) : ("")); ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label" for="tbox_Action">Actie</label>
                                                        <div class="col-md-4">
                                                            <input type="text" id="tbox_Action" class="form-control" value="<?php echo(isset($action) ? ($action->name) : ("")); ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3" for="tbox_Template">Template</label>
                                                        <div class="col-md-4">
                                                            <input type="text" id="tbox_Template" class="form-control" value="<?php echo(isset($template) ? ($template->name) : ("")); ?>" readonly>
                                                        </div>
                                                    </div>
<?php
    if (isset($customer))
    {
?>
                                                        <input type="hidden" id="hidden_code" value="<?php echo $customer->code ?>">
<?php
    }
?>
                                                </div>
                                                <!-- END USER(GROUP) & HOSTGROUP SETTINGS-->

                                                <!-- BEGIN HOST SETTINGS -->
                                                <div class="tab-pane" id="tab2">
                                                    <h3 class="block">Gegevens</h3>
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label" for="cbox_HostGroup">Groep</label>
                                                        <div class="col-md-4">
                                                            <input type="text" id="tbox_HostGroup" class="form-control" value="<?php echo(isset($hostGroup) ? ($hostGroup->name) : ("")); ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label" for="tbox_Prefix">
                                                            Prefix
                                                            <span class="required" aria-required="true"> * </span>
                                                        </label>
                                                        <div class="col-md-4">
                                                            <input type="text" name="tbox_Prefix" id="tbox_Prefix" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label" for="tbox_Host">
                                                            Host
                                                            <span class="required" aria-required="true"> * </span>
                                                        </label>
                                                        <div class="col-md-4">
                                                            <input type="text" name="tbox_Host" id="tbox_Host" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label" for="tbox_Preview">Voorbeeld</label>
                                                        <div class="col-md-4">
                                                            <input type="text" name="tbox_Preview" id="tbox_Preview" class="form-control" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- END HOST SETTINGS -->

                                                <!-- BEGIN HOST INTERFACE SETTINGS -->
                                                <div class="tab-pane" id="tab3">
                                                    <h3 class="block">Interface</h3>
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label"></label>
                                                        <div class="col-md-4">
                                                            <div class="checkbox-list">
                                                                <label>
                                                                    <span><input type="checkbox" name="chbox_DefaultInterface" checked></span>Standaard interface
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label"></label>
                                                        <div class="col-md-4">
                                                            <div class="btn-group" data-toggle="buttons">
                                                                <label class="btn green active">
                                                                    <input type="radio" name="rb_IpDns" id="rb_IP" class="toggle" checked>IP
                                                                </label>
                                                                <label class="btn green">
                                                                    <input type="radio" name="rb_IpDns" id="rb_DNS" class="toggle">DNS
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label" for="tbox_IP">IP</label>
                                                        <div class="col-md-4">
                                                            <input type="text" name="tbox_IP" id="tbox_IP" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label" for="tbox_DNS">DNS</label>
                                                        <div class="col-md-4">
                                                            <input type="text" name="tbox_DNS" id="tbox_DNS" class="form-control" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label" for="tbox_Port">
                                                            Poort
                                                            <span class="required" aria-required="true"> * </span>
                                                        </label>
                                                        <div class="col-md-4">
                                                            <input type="text" name="tbox_Port" id="tbox_Port" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label" for="cbox_Type">
                                                            Type
                                                            <span class="required" aria-required="true"> * </span>
                                                        </label>
                                                        <div class="col-md-4">
                                                            <select name="cbox_Type" id="cbox_Type" class="form-control" required>
                                                                <option value=""></option>
                                                                <?php
                                                                    foreach (InterfaceType::GetNames() as $item)
                                                                    {
                                                                        echo '<option value="' . $item . '">' . $item . '</option>';
                                                                    }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- END HOST INTERFACE SETTINGS -->

                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-md-offset-3 col-md-9">
                                                    <a href="javascript:;" class="btn default button-previous disabled" style="display: none;"><i class="fa fa-angle-left"></i>&nbsp;Terug</a>
                                                    <a href="javascript:;" class="btn green bold button-next">Volgende&nbsp;<i class="fa fa-angle-right"></i></a>
                                                    <button type="submit" name="btn_Submit" id="btn_Submit" class="btn green button-submit">Klaar&nbsp;<i class="fa fa-check"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
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
        <!-- END CORE PLUGINS -->

        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="../../assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
        <script src="../../assets/global/plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js" type="text/javascript"></script>
        <script src="../../assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js" type="text/javascript"></script>
        <script src="../../assets/global/plugins/jquery.input-ip-address-control-1.0.min.js" type="text/javascript"></script>
        <!-- END PAGE LEVEL PLUGINS -->

        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <script src="../../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <!-- END THEME GLOBAL SCRIPTS -->

        <!-- BEGIN THEME LAYOUT SCRIPTS -->
        <script src="../../assets/layouts/layout/scripts/layout.min.js" type="text/javascript"></script>
        <script src="../../assets/layouts/layout/scripts/demo.min.js" type="text/javascript"></script>
        <script src="../../assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
        <!-- END THEME LAYOUT SCRIPTS -->

        <script src="wizard.new-customer.script.js" type="text/javascript"></script>
    </body>

</html>
