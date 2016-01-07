<?php

/**
 * Created by PhpStorm.
 * User: developer
 * Date: 15/12/2015
 * Time: 9:29
 */

namespace WebServices;

    require_once dirname(dirname(__DIR__)) . '/include/ZabbixAPI.php';
    require_once dirname(dirname(__DIR__)) . "/include/ZabbixClasses.php";
    require_once dirname(dirname(__DIR__)) . '/include/CustomerParser.php';

    $parser = new CustomerParser();
    $zapi = new ZabbixAPI();
    $zapi->Authenticate();
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
                    <ul class="page-sidebar-menu  page-header-fixed " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="100" style="padding-top: 20px">
                        <li class="heading">
                            <h3 class="uppercase">Monitoring</h3>
                        </li>
                        <li class="nav-item">
                            <a href="../../dashboard/" class="nav-link">
                                <i class="fa fa-dashboard"></i>
                                <span class="title">Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item active open">
                            <a href="javascript:;" class="nav-link nav-toggle">
                                <i class="fa fa-user"></i>
                                <span class="title">Gebruikers</span>
                                <span class="selected"></span>
                                <span class="arrow open"></span>
                            </a>
                            <ul class="sub-menu">
                                <li class="nav-item">
                                    <a href="../create/" class="nav-link">
                                        <span class="title">Aanmaken</span>
                                    </a>
                                </li>
                                <li class="nav-item active open">
                                    <a href="" class="nav-link">
                                        <span class="title">Verwijderen</span>
                                        <span class="selected"></span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="../overview/" class="nav-link">
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
                                    <a href="../../users/overview" class="nav-link">
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
                                    <a href="../../hosts/overview" class="nav-link">
                                        <span class="title">Overzicht</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="javascript:;" class="nav-link nav-toggle">
                                <i class="fa fa-magic"></i>
                                <span class="title">Wizard</span>
                                <span class="arrow"></span>
                            </a>
                            <ul class="sub-menu">
                                <li class="nav-item">
                                    <a href="../../wizard/new-customer/" class="nav-link">
                                        <span class="title">Klant aanmaken</span>
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
                                <span>Remove user</span>
                            </li>
                        </ul>
                    </div>
                    <!-- END PAGE BAR -->

                    <!-- BEGIN PAGE TITLE-->
                    <h3 class="page-title">Remove a user from the monitoring environment</h3>
                    <!-- END PAGE TITLE-->

                    <!-- BEGIN FORMS -->
                    <div class="col-md-12">

                        <!-- BEGIN PORTLET SETTINGS -->
                        <div class="portlet light bordered">

                            <!-- BEGIN PORTLET TITLE -->
                            <div class="portlet-title">
                                <div class="caption font-red-sunglo">
                                    <i class="icon-user font-red-sunglo"></i>
                                    <span class="caption-subject bold uppercase">Users by customer</span>
                                </div>
                            </div>
                            <!-- END PORTLET TITLE -->

                            <!-- BEGIN PORTLET BODY -->
                            <div class="portlet-body form">

                                <!-- BEGIN FORM -->
                                <form id="form-deleteUser" class="form-horizontal" role="form" action="" method="post">

                                    <!-- BEGIN CUSTOMER SELECTION -->
                                    <div class="form-group">
                                        <label class=" control-label col-md-3" for="cbox_Customer">
                                            Customer
                                            <span class="required" aria-required="true">&nbsp;*</span>
                                        </label>
                                        <div class="col-md-4">
                                            <select name="cbox_Customer" id="cbox_Customer" class="form-control" >
                                                <option value=""></option>
                                                <?php
                                                    foreach ($parser->GetProcessedCustomers() as $item)
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
                                    <!-- END CUSTOMER SELECTION -->

                                    <!-- BEGIN USER SELECTION -->
                                    <div class="form-group">
                                        <label class="col-md-3 control-label" for="cbox_User">Gebruiker</label>
                                        <div class="col-md-4">
                                            <select name="cbox_User" id="cbox_User" class="form-control">
                                                <option value=""></option>
                                                <?php
                                                    foreach ($zapi->GetUsersByUserGroup($userGroupName) as $item)
                                                    {
                                                        echo "<option value='" . $item->id ."'>". $item->name . "</option>";
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- END USER SELECTION -->

                                    <!-- BEGIN FORM ACTIONS -->
                                    <div class="form-actions">
                                        <div class="row">
                                            <div class="col-md-offset-3 col-md-9">
                                                <button type="reset" class="btn btn-default">Cancel</button>
                                                <button name="submit_Delete" class="btn bold red">Remove</button>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- END FORM ACTIONS -->

                                </form>
                                <!-- BEGIN FORM -->

                            </div>
                            <!-- END PORTLET BODY -->

                        </div>
                        <!-- END USER(GROUP) & HOSTGROUP SETTINGS-->

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

        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <script src="../../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <!-- END THEME GLOBAL SCRIPTS -->

        <!-- BEGIN THEME LAYOUT SCRIPTS -->
        <script src="../../assets/layouts/layout/scripts/layout.min.js" type="text/javascript"></script>
        <script src="../../assets/layouts/layout/scripts/demo.min.js" type="text/javascript"></script>
        <script src="../../assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
        <!-- END THEME LAYOUT SCRIPTS -->

    </body>
    <!-- END BODY -->

</html>