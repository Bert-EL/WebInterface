<?php

/**
 * Created by PhpStorm.
 * User: developer
 * Date: 15/12/2015
 * Time: 9:29
 */
namespace WebServices;

class NewCustomer
{
    #region Constants

    /**
     * Contains the default password.
     */
    const DEFAULT_PASSWORD = "Temp*123";

    #endregion

    #region Private Fields

    /**
     * Gets or sets the reference to the Zabbix API.
     */
    private $Zapi;

    #endregion

    #region Public Fields

    /**
     * @var     int                 Gets the ID of the new user.
     */
    public $UserID = 0;

    /**
     * @var     int                 Gets the ID of the new user group.
     */
    public $UserGroupID = 0;

    /**
     * @var     int                 Gets the ID of the new host group.
     */
    public $HostGroupID = 0;

    #endregion

    #region Constructor

    /**
     * NewCustomer constructor.
     */
    public function __construct()
    {
        $this->Zapi = new ZabbixAPI();
    }

    #endregion

    #region Public Functions

    /**
     * Create a new customer that'll be used in the Zabbix portal.
     * This will create a new user, user group and host group.
     *
     * @param   string  $name       The name of the customer that'll be created in the Zabbix portal.
     * @param   int     $code       The customer's code.
     * @return  bool                True if the customer has been created succesfully; otherwise false.
     */
    public function CreateNewCustomer($name, $code)
    {
        if ($this->Zapi->IsValidAuthToken() && is_string($name) && is_int($code))
        {
            $this->HostGroupID = $this->Zapi->CreateHostGroupByCode($name, $code);
            $this->UserGroupID = $this->Zapi->CreateUserGroupByCode($name, $code, $this->HostGroupID, Permission::ReadOnlyAccess);
            $this->UserID = $this->Zapi->CreateUserByCode($code, NewCustomer::DEFAULT_PASSWORD, $this->UserGroupID);
        }

        return ($this->UserID > 0);
    }

    public function EvaluateInterfaceFields()
    {
        $result = false;
//        $useip = document.getElementById("chbox_UseIP").checked;
//        $ip = document.getElementById("tbox_IP").value;
//        $dns = document.getElementById("tbox_DNS").value;
//        $port = document.getElementById("tbox_Port").value;
//
//        $result &= (useip && !IsEmpty(ip)) || (!useip && !IsEmpty(dns));
//        $result &= !IsEmpty(port);

        return $result;
    }

    #endregion
}