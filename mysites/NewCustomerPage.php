<?php

/**
 * Created by PhpStorm.
 * User: developer
 * Date: 15/12/2015
 * Time: 9:29
 */

namespace WebServices;

    require_once dirname(__DIR__) . '/myphp/ZabbixAPI.php';
    require_once dirname(__DIR__) . '/myphp/CustomerParser.php';
    require_once dirname(__FILE__) . '/NewCustomer.php';

    $zapi = new ZabbixAPI();
    $zapi->Authenticate();

    $parser = new CustomerParser();

    var_dump($_REQUEST);
    var_dump($parser->EvaluateExportDate());

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
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <title>Nieuwe klant</title>
        <link rel="stylesheet" type="text/css" href="ScriptStyle.css">
        <script type="text/javascript" src="../js/jquery/jquery.js"></script>
        <script type="text/javascript" src="Customer/Scripts.js"></script>
    </head>

    <body>

        <h3>Voeg een nieuwe klant toe aan de monitoring omgeving</h3>

        <h4>Gebruiker, gebruikersgroep en host groep</h4>

        <form id="fInit" action="#" method="post">
            <div class="table">
                <div class="tr">
                    <div class="th"><label for="cbox_Customer"><strong>Klant</strong></label></div>
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
                    <div class="td"><label for="tbox_User"><strong>User</strong></label></div>
                    <div class="td">
                        <input class=field id="tbox_User" type="text" value="<?php echo $newUsername ?>" disabled>
                        <input type="hidden" name="newUser" value="<?php echo $newUsername ?>">
                    </div>
                </div>
                <div class="tr">
                    <div class="td"><label for="tbox_UserGroup"><strong>User group</strong></label></div>
                    <div class="td">
                        <input class=field id="tbox_UserGroup" type="text" value="<?php echo $newUserGroupName ?>" disabled>
                        <input type="hidden" name="newUserGroup" value="<?php echo $newUserGroupName ?>">
                    </div>
                </div>
                <div class="tr">
                    <div class="td"><label for="tbox_HostGroup"><strong>Host group</strong></label></div>
                    <div class="td">
                        <input class=field id="tbox_HostGroup" type="text" value="<?php echo $newHostGroupName ?>" disabled>
                        <input type="hidden" name="newHostGroup" value="<?php echo $newHostGroupName ?>">
                    </div>
                    <div class="td"><input name="submit_fInit" class="button" type="submit" value="Aanmaken"></div>
                </div>

                <input name="code" type="hidden" id="hidden_code" value="<?php echo $customer->code ?>">
            </div>
        </form>

        <br/><hr/>

        <h4>Host</h4>

        <h5>Gegevens</h5>

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

            <h5>Interface</h5>

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
                    <div class="td"><input name="submit_fHost" type="submit" class="button" value="Aanmaken"></div>
                </div>
            </div>
        </form>

    </body>
</html>