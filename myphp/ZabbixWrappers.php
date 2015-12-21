<?php
/**
 * Created by PhpStorm.
 * User: developer
 * Date: 2/12/2015
 * Time: 14:51
 */

namespace WebServices
{
    class ZabbixWrapper
    {
        public $jsonrpc;
        public $method;
        public $params;
        public $id;
        public $auth;

        /**
         * ZabbixWrapper constructor.
         */
        public function __construct()
        {
            $this->jsonrpc = "2.0";
            $this->auth = null;
        }

        public function Create($method, $authToken = null, $id = 1)
        {
            $this->method = $method;
            $this->auth = $authToken;
            $this->id = $id;
        }
    }

    class ZabbixRequest
    {
        public function __construct()
        { }
    }

    class ZabbixAuthRequest extends ZabbixRequest
    {
        public $user;
        public $password;

        public function __construct($username, $password)
        {
            $this->user = $username;
            $this->password = $password;
        }
    }

    class ZabbixHostRequest extends ZabbixRequest
    {
        public $hostids;
        public $output;
        public $selectInterfaces;
        public $filter;

        public function __construct()
        { }
    }

    class ZabbixGroupDoesExist extends ZabbixRequest
    {
        public $name;

        public function __construct($group)
        {
            $this->name = $group;
        }
    }

    class ZabbixHostGroupRequest extends ZabbixRequest
    {
        public $name;
//        public $groupids;
//        public $output;

        public function __construct($name)
        {
            $this->name = $name;
        }
    }

    class ZabbixUserCreateRequest extends ZabbixRequest
    {
        public $alias;
        public $passwd;
        public $usrgrps;
        public $autologin;
        public $autologout;
        public $type;
        public $name;
        public $surname;
        public $lang;

        public function __construct()
        { }
    }

    class ZabbixUserGetRequest extends ZabbixRequest
    {
        public $output;
    }

    class ZabbixUserGroupGetRequest extends ZabbixRequest
    {
        public $status;
        public $output;
    }

    class ZabbixHostGroupGetObjectRequest extends ZabbixRequest
    {
        public $name;

        public function __construct($hostGroupName)
        {
            $this->name = $hostGroupName;
        }
    }

    class ZabbixUserGroupGetObjectRequest extends ZabbixRequest
    {
        public $name;

        public  function __construct($userGroupName)
        {
            $this->name = $userGroupName;
        }
    }

    class ZabbixUserGroupCreateRequest extends ZabbixRequest
    {
        public $name;
        public $rights;
        public $userids;
    }

    class ZabbixUserGroupUpdateRequest extends ZabbixRequest
    {
        public $usrgrpid;
        public $rights = array("permission" => "", "id" => "");
    }

    class ZabbixHostCreateRequest extends ZabbixRequest
    {
        public $host;
        public $interfaces;
        public $groups = array("groupid" => "");
    }

    class ZabbixHostGroupGetRequest extends ZabbixRequest
    {
        public $groupids;
        public $output;

        public function __construct()
        { }
    }

    class ZabbixTemplateGetRequest extends ZabbixRequest
    {
        public $hostids;
        public $output;

        public function  __construct()
        { }
    }

    class ZabbixHostTemplateUpdateRequest extends ZabbixRequest
    {
        public $hostid;
        public $templates;

        public function  __construct()
        { }
    }

    class ZabbixTemplateCreateRequest extends ZabbixRequest
    {
        public $host;
        public $groups = null;
        public $hosts = null;

        /**
         * Set the host that'll be linked to the template.
         *
         * @param   array       $hosts          An array of host IDs.
         */
        public function SetHosts($hosts)
        {
            $this->hosts = array();

            if (is_array($hosts) && count($hosts) > 0)
            {
                foreach ($hosts as $item)
                {
                    $this->hosts[] = new ZabbixTemplateCreateHosts($item);
                }
            }
        }

