<?php

/**
 * Created by PhpStorm.
 * User: developer
 * Date: 2/12/2015
 * Time: 14:51
 */

namespace WebServices
{
    #region Base Class

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

    #endregion

    #region User Requests

    class ZabbixAuthRequest
    {
        public $user;
        public $password;

        public function __construct($username, $password)
        {
            $this->user = $username;
            $this->password = $password;
        }
    }

    class ZabbixUserCreateRequest
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

    class ZabbixUserGetRequest
    {
        public $output;
        public $usrgrpids;

        /**
         * Filter settings for the user GET request.
         *
         * @var ZabbixUserGetFilter|null
         */
        public $filter;
    }

    class ZabbixUserGetFilter
    {
        public $alias;
        public $userid;

        public function __construct()
        {   }

        public function FilterOnAlias($alias)
        {
            if (is_string($alias))
            {
                $this->alias = $alias;
            }
        }

        public function FilterOnUserID($id)
        {
            if (is_int($id))
            {
                $this->userid = $id;
            }
        }
    }

    #endregion

    #region UserGroup Requests

    /**
     * Class ZabbixUserGroupGetRequest
     *
     * @package WebServices
     */
    class ZabbixUserGroupGetRequest
    {
        public $status;
        public $output;
        public $usrgrpids;

        /**
         * Filter settings for the UserGroup GET request.
         *
         * @var ZabbixUserGroupGetFilter|null
         */
        public $filter;

        public function FilterOnID($id)
        {
            if (is_int($id))
            {
                $this->usrgrpids = $id;
            }
            elseif (is_array($id))
            {
                $usrgrpids = array();

                foreach ($id as $item)
                {
                    if (is_int($item))
                    {
                        $usrgrpids[] = $item;
                    }
                }
            }
        }
    }

    class ZabbixUserGroupGetFilter
    {
        public $name;

        public function __construct()
        {   }

        public function FilterOnName($alias)
        {
            if (is_string($alias))
            {
                $this->name = $alias;
            }
        }
    }

    class ZabbixUserGroupGetObjectRequest
    {
        public $name;

        public  function __construct($userGroupName)
        {
            $this->name = $userGroupName;
        }
    }

    class ZabbixUserGroupCreateRequest
    {
        public $name;
        public $rights;
        public $userids;
    }

    class ZabbixUserGroupUpdateRequest
    {
        public $usrgrpid;
        public $rights = array("permission" => "", "id" => "");
    }

    #endregion

    #region Host Requests

    class ZabbixHostGetRequest
    {
        public $hostids;
        public $output;
        public $selectInterfaces;
        public $filter;
        public $groupids;

        public function __construct()
        { }

        public function FilterOnID($id)
        {
            if (is_int($id))
            {
                $this->hostids = $id;
            }
            elseif (is_array($id))
            {
                $hostids = array();

                foreach ($id as $item)
                {
                    if (is_int($item))
                    {
                        $hostids[] = $item;
                    }
                }
            }
        }
    }

    class ZabbixHostCreateRequest
    {
        public $host;
        public $interfaces;
        public $groups = array("groupid" => "");
    }

    class ZabbixHostDoesExistRequest
    {
        public $host;
        public $nodeids = array();

        public function __construct($id)
        {
            if (is_int($id))
            {
                $this->nodeids[] = $id;
            }
            elseif (is_array($id))
            {
                foreach ($id as $item)
                {
                    if (is_int($item))
                    {
                        $this->nodeids[] = $item;
                    }
                }
            }
        }
    }

    class ZabbixHostGetFilter
    {
        public $host;

        public function __construct()
        {   }

        public function FilterOnName($name)
        {
            if (is_string($name))
            {
                $this->host = $name;
            }
        }
    }

    #endregion

    #region HostGroup Requests

    class ZabbixHostGroupRequest
    {
        public $name;
        public $groupids;
        public $output;
        public $filter;

        public function __construct($name)
        {
            $this->name = $name;
        }
    }

    class ZabbixHostGroupGetObjectRequest
    {
        public $name;

        public function __construct($hostGroupName)
        {
            $this->name = $hostGroupName;
        }
    }

    class ZabbixHostGroupGetRequest
    {
        public $groupids;
        public $output;

        public function __construct()
        { }
    }

    class ZabbixGroupDoesExist
    {
        public $name;

        public function __construct($group)
        {
            if (is_string($group))
            {
                $this->name = $group;
            }
            elseif (is_array($group) && count($group) > 0)
            {
                $this->name = array();

                foreach ($group as $item)
                {
                    if (is_string($item))
                    {
                        $this->name[] = $item;
                    }
                }
            }
        }
    }

    class ZabbixHostGroupGetFilter
    {
        public $name;
        public $groupid;

        public function __construct()
        {   }

        public function FilterOnName($name)
        {
            if (is_string($name))
            {
                $this->name = $name;
            }
        }

        public function FilterOnID($id)
        {
            if (is_int($id))
            {
                $this->groupid = $id;
            }
        }
    }

    #endregion

    #region Template Requests

    class ZabbixTemplateGetRequest
    {
        public $hostids;
        public $output;
        public $filter;
        public $templateids;

        public function  __construct()
        { }

        public function FilterOnID($id)
        {
            if (is_int($id))
            {
                $this->templateids = (string)$id;
            }
            elseif (is_array($id))
            {
                $templateids = array();

                foreach ($id as $item)
                {
                    if (is_int($item))
                    {
                        $templateids[] = (string)$item;
                    }
                }
            }
        }
    }

    class ZabbixTemplateGetFilter
    {
        public $host = array();

        public function __construct($arg)
        {
            if (is_array($arg))
            {
                foreach ($arg as $item)
                {
                    if (is_string($item))
                    {
                        $this->host[] = $item;
                    }
                }
            }
            elseif (is_string($arg))
            {
                $this->host[] = $arg;
            }
        }
    }

    class ZabbixTemplateCreateRequest
    {
        public $host;
        public $groups = null;
        public $hosts = null;
        public $name;

        /**
         * Set the host that'll be linked to the template.
         *
         * @param   array       $hosts          An array of host IDs.
         */
        public function SetHosts($hosts)
        {
            $this->hosts = array();

            if (is_int($hosts))
            {
                $this->hosts[] = new ZabbixTemplateCreateHosts($hosts);
            }
            elseif (is_array($hosts) && count($hosts) > 0)
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

            if (is_int($groups))
            {
                $this->groups[] = new ZabbixTemplateCreateGroups($groups);
            }
            elseif (is_array($groups) && count($groups) > 0)
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
                $this->groupid = $id;
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

    class ZabbixHostTemplateUpdateRequest
    {
        public $hostid;
        public $templates;

        public function  __construct()
        { }
    }

    #endregion

    #region Action Requests

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

        /**
         * @var ZabbixActionGetFilter
         */
        public $filter;

        public function __construct()
        {   }

        public function FilterOnID($id)
        {
            if (is_int($id))
            {
                $this->actionids = (string)$id;
            }
            elseif (is_array($id))
            {
                $actionids = array();

                foreach ($id as $item)
                {
                    if (is_int($item))
                    {
                        $actionids[] = (string)$item;
                    }
                }
            }
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
        public $eventsource;

        public $name;

        /**
         * Set the host groups that'll be assigned the template.
         *
         * @param   array       $arg         An array of host group IDs.
         */
        public function SetNames($arg)
        {
            $this->name = array();

            if (is_array($arg) && count($arg) > 0)
            {
                foreach ($arg as $item)
                {
                    $this->name[] = $item;
                }
            }
            else
            {
                $this->name[] = $arg;
            }
        }
    }

    #endregion
}