<?php

/**
 * Created by PhpStorm.
 * User: developer
 * Date: 29/12/2015
 * Time: 8:51
 */

namespace WebServices;

    require_once dirname(dirname(__DIR__)) . "/include/ZabbixAPI.php";
    require_once dirname(dirname(__DIR__)) . "/include/ZabbixClasses.php";
    require_once dirname(dirname(__DIR__)) . "/include/CustomerParser.php";
    require_once "delete-customer.php";

    $parser = new CustomerParser();
    $zapi = new ZabbixAPI();
    $zapi->Authenticate();

    $messageToUser = "";
    $showMessageToUser = false;

    if (isset($_REQUEST["cbox_Customer"]))
    {
        $customerName = $_REQUEST["cbox_Customer"];
        $customerCode = $parser->GetCodeByName($customerName);
        $customer = Customer::WithNameAndCode($customerName, $customerCode);

        $hostGroupName = $zapi->FormatGroupName(NameType::HostGroup, $customer);
        $hostGroup = HostGroup::WithNameAndID($hostGroupName, $zapi->GetHostGroupID($hostGroupName));

        $userGroupName = $zapi->FormatGroupName(NameType::UserGroup, $customer);
        $userGroup = UserGroup::WithNameAndID($userGroupName, $zapi->GetUserGroupID($userGroupName));

        $template = $zapi->GetTemplateByName($zapi->GetTemplateAlias($customer));

        $actionName = $zapi->FormatGroupName(NameType::Action, $customer);
        $action = $zapi->GetActionByName($actionName);

        if (isset($_REQUEST["btn_Submit"]))
        {
            $showMessageToUser = true;
            $deleteCustomer = new DeleteCustomer($zapi);

            if (isset($_REQUEST["chbox_Template"]) && $_REQUEST["chbox_Template"] === "on")                                 // If true; delete the customer template.
            {
                $messageToUser .= $deleteCustomer->DeleteCustomerTemplate($template->id);
            }

            if (isset($_REQUEST["chbox_UserGroup"]) && $_REQUEST["chbox_UserGroup"] === "on")                               // If true; delete all the users in the user group and the group itself.
            {
                $messageToUser .= $deleteCustomer->DeleteCustomerUserGroup($userGroup->id);
            }
            elseif (isset($_REQUEST["s2_Users"]) && count($_REQUEST["s2_Users"]) > 0)                                       // If true; delete all users in the user group when ID #0 is specified. Otherwise delete all specified users.
            {
                $users = $_REQUEST["s2_Users"];
                $messageToUser .= (in_array(0, $users)) ? ($deleteCustomer->DeleteAllCustomerUsers($userGroup->id)) : ($deleteCustomer->DeleteCustomerUsers($users));
            }

            if (isset($_REQUEST["chbox_HostGroup"]) && $_REQUEST["chbox_HostGroup"] === "on")                               // If true; delete all the hosts in the host group and the group itself.
            {
                $messageToUser .= $deleteCustomer->DeleteCustomerHostGroup($hostGroup->id);
            }
            elseif (isset($_REQUEST["s2_Hosts"]) && count($_REQUEST["s2_Hosts"]) > 0)                                       // If true; delete all hosts in the host group when ID #0 is specified. Otherwise delete all specified hosts.
            {
                $hosts = $_REQUEST["s2_Hosts"];
                $messageToUser .= (in_array(0, $hosts)) ? ($deleteCustomer->DeleteAllCustomerHosts($hostGroup->id)) : ($deleteCustomer->DeleteCustomerHosts($hosts));
            }

            if (isset($_REQUEST["chbox_Action"]) && $_REQUEST["chbox_Action"] === "on" && !is_null($action))                // If true; delete the customer action.
            {
                $messageToUser .= $deleteCustomer->DeleteCustomerAction($action->id);
            }

            unset($customer);
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

        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <link href="../../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css">
        <link href="../../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css">
        <!-- END PAGE LEVEL PLUGINS -->

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
                                <a href=""><i class="icon-lock"></i>Lock Screen</a>
                            </li>
                            <li>
                                <a href=""><i class="icon-key"></i>Log Out</a>
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
                                <a href="../../user/overview/" class="nav-link">
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
                                <a href="../../users/create/" class="nav-link">
                                    <span class="title">Aanmaken</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../../users/delete/" class="nav-link">
                                    <span class="title">Verwijderen</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../../users/overview/" class="nav-link">
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
                                <a href="../../host/create/" class="nav-link">
                                    <span class="title">Aanmaken</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../../host/delete/" class="nav-link">
                                    <span class="title">Verwijderen</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../../host/overview/" class="nav-link">
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
                                <a href="../../hosts/create/" class="nav-link">
                                    <span class="title">Aanmaken</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../../hosts/delete/" class="nav-link">
                                    <span class="title">Verwijderen</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../../hosts/overview/" class="nav-link">
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
                            <li class="nav-item">
                                <a href="../../wizard/new-customer/" class="nav-link">
                                    <span class="title">Klant aanmaken</span>
                                </a>
                            </li>
                            <li class="nav-item active open">
                                <a href="../../wizard/delete-customer/" class="nav-link">
                                    <span class="title">Klant verwijderen</span>
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

                <!-- BEGIN PAGE BAR -->
                <div class="page-bar">
                    <ul class="page-breadcrumb">
                        <li>
                            <a href="../../">Home</a>
                            <i class="fa fa-circle"></i>
                        </li>
                        <li>
                            <span>Klant verwijderen</span>
                        </li>
                    </ul>
                </div>
                <!-- END PAGE BAR -->

                <!-- BEGIN PAGE TITLE-->
                <h3 class="page-title">Verwijder een klant uit de monitoring omgeving</h3>
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
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption font-red-sunglo">
                                <i class="fa fa-trash font-red-sunglo"></i>
                                <span class="caption-subject bold uppercase">
                                    Klant verwijderen
                                </span>
                            </div>
                        </div>
                        <div class="portlet-body form">

                            <!-- BEGIN FORM -->
                            <form id="form-delete" class="form-horizontal" role="form" action="" method="post">

                                <!-- BEGIN FORM BODY -->
                                <div class="form-body">

                                    <!-- BEGIN CUSTOMER SELECT -->
                                    <div class="form-group">
                                        <label class="control-label col-md-3" for="cbox_Customer">
                                            Klant
                                            <span class="required" aria-required="true"> * </span>
                                        </label>
                                        <div class="col-md-4">
                                            <select name="cbox_Customer" id="cbox_Customer" class="form-control" required>
                                                <option value=""></option>
                                                <?php
                                                    foreach ($parser->GetProcessedCustomers() as $item)
                                                    {
                                                        if (isset($customer) && $customer->name == $item->name)
                                                        {
                                                            echo "<option value='" . $item->name . "' selected>" . $item->name . "</option>";
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
                                    <!-- END CUSTOMER SELECT -->

                                    <!-- BEGIN DATA SELECT -->
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Te verwijderen gegevens</label>
                                        <div class="col-md-4">
                                            <div class="checkbox-list">
                                                <label><input type="checkbox" name="chbox_UserGroup" id="chbox_UserGroup" checked>User Group</label>
                                                <label><input type="checkbox" name="chbox_HostGroup" id="chbox_HostGroup" checked>Host Group</label>
                                                <label><input type="checkbox" name="chbox_Template" id="chbox_Template" checked>Template</label>
                                                <label><input type="checkbox" name="chbox_Action" id="chbox_Action" checked>Action</label>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- END DATA SELECT -->

                                    <!-- BEGIN USER SELECT -->
                                    <div class="form-group">
                                        <label for="s2_Users" class="col-md-3 control-label">Gebruikers</label>
                                        <div class="col-md-4">
                                            <select name="s2_Users[]" id="s2_Users" class="form-control select2-multiple" multiple="multiple" disabled>
                                                <?php
                                                    if (isset($userGroupName))
                                                    {
                                                        echo "<option value='0'>Alle gebruikers</option>";

                                                        foreach ($zapi->GetUsersByUserGroup($userGroupName) as $item)
                                                        {
                                                            echo "<option value='" . $item->id ."'>". $item->name . "</option>";
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- END USER SELECT -->

                                    <!-- BEGIN HOST SELECT -->
                                    <div class="form-group">
                                        <label for="s2_Hosts" class="col-md-3 control-label">Hosts</label>
                                        <div class="col-md-4">
                                            <select name="s2_Hosts[]" id="s2_Hosts" class="form-control select2-multiple" multiple="multiple" disabled>
                                                <?php
                                                    if (isset($hostGroupName))
                                                    {
                                                        echo "<option value='0'>Alle hosts</option>";

                                                        foreach ($zapi->GetHostsByHostGroup($hostGroupName) as $item)
                                                        {
                                                            echo "<option value='" . $item->id ."'>". $item->name . "</option>";
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- END HOST SELECT -->

                                </div>
                                <!-- END FORM BODY-->

                                <!-- BEGIN FORM ACTIONS -->
                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-md-offset-3 col-md-9">
                                            <button type="button" id="btn_Clear" class="btn btn-default">Annuleren</button>
                                            <button type="submit" name="btn_Submit" class="btn bold red">Verwijderen</button>
                                        </div>
                                    </div>
                                </div>
                                <!-- END FORM ACTIONS -->

                            </form>
                            <!-- END FORM -->
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
    <script src="../../assets/global/plugins/select2/js/select2.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS-->

    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="../../assets/global/scripts/app.min.js" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->

    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="../../assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js" type="text/javascript"></script>
    <script src="wizard.delete-customer.script.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL SCRIPTS -->

    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="../../assets/layouts/layout/scripts/layout.min.js" type="text/javascript"></script>
    <script src="../../assets/layouts/layout/scripts/demo.min.js" type="text/javascript"></script>
    <script src="../../assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
    <!-- END THEME LAYOUT SCRIPTS -->

    </body>

</html>