        /**
         * Set the host groups that'll be assigned the template.
         *
         * @param   array       $groups         An array of host group IDs.
         */
        public function SetGroups($groups)
        {
            $this->groups = array();

            if (is_array($groups) && count($groups) > 0)
            {
                foreach ($groups as $item)
                {
                    $this->groups[] = new ZabbixTemplateCreateGroups($item);
                }
            }
        }
    }

    class ZabbixTemplateCreateGroups
    {
        public $groupid;

        public function  __construct($id)
        {
            if (is_int($id))
            {
                $this->groupid = (string)$id;
            }
        }
    }

    class ZabbixTemplateCreateHosts
    {
        public $hostid;

        public function  __construct($id)
        {
            if (is_int($id))
            {
                $this->hostid = (string)$id;
            }
        }
    }

    class ZabbixActionCreateRequest
    {
        /**
         * @var     string                  Name of the action.
         */
        public $name = "";

        /**
         * @var     int                     Whether the action is enabled or disabled.
         *
         * Possible values:
         *      0 - (default) enabled;
         *      1 - disabled.
         */
        public $status = 0;

        /**
         * @var     object                  Action filter object for the action.
         */
        public $filter;

        /**
         * @var     string                  Problem message subject.
         */
        public $def_shortdata = "";

        /**
         * @var     string                  Problem message text.
         */
        public $def_longdata = "";

        /**
         * @link    https://www.zabbix.com/documentation/2.2/manual/api/reference/event/object#event
         * @var     int                     Type of events that the action will handle.
         *
         * Possible values:
         *      0 - event created by a trigger;
         *      1 - event created by a discovery rule;
         *      2 - event created by active agent auto-registration;
         *      3 - internal event.
         */
        public $eventsource = 2;

        /**
         * @var     int                     Default operation step duration. Must be greater than 60 seconds.
         */
        public $esc_period = 0;

        /**
         *
         * @var     array                    The action operation object defines an operation that will be performed when an action is executed.
         */
        public $operations = array();

        /**
         * ZabbixActionCreateRequest constructor.
         */
        public function __construct()
        {
            $this->filter = new ZabbixActionCreateFilter();
        }
    }

    class ZabbixActionCreateConditions
    {
        /**
         * @link    https://www.zabbix.com/documentation/2.2/manual/api/reference/action/object#action
         * @var     int                     Type of condition.
         *
         * Possible values for auto-registration actions:
         *      20 - proxy;
         *      22 - host name;
         *      24 - host metadata.
         */
        public $conditiontype = 0;

        /**
         * @var int                         Condition operator.
         *
         * Possible values:
         *      2 - like;
         *      3 - not like;
         */
        public $operator = 0;

        /**
         * @var     string                  Value to compare with.
         */
        public $value = "";
    }

    class ZabbixActionCreateFilter
    {
        /**
         * @var     int                     Action condition evaluation method.
         *
         * Possible values:
         *      0 - AND / OR;
         *      1 - AND;
         *      2 - OR.
         */
        public $evaltype = 0;

        /**
         * @var     array                   The action condition object defines a condition that must be met to perform the configured action operations
         */
        public $conditions = array();
    }

    class ZabbixActionCreateOperations
    {
        /**
         * @var     int                     Type of operation.
         *
         * Possible values:
         *      0 - send message;
         *      1 - remote command;
         *      2 - add host;
         *      3 - remove host;
         *      4 - add to host group;
         *      5 - remove from host group;
         *      6 - link to template;
         *      7 - unlink from template;
         *      8 - enable host;
         *      9 - disable host
         */
        public $operationtype = 0;

        /**
         * @var     object                  Host groups to add hosts to.
         */
        public $opgroup;

        /**
         * @var     object                  Templates to link the hosts to to.
         */
        public $optemplate;
    }

    class ZabbixActionGetRequest
    {
        public $actionids;
//        public $output = "extend";
//        public $selectOperations = "extend";
        public $selectFilter = "extend";
        public $selectConditions = "extend";
        public $filter;

        public function __construct()
        {
            $this->filter = new ZabbixActionGetFilter();
        }
    }

