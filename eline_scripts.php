<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once dirname(__FILE__) . '/myphp/ZabbixAPI.php';
require_once dirname(__FILE__) . '/myphp/ZabbixWrappers.php';
require_once dirname(__FILE__) . '/myphp/CustomerParser.php';

session_start();

$zapi = new WebServices\ZabbixAPI();
$parser = new \WebServices\CustomerParser();

$api = filter_input(INPUT_POST, "API");
$action = filter_input(INPUT_POST, "action");
$value = filter_input(INPUT_POST, "value");

//var_dump("API: " . $api);
//var_dump("Action: " . $action);
//var_dump("Value: " . $value);

if (!is_null($action))
{
    if (!isset($_SESSION["authtoken"]))
    {
        if ($action == "Authenticate")
        {
            $_SESSION["authtoken"] = $zapi->Authenticate();
            echo $_SESSION["authtoken"];
        }
    }
    else
    {
        if ($api == "Zabbix")
        {
            if (method_exists($zapi, $action))
            {
                var_dump($zapi->$action());                                                                                     // !!!
            }
        }
        else
        {
            if (method_exists($parser, $action))
            {
                if (is_null($value))
                {
                    $result = $parser->$action();
                    //                var_dump($result);
                    echo $result;
                }
                else
                {
                    $result = $parser->$action($value);
                    //                var_dump($result);
                    echo $result;
                }
            }
        }
    }
}

?>