    class ZabbixActionGetFilter
    {
        /**
         * @link    https://www.zabbix.com/documentation/2.2/manual/api/reference/event/object#event
         * @var     int                     Type of events that the action will handle.
         *
         * Possible values:
         *      0 - event created by a trigger;
         *      1 - event created by a discovery rule;
         *      2 - event created by active agent auto-registration;
         *      3 - internal event.
         */
        public $eventsource = 0;
    }

    class ZabbixUserLoginResponse
    {
        public $jsonrpc;
        public $result;
        public $id;

        public function __construct()
        { }
    }

    class ZabbixHostCreateResponse
    {
        public $jsonrpc;
        public $result;
        public $id = array("hostids" => "");
    }

    class ZabbixHostGroupCreateResponse
    {
        public $jsonrpc;
        public $result = array("groupsids" => "");
        public $id;
    }

    class ZabbixHostGetResponse
    {
        public $jsonrpc;
        public $result = array("host" => "", "hostid" => "");
        public $id;
    }

    class ZabbixHostGroupGetResponse
    {
        public $jsonrpc;
        public $result = array("groupid" => "", "name" => "");
        public $id;
    }

    class ZabbixUserGroupGetResponse
    {
        public $jsonrpc;
        public $result = array("usrgrpid" => "", "name" => "", "gui_access" => "", "users_status" => "", "debug_mode" => "");
        public $id;
    }

    class ZabbixUserCreateResponse
    {
        public $jsonrpc;
        public $result = array("userids" => "");
        public $id;
    }

    class ZabbixUserGroupCreateResponse
    {
        public $jsonrpc;
        public $result = array("usrgrpids" => "");
        public $id;
    }

    class ZabbixHostGroupGetObjectResponse
    {
        public $jsonrpc;
        public $result = array("groupid" => "", "name" => "", "internal" => "");
        public $id;
    }

    class ZabbixUserGroupGetObjectResponse
    {
        public $jsonrpc;
        public $result = array("usrgrpid" => "", "name" => "", "gui_acces" => "", "users_status" => "", "debug_mode" => "");
        public $id;
    }

    class ZabbixUserGetResponse
    {
        public $id;
        public $jsonrpc;

        public $result = array
        (
            "userid" => "",
            "name" => "",
            "surname" => "",
            "url" => "",
            "autologin" => "",
            "autologout" => "",
            "refresh" => "",
            "type" => "",
            "theme" => "",
            "attempt_failed" => "",
            "attempt_clock" => "",
            "rows_per_page" => ""
        );
    }

    class ZabbixTemplateGetResponse
    {
        public $jsonrpc;
        public $result;
        public $templateid = [];
        public $id;

        public function  __construct($json = false)
        {
            if ($json)
            {
                $this->Set($json);
            }
        }

        public function Set($data)
        {
            foreach ($data as $key => $value)
            {
                if (is_array($value))
                {
                    $sub = new ZabbixTemplateGetResponse();
                    $sub->Set($value);
                    $value = $sub;
                }

                $this->{$key} = $value;
            }
        }
    }

    /**
     * Class BaseItem
     *
     * Provide a base class for name and ID items.
     *
     * @package WebServices
     */
    abstract class BaseItem
    {
        #region Public Fields

        /**
         * @var     string                      Gets or sets the name of the item.
         */
        public $name = "";

        /**
         * @var     int                         Gets or sets the ID of the item.
         */
        public $id = 0;

        /**
         * BaseItem constructor.
         *
         * Initialize a new instance of the BaseItem class.
         *
         * @param   string      $name           The name of the item.
         * @param   int         $ID             The ID of the item.
         */
        public function __construct($name, $ID)
        {
            $this->name = $name;
            $this->id = $ID;
        }

//        public static function WithNameAndID($name, $ID)
//        {
//            $instance = new self();
//            $instance->name = $name;
//            $instance->id = $ID;
//
//            return $instance;
//        }
    }

    /**
     * Class User
     *
     * Provides a class that represents a User.
     *
     * @package WebServices
     */
    class User extends BaseItem
    {
        /**
         * User constructor.
         *
         * Initialize a new instance of the User class.
         *
         * @param   string      $name           The name of the user.
         * @param   int         $ID             The ID of the user.
         */
        public function __construct($name, $ID)
        {
            parent::__construct($name, $ID);
        }
    }

    /**
     * Class UserGroup
     *
     * Provides a class that represents a UserGroup.
     *
     * @package WebServices
     */
    class UserGroup extends BaseItem
    {
        /**
         * UserGroup constructor.
         *
         * Initialize a new instance of the UserGroup class.
         *
         * @param   string      $name           The name of the usergroup.
         * @param   int         $ID             The ID of the usergroup.
         */
        public function __construct($name, $ID)
        {
            parent::__construct($name, $ID);
        }
    }

    /**
     * Class Host
     *
     * Provides a class that represents a Host.
     *
     * @package WebServices
     */
    class Host extends BaseItem
    {
        /**
         * Host constructor.
         *
         * Initialize a new instance of the Host class.
         *
         * @param   string      $name           The name of the Host.
         * @param   int         $ID             The ID of the Host.
         */
        public function __construct($name, $ID)
        {
            parent::__construct($name, $ID);
        }
    }

    /**
     * Class HostGroup
     *
     * Provides a class that represents a Host Group.
     *
     * @package WebServices
     */
    class HostGroup extends BaseItem
    {
        /**
         * HostGroup constructor.
         *
         * Initialize a new instance of the HostGroup class.
         *
         * @param   string      $name           The name of the hostgroup.
         * @param   int         $ID             The ID of the hostgroup.
         */
        public function __construct($name, $ID)
        {
            parent::__construct($name, $ID);
        }
    }

    /**
     * Class Template
     *
     * Provides a class that represents a Template.
     *
     * @package WebServices
     */
    class Template extends BaseItem
    {
        /**
         * Template constructor.
         *
         * Initialize a new instance of the Template class.
         *
         * @param   string      $name           The name of the template.
         * @param   int         $ID             The ID of the template.
         */
        public function __construct($name, $ID)
        {
            parent::__construct($name, $ID);
        }
    }

    /**
     * Class HostInterface
     * Provides a class to the user that facilitates the interface assignment of a host.
     *
     * @package WebServices
     */
    class HostInterface
    {
        /**
         * @var string                  Gets or sets the DNS name used by the interface.
         *
         * Only set this property when 'useIP' is false.
         */
        public $dns = "";

        /**
         * @var string                  Gets or sets the IP address used by the interface.
         *
         * Only set this property when 'useIP is true.
         */
        public $ip = "";

        /**
         * @var int                     Gets or sets whether the interface is used as default on the host.
         *
         * Only one interface of some type can be set as default on a host.
         *
         *  Possible values:
         *      (0) = not default;
         *      (1) = default;
         */
        public $main = 0;

        /**
         * @var int                     Gets or sets the port number used by the interface.
         *
         * This property can contain user macros.
         */
        public $port = 0;

        /**
         * @var int                     Gets or sets the interface type.
         *
         * Possible values are:
         *      (1) = Agent;
         *      (2) = SNMP;
         *      (3) = IPMI;
         *      (4) = JMX;
         */
        public $type = 0;

        /**
         * @var int                     Gets or sets whether the connection should be made via IP.
         *
         * Possible values are:
         *      (0) = connect using host DNS name;
         *      (1) = connect using host IP address;
         */
        public $useip;

        /**
         * HostInterface constructor.
         *
         * @param   int     $type       The interface that'll be created.
         * @param   bool    $isDefault  Whether the interface should be default.
         * @param   bool    $useIP      Whether the interface should work by IP.
         */
        public function __construct($type, $isDefault, $useIP)
        {
            $this->type = (int)$type;
            $this->main = ($isDefault) ? (1) : (0);
            $this->useip = ($useIP) ? (1) : (0);

            if ($useIP)
            {
                $this->dns = "";
            }
            else
            {
                $this->ip = "";
            }
        }
    }
}