<?php

/**
 * Created by PhpStorm.
 * User: developer
 * Date: 2/12/2015
 * Time: 14:50
 */

namespace WebServices
{
    require_once dirname(__FILE__) . "/WebServiceAPI.php";
    require_once dirname(__FILE__) . "/ZabbixWrappers.php";
    require_once dirname(__FILE__) . "/ZabbixClasses.php";

    /***
     * Class NameType
     *
     * Zabbix name type enumerations.
     *
     * @package WebServices
     */
    abstract class NameType
    {
        #region Constants

        /**
         * The name is targetted for a user.
         */
        const User = 0;

        /**
         * The name is targetted for a user group.
         */
        const UserGroup = 1;

        /**
         * The name is targetted for a host.
         */
        const Host = 2;

        /**
         * The name is targetted for a host group.
         */
        const HostGroup = 3;

        /**
         * The name is targetted for an action.
         */
        const Action = 4;

        /**
         * The name is targetted for a template.
         */
        const Template = 5;

        /**
         * Indication of the end of the enumeration.
         */
        const SENTNEL = 6;

        #endregion

        #region Public Methods

        /**
         * Get the values of all the constants in the class.
         * The index of the value is the name of the constant.
         *
         * @return  array                       The values of all the constants as an array.
         */
        public static function GetValues()
        {
            $class = new \ReflectionClass(__CLASS__);
            $constants = $class->getConstants();
            return $constants;
        }

        #endregion
    }

    /**
     * Class Permission
     *
     * Host group permissions for the user group.
     *
     * @package WebServices
     */
    abstract class Permission
    {
        #region Constants

        /**
         * Deny access to the host group.
         */
        const AccessDenied = 0;

        /**
         * Read-Only access to the host group.
         */
        const ReadOnlyAccess = 2;

        /**
         * Read-Write access to the host group.
         */
        const ReadWriteAccess = 3;

        #endregion
    }

    /**
     * Class HostInterface
     *
     * Interface types of the host.
     *
     * @package WebServices
     */
    abstract class InterfaceType
    {
        #region Constants

        /**
         * The host is an Agent.
         */
        const Agent = 1;

        /**
         * The host is an SNMP trap.
         */
        const SNMP = 2;

        /**
         * The host uses IPMI.
         */
        const IPMI = 3;

        /**
         * The host uses JMX.
         */
        const JMX = 4;

        #endregion

        #region Public Methods

        /**
         * Get the names of all the constants in the class.
         * The index of the name is the value of the constant.
         *
         * @return  array                       The names of all the constants as an array.
         */
        public static function GetNames()
        {
            $class = new \ReflectionClass(__CLASS__);
            $constants = array_flip($class->getConstants());
            return $constants;
        }

        /**
         * Get the values of all the constants in the class.
         * The index of the value is the name of the constant.
         *
         * @return  array                       The values of all the constants as an array.
         */
        public static function GetValues()
        {
            $class = new \ReflectionClass(__CLASS__);
            $constants = $class->getConstants();
            return $constants;
        }

        /**
         * Get the value of a constant by the passed name of the constant.
         *
         * @param   string      $name           The name of the constant.
         * @return  mixed                       The value of the constant if successful; otherwise false.
         */
        public static function GetValue($name)
        {
            if (is_string($name))
            {
                return self::GetValues()[$name];
            }
            else
            {
                return false;
            }
        }

        #endregion
    }

    /**
     * Class UserType
     *
     * Zabbix user type enumerations
     *
     * @package WebServices
     */
    abstract class UserType
    {
        #region Constants

        /**
         * It'll be a basic user.
         */
        const User = 1;

        /**
         * It'll be an admin user.
         */
        const Admin = 2;

        /**
         * It'll be a super admin user.
         */
        const SuperAdmin = 3;

        #endregion
    }

    /**
     * Class Language
     *
     * Zabbix language definitions.
     *
     * @package WebServices
     */
    abstract class Language
    {
        #region Constants

        const English_GB    = "en_GB";
        const English_US    = "en_US";
        const Chinese       = "zh_CN";
        const Czech         = "cs_CZ";
        const Italian       = "it_IT";
        const Japanese      = "ja_JP";
        const Polish        = "pl_PL";
        const Portuguese    = "pt_BR";
        const Russian       = "ru_RU";
        const Slovak        = "sk_SK";

        #endregion
    }

    /**
     * Class ZabbixAPI
     *
     * @package WebServices
     */
    class ZabbixAPI extends WebServiceAPI
    {
        #region Private Fields

        /**
         * Provides an incremental ID for the API queries.
         *
         * @var int
         */
        private $nonce = 1;

        /**
         * Collection of the name type indications.
         *
         * @var array
         */
        private $nameTypes = array
        (
            NameType::User      => "U",
            NameType::UserGroup => "UG",
            NameType::Host      => "H",
            NameType::HostGroup => "HG",
            NameType::Action    => "A",
            NameType::Template  => "T"
        );

        public $longNameTypes = array
        (
            NameType::User      => "user",
            NameType::UserGroup => "user group",
            NameType::Host      => "host",
            NameType::HostGroup => "host group",
            NameType::Template  => "template",
            NameType::Action    => "action"
        );

        #endregion

        #region Constructor

        /**
         * ZabbixAPI constructor.
         */
        public function __construct()
        {
            $this->Initialize("Admin", "zabbix", "application/json", "http://172.16.252.1//zabbix/api_jsonrpc.php");
        }

        #endregion

        #region Public Methods

        /**
         * Authenticate the client with the Zabbix API.
         *
         * @link    https://www.zabbix.com/documentation/2.2/manual/api/reference/user/login
         * @return  string                      The authentication token if successfull; otherwise null.
         */
        public function Authenticate()
        {
            $authToken = null;

            if (!$this->IsValidAuthToken())
            {
                $request = new ZabbixWrapper();
                $request->Create("user.login");
                $request->params = new ZabbixAuthRequest($this->Username, $this->Password);

                $response = $this->Send($request);

                if (!empty($response) && array_key_exists("result", $response))
                {
                    $authToken = $response["result"];

                    if (!isset($_SESSION["authtoken"]))
                    {
                        $_SESSION["authtoken"] = $authToken;
                    }
                }
            }

            return $authToken;
        }

        /**
         * Gets the authentication token.
         *
         * @return  string                      The authentication token if set; otherwise an empty string.
         */
        public function GetAuthToken()
        {
            return (isset($_SESSION["authtoken"]) ? ($_SESSION["authtoken"]) : (""));
        }

        /**
         * Evaluate whether the authentication token is valid.
         *
         * @return                              True if the authentication token is valid; otherwise false.
         */
        public function IsValidAuthToken()
        {
            return isset($_SESSION["authtoken"]);
        }

        /**
         * Evaluate whether the API response is valid;
         *
         * @param   array       $response       The API response.
         * @return  bool                        True if the API response is valid; otherwise false.
         */
        public function IsValidResponse($response)
        {
            return !empty($response) && array_key_exists("result", $response);
        }

        #endregion

        #region User Methods

        /**
         * Create a new user with a formatted username.
         *
         * @link    https://www.zabbix.com/documentation/2.2/manual/api/reference/user/create
         * @param   int         $customerCode   The customer's code.
         * @param   string      $passwd         The user's password.
         * @param   int         $userGroupID    The user group to add the user to.
         * @param   int         $type           [OPTIONAL] Type of the user. (1) = Zabbix user; (2) = Zabbix admin; (3) = Zabbix super admin.
         * @param   string      $name           [OPTIONAL] Name of the user.
         * @param   string      $surname        [OPTIONAL] Surname of the user.
         * @param   bool|false  $autologin      [OPTIONAL] Whether to enable auto-login.
         * @param   int         $autologout     [OPTIONAL] User session life time in seconds. If set to 0, the session will never expire.
         * @param   string      $lang           [OPTIONAL] Language code of the user's language.
         * @return  int                         The user ID if the user has been created successfully; otherwise 0.
         */
        public function CreateUserByCode($customerCode, $passwd, $userGroupID, $type = UserType::User, $name = "", $surname = "", $autologin = false, $autologout = 900, $lang = Language::English_GB)
        {
            return $this->CreateUserByName($this->FormatUserName($customerCode), $passwd, $userGroupID, $type, $name, $surname, $autologin, $autologout, $lang);
        }

        /**
         * Create a new user with a free-formatted username.
         *
         * @link    https://www.zabbix.com/documentation/2.2/manual/api/reference/user/create
         * @param   string      $alias          The user's alias (used as the username).
         * @param   string      $passwd         The user's password.
         * @param   int         $userGroupID    The user group to add the user to.
         * @param   int         $type           [OPTIONAL] Type of the user. (1) = Zabbix user; (2) = Zabbix admin; (3) = Zabbix super admin.
         * @param   string      $name           [OPTIONAL] Name of the user.
         * @param   string      $surname        [OPTIONAL] Surname of the user.
         * @param   bool|false  $autologin      [OPTIONAL] Whether to enable auto-login.
         * @param   int         $autologout     [OPTIONAL] User session life time in seconds. If set to 0, the session will never expire.
         * @param   string      $lang           [OPTIONAL] Language code of the user's language.
         * @return  int                         The user ID if the user has been created successfully; otherwise 0.
         */
        public function CreateUserByName($alias, $passwd, $userGroupID, $type = UserType::User, $name = "", $surname = "", $autologin = false, $autologout = 900, $lang = Language::English_GB)
        {
            $userID = 0;

            if (is_string($alias) && is_string($passwd) && is_int($userGroupID) && is_int($type) && is_string($name) && is_string($surname) && is_bool($autologin) && is_int($autologout) && is_string($lang))
            {
                if ($this->IsValidAuthToken() && !empty($alias) && !empty($passwd) && !$this->DoesUserExists($alias))
                {
                    $request = new ZabbixWrapper();
                    $request->Create("user.create", $this->GetAuthToken(), $this->nonce++);
                    $request->params = new ZabbixUserCreateRequest();
                    $request->params->alias = $alias;
                    $request->params->passwd = $passwd;
                    $request->params->usrgrps = array($userGroupID);
                    $request->params->autologin = ($autologin) ? (1) : (0);
                    $request->params->autologout = $autologout;
                    $request->params->type = $type;
                    $request->params->name = $name;
                    $request->params->surname = $surname;
                    $request->params->lang = $lang;

                    $response = $this->Send($request);

                    if ($this->IsValidResponse($response))
                    {
                        $userID = $response["result"]["userids"][0];
                    }
                }
            }

            return $userID;
        }

        /**
         * Get the total amount of users a customer has.
         *
         * @param   int         $customerCode   The customer's code.
         * @return  int                         The total amount users that have been assigned to the customer.
         */
        public function GetUserCount($customerCode)
        {
            $count = 0;

            if (is_int($customerCode))
            {
                foreach ($this->GetUsers() as $item)
                {
                    if (preg_match('/U(?P<code>\d{4,})(?P<id>\d{3})/', $item->name, $matches))                                  // The first and last backslash are needed to indicat the pattern!
                    {
                        if ($matches["code"] == $customerCode)
                        {
                            $count++;
                        }
                    }
                }
            }

            return $count;
        }

        /**
         * Evaluate if the passed credentials are valid.
         *
         * @param   string      $username       The name of the user.
         * @param   string      $password       The password of the user.
         * @return array
         */
        public function AuthenticateUser($username, $password)
        {
            $isValidUser = false;

            if (is_string($username) && !empty($username) && is_string($password))
            {
                if ($this->IsValidAuthToken())
                {
                    $request = new ZabbixWrapper();
                    $request->Create("user.login");
                    $request->params = new ZabbixAuthRequest($username, $password);

                    $response = $this->Send($request);

                    if ($this->IsValidResponse($response) && array_key_exists("result", $response))
                    {
                        $isValidUser = true;
                    }
                }
            }

            return $isValidUser;
        }

        /**
         * Get the details (alias and user ID) of all users.
         *
         * @link    https://www.zabbix.com/documentation/2.2/manual/api/reference/user/get
         * @return  array                       A collection of user aliases and user IDs if successfull; otherwise an empty list.
         */
        public function GetUsers()
        {
            $users = array();

            if ($this->IsValidAuthToken())
            {
                $request = new ZabbixWrapper();
                $request->Create("user.get", $this->GetAuthToken(), $this->nonce++);
                $request->params = new ZabbixUserGetRequest();
                $request->params->output = array("alias", "userid");

                $response = $this->Send($request);

                if ($this->IsValidResponse($response))
                {
                    foreach ($response["result"] as $item)
                    {
                        $users[] = User::WithNameAndID($item["alias"], $item["userid"]);
                    }
                }
            }

            return $users;
        }

        /**
         * Retrieve the users in a given user group by using the name or ID of the group.
         *
         * @link    https://www.zabbix.com/documentation/2.2/manual/api/reference/user/get
         * @param   string|int       $arg        The name or ID of the user group.
         * @return  array                        A collection of all the users in the user group.
         */
        public function GetUsersByUserGroup($arg)
        {
            $users = array();

            if ($this->IsValidAuthToken() && is_string($arg) || is_int($arg))
            {
                $userGroupID = (is_int($arg) ? ($arg) : ($this->GetUserGroupID($arg)));

                $request = new ZabbixWrapper();
                $request->Create("user.get", $this->GetAuthToken(), $this->nonce++);
                $request->params = new ZabbixUserGetRequest();
                $request->params->output = array("alias", "userid");
                $request->params->usrgrpids = $userGroupID;

                $response = $this->Send($request);

                if ($this->IsValidResponse($response) && count($response["result"]) > 0)
                {
                    foreach ($response["result"] as $item)
                    {
                        $users[] = User::WithNameAndID($item["alias"], $item["userid"]);
                    }
                }
            }

            return $users;
        }

        /**
         * Evaluate if a user exists.
         *
         * @param   string      $username       The name of the user.
         * @return  bool                        True if the user exist; otherwise false.
         */
        public function DoesUserExists($username)
        {
            $result = false;

            if ($this->IsValidAuthToken() && is_string($username))
            {
                $request = new ZabbixWrapper();
                $request->Create("user.get", $this->GetAuthToken(), $this->nonce++);
                $request->params = new ZabbixUserGetRequest();
                $request->params->output = array("alias", "userid");
                $request->params->filter = new ZabbixUserGetFilter();

                $response = $this->Send($request);

                if ($this->IsValidResponse($response))
                {
                    $result = ($response["result"] == 1);
                }
            }

            return $result;
        }

        /**
         * Get a user by its ID.
         *
         * @param   int         $id         The ID of the user.
         * @return  User|null               The user object if successfull; otherwise null.
         */
        public function GetUserByID($id)
        {
            $user = null;

            if ($this->IsValidAuthToken() && is_int($id))
            {
                $request = new ZabbixWrapper();
                $request->Create("user.get", $this->GetAuthToken(), $this->nonce++);
                $request->params = new ZabbixUserGetRequest();
                $request->params->output = array("alias", "userid");
                $request->params->filter = new ZabbixUserGetFilter();
                $request->params->filter->FilterOnUserID($id);

                $response = $this->Send($request);

                if ($this->IsValidResponse($response) && count($response["result"]) == 1)
                {
                    $user = User::WithNameAndID($response["result"][0]["alias"], $response["result"][0]["userid"]);
                }
            }

            return $user;
        }

        /**
         * Delete the given user(s) by the passed ID(s).
         *
         * @link    https://www.zabbix.com/documentation/2.2/manual/api/reference/user/delete
         * @param   int|array    $arg               The ID(s) of the user(s) that will be deleted.
         * @return  bool                            True if the user(s) is/are successfully deleted; otherwise false.
         */
        public function DeleteUser($arg)
        {
            $isDeleted = false;

            if (is_int($arg) || is_array($arg))
            {
                if ($this->IsValidAuthToken())
                {
                    $request = new ZabbixWrapper();
                    $request->Create("user.delete", $this->GetAuthToken(), $this->nonce++);
                    $request->params = array();

                    if (is_int($arg))
                    {
                        $request->params[] = (string)$arg;
                    }
                    elseif (is_array($arg) && count($arg) > 0)
                    {
                        foreach ($arg as $item)
                        {
                            if (is_int($item))
                            {
                                $request->params[] = (string)$item;
                            }
                        }
                    }

                    $response = $this->Send($request);

                    if ($this->IsValidResponse($response) && !is_null($response["result"]["userids"]) && count($response["result"]["userids"]) > 0)
                    {
                        $isDeleted = true;
                    }
                }
            }

            return $isDeleted;
        }

        #endregion

        #region User Group Methods

        /**
         * Get the user group ID of the given user group name.
         *
         * @link https://www.zabbix.com/documentation/2.2/manual/api/reference/usergroup/getobjects
         * @param $userGroupName string The name of the user group.
         * @return int The host group ID if successful; otherwise 0.
         */
        public function GetUserGroupID($userGroupName)
        {
            $userGroupID = 0;

            if ($this->IsValidAuthToken() && is_string($userGroupName))
            {
                $request = new ZabbixWrapper();
                $request->Create("usergroup.getobjects", $this->GetAuthToken(), $this->nonce++);
                $request->params = new ZabbixUserGroupGetObjectRequest($userGroupName);

                $response = $this->Send($request);

                if ($this->IsValidResponse($response) && count($response["result"]) > 0)
                {
                    $userGroupID = $response["result"][0]["usrgrpid"];
                }
            }

            return $userGroupID;
        }

        /**
         * Get an user group by its ID.
         *
         * @link    https://www.zabbix.com/documentation/2.2/manual/api/reference/usergroup/get
         * @param   int         $id         The ID of the user.
         * @return  UserGroup|null          The user object if successfull; otherwise null.
         */
        public function GetUserGroupByID($id)
        {
            $userGroup = null;

            if ($this->IsValidAuthToken() && is_int($id))
            {
                $request = new ZabbixWrapper();
                $request->Create("usergroup.get", $this->GetAuthToken(), $this->nonce++);
                $request->params = new ZabbixUserGroupGetRequest();
                $request->params->FilterOnID($id);
                $request->params->output = array("name", "usrgrpid");

                $response = $this->Send($request);

                if ($this->IsValidResponse($response) && count($response["result"]) == 1)
                {
                    $userGroup = UserGroup::WithNameAndID($response["result"][0]["name"], $response["result"][0]["usrgrpid"]);
                }
            }

            return $userGroup;
        }

        /**
         * Retrieve the active (enabled) user groups.
         *
         * @link https://www.zabbix.com/documentation/2.2/manual/api/reference/usergroup/get
         * @return array The different user groups and their ID if successful; otherwise an empty array.
         */
        public function GetActiveUserGroups()
        {
            $usergroups = array();

            if ($this->IsValidAuthToken())
            {
                $request = new ZabbixWrapper();
                $request->Create("usergroup.get", $this->GetAuthToken(), $this->nonce++);
                $request->params = new ZabbixUserGroupGetRequest();
                $request->params->status = 0;
                $request->params->output = "extend";

                $response = $this->Send($request);

                if ($this->IsValidResponse($response))
                {
                    foreach ($response["result"] as $item)
                    {
                        $usergroups[] = UserGroup::WithNameAndID($item["name"], $item["usrgrpid"]);
                    }
                }
            }

            return $usergroups;
        }

        /**
         * Create a new, free-formatted, user group.
         *
         * @link    https://www.zabbix.com/documentation/2.2/manual/api/reference/usergroup/create
         * @param   Customer    $customer       The customer (includes the name and code).
         * @param   int         $hostGroupID    ID of the host group to add permision to.
         * @param   int         $perm           Access level to the host group. (0) = access denied; (1) = read-only access; (3) = read-write access;
         * @param   null        $userIDs        [OPTIONAL] IDs of users to add to the user group.
         * @return  int                         The created user group ID if successful; otherwise <c>0</c>.
         */
        public function CreateUserGroupByCode($customer, $hostGroupID, $perm, $userIDs = null)
        {
            return $this->CreateUserGroupByName($this->FormatGroupName(NameType::UserGroup, $customer), $hostGroupID, $perm, $userIDs);
        }

        /**
         * Create a new, free-formatted, user group.
         *
         * @link    https://www.zabbix.com/documentation/2.2/manual/api/reference/usergroup/create
         * @param   string  $name           Name of the user group.
         * @param   int     $hostGroupID    ID of the host group to add permision to.
         * @param   int     $perm           Access level to the host group. (0) = access denied; (1) = read-only access; (3) = read-write access;
         * @param   null    $userIDs        [OPTIONAL] IDs of users to add to the user group.
         * @return  int                     The created user group ID if successful; otherwise 0.
         */
        public function CreateUserGroupByName($name, $hostGroupID, $perm, $userIDs = null)
        {
            $userGroupID = 0;

            if (is_string($name) && is_int($hostGroupID) && is_int($perm))
            {
                if ($this->IsValidAuthToken() && !empty($name) && !$this->DoesUserGroupExist($name))
                {
                    $request = new ZabbixWrapper();
                    $request->Create("usergroup.create", $this->GetAuthToken(), $this->nonce++);

                    $request->params = new ZabbixUserGroupCreateRequest();
                    $request->params->name = $name;
                    $request->params->rights = array("permission" => $perm, "id" => $hostGroupID);

                    if (!is_null($userIDs) && count($userIDs) > 0)
                    {
                        $request->params->userids = $userIDs;
                    }

                    $response = $this->Send($request);

                    if ($this->IsValidResponse($response))
                    {
                        $userGroupID = (int)$response["result"]["usrgrpids"][0];
                    }
                }
            }

            return $userGroupID;
        }

        /**
         * Update the permissions of an user group.
         *
         * @link    https://www.zabbix.com/documentation/2.2/manual/api/reference/usergroup/update
         * @param   int     $userGroupID    ID of the user group that'll be updated.
         * @param   int     $hostGroupID    ID of the host group to add permision to.
         * @param   int     $perm           Access level to the host group. (0) = access denied; (2) = read-only access; (3) = read-write access;
         * @return  mixed                   The updated user group ID if successful; otherwise 0.
         */
        public function UpdateUserGroupPermission($userGroupID, $hostGroupID, $perm)
        {
            if (is_int($userGroupID) && is_int($hostGroupID) && is_int($perm))
            {
                if ($this->IsValidAuthToken())
                {
                    $request = new ZabbixWrapper();
                    $request->Create("usergroup.update", $this->GetAuthToken(), $this->nonce++);
                    $request->params = new ZabbixUserGroupUpdateRequest();
                    $request->params->usrgrpid = $userGroupID;
                    $request->params->rights = array("permission" => $perm, "id" => $hostGroupID);

                    $response = $this->Send($request);

                    if ($this->IsValidResponse($response))
                    {
                        $userGroupID = $response["result"]["usrgrpids"][0];
                    }
                }
            }

            return $userGroupID;
        }

        /**
         * Evaluate if a user group exists.
         *
         * @link    https://www.zabbix.com/documentation/2.2/manual/api/reference/usergroup/exists
         * @param   string  $userGroupName  The name of the user group.
         * @return  bool                    True if the user group exist; otherwise false.
         */
        public function DoesUserGroupExist($userGroupName)
        {
            $doesExist = false;

            if (is_string($userGroupName))
            {
                if ($this->IsValidAuthToken() && !empty($userGroupName))
                {
                    $request = new ZabbixWrapper();
                    $request->Create("usergroup.exists", $this->GetAuthToken(), $this->nonce++);
                    $request->params = new ZabbixGroupDoesExist($userGroupName);

                    $response = $this->Send($request);

                    $doesExist = ($this->IsValidResponse($response) && $response["result"] == "true");
                }
            }

            return $doesExist;
        }

        /**
         * Delete the given usergroup by the passed ID.
         *
         * @link    https://www.zabbix.com/documentation/2.2/manual/api/reference/hostgroup/delete
         * @param   int|array    $arg               The ID(s) or name of the host(s) that will be deleted.
         * @return  bool                            True if the host(s) is/are successfully deleted; otherwise false.
         */
        public function DeleteUserGroup($arg)
        {
            $isDeleted = false;

            if (is_int($arg) || is_array($arg))
            {
                if ($this->IsValidAuthToken())
                {
                    $request = new ZabbixWrapper();
                    $request->Create("usergroup.delete", $this->GetAuthToken(), $this->nonce++);
                    $request->params = array();

                    if (is_int($arg))
                    {
                        $request->params[] = (string)$arg;
                    }
                    elseif (is_array($arg) && count($arg) > 0)
                    {
                        foreach ($arg as $item)
                        {
                            if (is_int($item))
                            {
                                $request->params[] = (string)$item;
                            }
                        }
                    }

                    $response = $this->Send($request);

                    if ($this->IsValidResponse($response) && !is_null($response["result"]["usrgrpids"]) && count($response["result"]["usrgrpids"]) > 0)
                    {
                        $isDeleted = true;
                    }
                }
            }

            return $isDeleted;
        }

        #endregion

        #region Host Methods

        /**
         * Retrieve a host according to the given host ID.
         *
         * @link    https://www.zabbix.com/documentation/2.2/manual/api/reference/host/get
         * @param   int     $hostID             The ID of the requested host.
         * @return  Host|null                   The host if successful; otherwise null.
         */
        public function GetHostByID($hostID)
        {
            $host = null;

            if ($this->IsValidAuthToken() && is_int($hostID))
            {
                $request = new ZabbixWrapper();
                $request->Create("host.get", $this->GetAuthToken(), $this->nonce++);
                $request->params = new ZabbixHostGetRequest();
                $request->params->FilterOnID($hostID);
                $request->params->output = array("hostid", "host");

                $response = $this->Send($request);

                if ($this->IsValidResponse($response))
                {
                    $host = Host::WithNameAndID($response["result"][0]["host"], $response["result"][0]["hostid"]);
                }
            }

            return $host;
        }

        /**
         * Retrieve a host according to the given hostname.
         *
         * @link    https://www.zabbix.com/documentation/2.2/manual/api/reference/host/get
         * @param   string    $hostname         The name of the host.
         * @return  Host|null                   The host if successful; otherwise null.
         */
        public function GetHostByName($hostname)
        {
            $host = null;

            if (is_string($hostname))
            {
                if ($this->IsValidAuthToken() && !empty($hostname))
                {
                    $request = new ZabbixWrapper();
                    $request->Create("host.get", $this->GetAuthToken(), $this->nonce++);
                    $request->params = new ZabbixHostGetRequest();
                    $request->params->filter = array("host" => $hostname);
                    $request->params->output = array("hostid", "host");

                    $response = $this->Send($request);

                    if ($this->IsValidResponse($response) && count($response["result"]) > 0)
                    {
                        $host = Host::WithNameAndID($response["result"][0]["host"], $response["result"][0]["hostid"]);
                    }
                }
            }

            return $host;
        }

        /**
         * Retrieve hosts according to the passed parameters.
         *
         * @link    https://www.zabbix.com/documentation/2.2/manual/api/reference/host/get
         * @return  array|Host                  A collection of hosts if successful; otherwise an empty array.
         */
        public function GetHosts()
        {
            $hosts = array();

            if ($this->IsValidAuthToken())
            {
                $request = new ZabbixWrapper();
                $request->Create("host.get", $this->GetAuthToken(), $this->nonce++);
                $request->params = new ZabbixHostGetRequest();
                $request->params->output = array("hostid", "host");
                //selectInterfaces = new string[] { "interfaceid", "ip" }

                $response = $this->Send($request);

                if ($this->IsValidResponse($response))
                {
                    foreach ($response["result"] as $item)
                    {
                        $hosts[] = Host::WithNameAndID($item["host"], $item["hostid"]);
                    }
                }
            }

            return $hosts;
        }

        /**
         * Retrieve the hosts in a given host group by using the name or ID of the group.
         *
         * @param  string|int       $arg        The name or ID of the host group.
         * @return array                        A collection of all the hosts in the host group.
         */
        public function GetHostsByHostGroup($arg)
        {
            $hosts = array();

            if (is_string($arg) || is_int($arg))
            {
                if ($this->IsValidAuthToken())
                {
                    $hostGroupID = (is_int($arg))?($arg):($this->GetHostGroupID($arg));

                    $request = new ZabbixWrapper();
                    $request->Create("host.get", $this->GetAuthToken(), $this->nonce++);
                    $request->params = new ZabbixHostGetRequest();
                    $request->params->output = array("hostid", "host");
                    $request->params->groupids = $hostGroupID;

                    $response = $this->Send($request);

                    if ($this->IsValidResponse($response))
                    {
                        foreach ($response["result"] as $item)
                        {
                            $hosts[] = Host::WithNameAndID($item["host"], $item["hostid"]);
                        }
                    }
                }
            }

            return $hosts;
        }

        /**
         * Get the host ID of the given host name.
         *
         * @param   string      $hostname       The name of the host whose ID will be retrieved.
         * @return  int                         The host ID if successful; otherwise 0.
         */
        public function GetHostID($hostname)
        {
            $h = null;

            if (is_string($hostname))
            {
                if (!empty($hostname))
                {
                    foreach ($this->GetHosts() as $item)
                    {
                        if ($item->Name == $hostname)
                        {
                            return $item->ID;
                        }
                    }
                }
            }

            return 0;
        }

        /**
         * Get the name of the host by using the passed ID.
         *
         * @param   int         $id             The ID of the host whose name will be retrieved.
         * @return  string                      The hostname if successful; otherwise an empty string.
         */
        public function GetHostName($id)
        {
            $h = null;

            if (is_int($id))
            {
                foreach ($this->GetHosts() as $item)
                {
                    if ($item->ID == $id)
                    {
                        return $item->Name;
                    }
                }
            }

            return "";
        }

        /**
         * Create a new host.
         *
         * @link    https://www.zabbix.com/documentation/2.2/manual/api/reference/host/create
         * @param   string          $hostName       The technical name of the host.
         * @param   int             $hostGroupID    The ID of the host group where the host will be added to.
         * @param   HostInterface   $hostInterface  Interfaces to be created for the host.
         * @return  int                             The ID of the host if the host is created successfully; otherwise 0.
         */
        public function CreateHost($hostName, $hostGroupID, $hostInterface)
        {
            $hostID = 0;

            if (is_string($hostName) && is_int($hostGroupID) && !is_null($hostInterface))
            {
                if ($this->IsValidAuthToken())
                {
                    $interfaces = array($hostInterface);
                    $groups = array(array("groupid" => $hostGroupID));

                    $request = new ZabbixWrapper();
                    $request->Create("host.create", $this->GetAuthToken(), $this->nonce++);
                    $request->params = new ZabbixHostCreateRequest();
                    $request->params->host = $hostName;
                    $request->params->interfaces = $interfaces;
                    $request->params->groups = $groups;

                    $response = $this->Send($request);

                    if ($this->IsValidResponse($response) && !is_null($response["result"]["hostids"]) && count($response["result"]["hostids"]) == 1)
                    {
                        $hostID = (int)$response["result"]["hostids"][0];
                    }
                }
            }

            return $hostID;
        }

        /**
         * Delete the given host(s) by the passed ID.
         *
         * @link    https://www.zabbix.com/documentation/2.2/manual/api/reference/host/delete
         * @param   int|array       $id             The host(s) that will be deleted.
         * @return  bool                            True if the host(s) is/are successfully deleted; otherwise false.
         */
        public function DeleteHost($id)
        {
            $isDeleted = false;

            if (is_int($id) || is_array($id))
            {
                if ($this->IsValidAuthToken())
                {
                    $request = new ZabbixWrapper();
                    $request->Create("host.delete", $this->GetAuthToken(), $this->nonce++);
                    $request->params = array();

                    if (is_int($id))
                    {
                        $request->params[] = (string)$id;
                    }
                    elseif (is_array($id) && count($id) > 0)
                    {
                        foreach ($id as $item)
                        {
                            if (is_int($item))
                            {
                                $request->params[] = (string)$item;
                            }
                        }
                    }

                    $response = $this->Send($request);

                    if ($this->IsValidResponse($response) && !is_null($response["result"]["hostids"]) && count($response["result"]["hostids"]) > 0)
                    {
                        $isDeleted = true;
                    }
                }
            }

            return $isDeleted;
        }

        /**
         * Delete the given host by it's name.
         *
         * @link    https://www.zabbix.com/documentation/2.2/manual/api/reference/host/delete
         * @param   string          $name           The name of the host that'll be deleted.
         * @return  bool                            True if the host(s) is/are successfully deleted; otherwise false
         */
        public function DeleteHostByName($name)
        {
            $isDeleted = false;

            if (is_string($name))
            {
                $isDeleted = $this->DeleteHost($this->GetHostID($name));
            }

            return $isDeleted;
        }

        /**
         * Evaluate whether the host exists by using its host ID or hostname.
         *
         * @param   int             $arg            The ID or name of the host.
         * @return  bool                            True if the host exists; otherwise false.
         */
        public function DoesHostExist($arg)
        {
            $isDeleted = false;

            if (is_int($arg) || is_string($arg))
            {
                if ($this->IsValidAuthToken())
                {
                    $hostname = (is_string($arg)) ? ($arg) : ($this->GetHostName($arg));

                    $request = new ZabbixWrapper();
                    $request->Create("host.exists", $this->GetAuthToken(), $this->nonce++);
                    $request->params = new ZabbixHostDoesExistRequest(null);
                    $request->params->host = $hostname;

                    $response = $this->Send($request);

                    if ($this->IsValidResponse($response))
                    {
                        $isDeleted = $response["result"];
                    }
                }
            }

            return $isDeleted;
        }

        #endregion

        #region Host Group Methods

        /**
         * Create a new formatted host group.
         *
         * @link    https://www.zabbix.com/documentation/2.2/manual/api/reference/hostgroup/create
         * @param   Customer        $customer       The customer (includes the name and code).
         * @return  int                             The host group if the group was created succesfully; otherwise 0.
         */
        public function CreateHostGroupByCode($customer)
        {
            return $this->CreateHostGroupByName($this->FormatGroupName(NameType::HostGroup, $customer));
        }

        /**
         * Create a new, free-formatted, host group.
         * In general one host group per customer.
         *
         * @link    https://www.zabbix.com/documentation/2.2/manual/api/reference/hostgroup/create
         * @param   string      $hostGroupName  The name of the host group.
         * @return  int                         The host group if the group was created succesfully; otherwise 0.
         */
        public function CreateHostGroupByName($hostGroupName)
        {
            $hostGroupID = 0;

            if (is_string($hostGroupName))
            {
                if ($this->IsValidAuthToken() && !$this->DoesHostGroupExist($hostGroupName))
                {
                    $request = new ZabbixWrapper();
                    $request->Create("hostgroup.create", $this->GetAuthToken(), $this->nonce++);
                    $request->params = new ZabbixHostGroupRequest($hostGroupName);

                    $response = $this->Send($request);

                    if ($this->IsValidResponse($response) && !is_null($response["result"]["groupids"]) && count($response["result"]["groupids"]) == 1)
                    {
                        $hostGroupID = (int)$response["result"]["groupids"][0];
                    }
                }
            }

            return $hostGroupID;
        }

        /**
         * Evaluate if a host group exists.
         *
         * @link    https://www.zabbix.com/documentation/2.2/manual/api/reference/hostgroup/exists
         * @param   string      $hostGroupName      The name of the host group.
         * @return  bool                            True if the host group exist; otherwise false.
         */
        public function DoesHostGroupExist($hostGroupName)
        {
            $doesExist = true;

            if (is_string($hostGroupName) || (is_array($hostGroupName) && count($hostGroupName) > 0))
            {
                if ($this->IsValidAuthToken())
                {
                    $request = new ZabbixWrapper();
                    $request->Create("hostgroup.exists", $this->GetAuthToken(), $this->nonce++);
                    $request->params = new ZabbixGroupDoesExist($hostGroupName);

                    $response = $this->Send($request);

                    $doesExist = ($response["result"] == "true");
                }
            }

            return $doesExist;
        }

        /**
         * Evaluate if the passed host group names exists.
         *
         * @param   array       $hostGroupNames     A collection of host group names that'll be evaluated.
         * @return  array                           The names of the host groups that do exist.
         */
        public function DoesHostGroupsExists($hostGroupNames)
        {
            $results = array();

            if (is_array($hostGroupNames))
            {
                $existingGroups = $this->GetHostGroups();

                foreach ($existingGroups as $name)
                {
                    if (in_array($name->name, $hostGroupNames))
                    {
                        $results[] = $name->name;
                        break;
                    }
                }
            }

            return $results;
        }

        /**
         * Get the host group ID of the given host group name.
         *
         * @link    https://www.zabbix.com/documentation/2.2/manual/api/reference/hostgroup/getobjects
         * @param   string      $hostGroupName      The name of the host group.
         * @return  int                             The host group ID if successful; otherwise 0.
         */
        public function GetHostGroupID($hostGroupName)
        {
            $hostGroupID = 0;

            if (is_string($hostGroupName))
            {
                if ($this->IsValidAuthToken())
                {
                    $request = new ZabbixWrapper();
                    $request->Create("hostgroup.getobjects", $this->GetAuthToken(), $this->nonce++);
                    $request->params = new ZabbixHostGroupGetObjectRequest($hostGroupName);

                    $response = $this->Send($request);

                    if ($this->IsValidResponse($response) && count($response["result"]) > 0)
                    {
                        $hostGroupID = $response["result"][0]["groupid"];
                    }
                }
            }

            return $hostGroupID;
        }

        /**
         * Retrieve the existing host groups.
         *
         * @link    https://www.zabbix.com/documentation/2.2/manual/api/reference/hostgroup/get
         * @return  array                           A collection of host groups and related host group IDs if successful; otherwise an empty list.
         */
        public function GetHostGroups()
        {
            $hostgroups = array();

            if ($this->IsValidAuthToken())
            {
                $request = new ZabbixWrapper();
                $request->Create("hostgroup.get", $this->GetAuthToken(), $this->nonce++);
                $request->params = new ZabbixHostGroupGetRequest();
                $request->params->output = array("groupid", "name");

                $response = $this->Send($request);

                if ($this->IsValidResponse($response))
                {
                    foreach ($response["result"] as $item)
                    {
                        $hostgroups[] = HostGroup::WithNameAndID($item["name"], $item["groupid"]);
                    }
                }
            }

            return $hostgroups;
        }

        /**
         * Retrieve the names of the existing host groups.
         *
         * @return  array                           A collection of host group names if successfull; otherwise an empty array.
         */
        public function GetHostGroupNames()
        {
            $names = array();

            foreach ($this->GetHostGroups() as $item)
            {
                $names[] = $item->name;
            }

            return $names;
        }

        /**
         * Retrieve a host according to the given host ID.
         *
         * @link    https://www.zabbix.com/documentation/2.2/manual/api/reference/hostgroup/get
         * @param   int         $hostGroupID        The id of the passed host.
         * @return  HostGroup|null                  The host if successful; otherwise null.
         */
        public function GetHostGroup($hostGroupID)
        {
            $hostGroup = null;

            if (is_int($hostGroupID))
            {
                if ($this->IsValidAuthToken() && $hostGroupID > 0)
                {
                    $request = new ZabbixWrapper();
                    $request->Create("hostgroup.get", $this->GetAuthToken(), $this->nonce++);
                    $request->params = new ZabbixHostGroupGetRequest();
                    $request->params->groupids = array($hostGroupID);
                    $request->params->output = array("groupid", "name");

                    $response = $this->Send($request);

                    if ($this->IsValidResponse($response) && count($response["result"]) > 0)
                    {
                        $hostGroup = HostGroup::WithNameAndID($response["result"][0]["name"], $response["result"][0]["groupid"]);
                    }
                }
            }

            return $hostGroup;
        }

        /**
         * Retrieve a hostgroup according to the given hostgroup ID.
         *
         * @link    https://www.zabbix.com/documentation/2.2/manual/api/reference/hostgroup/get
         * @param   int         $hostgroupID        The ID of the requested host.
         * @return  HostGroup|null                  The host if successful; otherwise null.
         */
        public function GetHostGroupByID($hostgroupID)
        {
            $hostgroup = null;

            if (is_int($hostgroupID))
            {
                if ($this->IsValidAuthToken() && $hostgroupID > 0)
                {
                    $request = new ZabbixWrapper();
                    $request->Create("hostgroup.get", $this->GetAuthToken(), $this->nonce++);
                    $request->params = new ZabbixHostGroupGetRequest();
                    $request->params->groupids = array($hostgroupID);
                    $request->params->output = array("groupids", "name");

                    $response = $this->Send($request);

                    if ($this->IsValidResponse($response) && count($response["result"]) > 0)
                    {
                        $hostgroup = HostGroup::WithNameAndID($response["result"][0]["name"], $response["result"][0]["groupid"]);
                    }
                }
            }

            return $hostgroup;
        }

        /**
         * Delete the given host(s) by the passed ID.
         *
         * @link    https://www.zabbix.com/documentation/2.2/manual/api/reference/hostgroup/delete
         * @param   int|array    $arg               The ID(s) or name of the host(s) that will be deleted.
         * @return  bool                            True if the host(s) is/are successfully deleted; otherwise false.
         */
        public function DeleteHostGroup($arg)
        {
            $isDeleted = false;

            if (is_int($arg) || is_array($arg))
            {
                if ($this->IsValidAuthToken())
                {
                    $request = new ZabbixWrapper();
                    $request->Create("hostgroup.delete", $this->GetAuthToken(), $this->nonce++);
                    $request->params = array();

                    if (is_int($arg))
                    {
                        $request->params[] = (string)$arg;
                    }
                    elseif (is_array($arg) && count($arg) > 0)
                    {
                        foreach ($arg as $item)
                        {
                            if (is_int($item))
                            {
                                $request->params[] = (string)$item;
                            }
                        }
                    }

                    $response = $this->Send($request);

                    if ($this->IsValidResponse($response) && !is_null($response["result"]["groupids"]) && count($response["result"]["groupids"]) > 0)
                    {
                        $isDeleted = true;
                    }
                }
            }

            return $isDeleted;
        }

        #endregion

        #region Template Methods

        /**
         * Create a new template and link it to the given host and host groups.
         *
         * @link
         * @param   string          $name           The name of the template.
         * @param   string          $alias          The display name of the template.
         * @param   int|array       $hosts          [OPTIONAL] A collection of hosts that'll be linked to the template.
         * @param   int|array       $hostGroups     A collection of host groups that'll be linked to the template.
         * @return  int                             The template ID.
         */
        public function CreateTemplate($templateName, $alias, $hostGroups, $hosts)
        {
            $templateid = 0;

            if (is_string($templateName) && is_string($alias) && (is_int($hosts) || is_array($hosts)) && (is_int($hostGroups) || (is_array($hostGroups) && count($hostGroups) > 0)))
            {
                if ($this->IsValidAuthToken())
                {
                    $request = new ZabbixWrapper();
                    $request->Create("template.create", $this->GetAuthToken(), $this->nonce++);
                    $request->params = new ZabbixTemplateCreateRequest();
                    $request->params->host = $alias;
                    $request->params->name = $templateName;
                    $request->params->SetGroups($hostGroups);
                    $request->params->SetHosts($hosts);

                    $response = $this->Send($request);

                    if ($this->IsValidResponse($response) && !is_null($response["result"]["templateids"]) && count($response["result"]["templateids"]) == 1)
                    {
                        $templateid = $response["result"]["templateids"][0];
                    }
                }
            }

            return $templateid;
        }

        /**
         * Get all Zabbix templates.
         *
         * @link    https://www.zabbix.com/documentation/2.2/manual/api/reference/template/get
         * @return  array                           A list of Zabbix templates if successful; otherwise an empty array.
         */
        public function GetTemplates()
        {
            $templates = array();

            if ($this->IsValidAuthToken())
            {
                $request = new ZabbixWrapper();
                $request->Create("template.get", $this->GetAuthToken(), $this->nonce++);
                $request->params = new ZabbixTemplateGetRequest();
                $request->params->output = array("name", "templateid");

                $response = $this->Send($request);

                if ($this->IsValidResponse($response))
                {
                    foreach ($response["result"] as $key => $value)
                    {
                        if (!in_array($value["name"], $templates))
                        {
                            $templates[] = Template::WithNameAndID($value["name"], $value["templateid"]);
                        }
                    }
                }
            }

            return $templates;
        }

        /**
         * Get a template by name.
         *
         * @link    https://www.zabbix.com/documentation/2.2/manual/api/reference/template/get
         * @param   string      $name               The name of the template.
         * @return  Template                        An object representing the Template.
         */
        public function GetTemplateByName($name)
        {
            $template = null;

            if ($this->IsValidAuthToken() && is_string($name))
            {
                $request = new ZabbixWrapper();
                $request->Create("template.get", $this->GetAuthToken(), $this->nonce++);
                $request->params = new ZabbixTemplateGetRequest();
                $request->params->output = array("name", "templateid");
                $request->params->filter = new ZabbixTemplateGetFilter($name);

                $response = $this->Send($request);

                if ($this->IsValidResponse($response))
                {
                    foreach ($response["result"] as $key => $value)
                    {
                        $template = Template::WithNameAndID($value["name"], (int)$value["templateid"]);
                    }
                }
            }

            return $template;
        }

        /**
         * Get a template by its ID.
         *
         * @link    https://www.zabbix.com/documentation/2.2/manual/api/reference/template/get
         * @param   int         $id                 The ID of the template.
         * @return  Template|null                   The template object if successfull; otherwise null.
         */
        public function GetTemplateByID($id)
        {
            $action = null;

            if ($this->IsValidAuthToken() && is_int($id))
            {
                $request = new ZabbixWrapper();
                $request->Create("template.get", $this->GetAuthToken(), $this->nonce++);
                $request->params = new ZabbixTemplateGetRequest();
                $request->params->output = array("host", "templateid");
                $request->params->FilterOnID($id);

                $response = $this->Send($request);

                if ($this->IsValidResponse($response) && count($response["result"]) == 1)
                {
                    $action = Action::WithNameAndID($response["result"][0]["host"], $response["result"][0]["templateid"]);
                }
            }

            return $action;
        }

        /**
         * Get the templates that are linked to the given host.
         *
         * @link    https://www.zabbix.com/documentation/2.2/manual/api/reference/template/get
         * @param   string      $hostname       The name of the host.
         * @return  array                       A list of Zabbix templates if successful; otherwise an empty list.
         */
        public function GetTemplatesByHostname($hostname)
        {
            $templates = array();

            if (is_string($hostname))
            {
                $hostID = $this->GetHostID($hostname);

                if ($this->IsValidAuthToken() && $hostID != 0)
                {
                    $request = new ZabbixWrapper();
                    $request->Create("template.get", $this->GetAuthToken(), $this->nonce++);
                    $request->params = new ZabbixTemplateGetRequest();
                    $request->params->output = array("name", "templateid");
                    $request->params->hostids = array($hostID);

                    $response = $this->Send($request);

                    if ($this->IsValidResponse($response))
                    {
                        foreach ($response["result"] as $key => $value)
                        {
                            if (!in_array($value["name"], $templates))
                            {
                                $templates[] = Template::WithNameAndID($value["name"], $value["templateid"]);
                            }
                        }
                    }
                }
            }

            return $templates;
        }

        /**
         * Update the templates of the given host.
         *
         * @link    https://www.zabbix.com/documentation/2.2/manual/api/reference/host/update
         * @param   string      $hostname       The name of the host.
         * @param   array       $templateIDs    The new template(s) that'll be applied to the host.
         * @return  bool                        True if the update was successful; otherwise false.
         */
        public function UpdateTemplate($hostname, $templateIDs)
        {
            $result = false;

            if (is_string($hostname) && is_array($templateIDs))
            {
                $hostID = $this->GetHostID($hostname);

                if ($this->IsValidAuthToken() && $hostID > 0 && !is_null($templateIDs))
                {
                    $request = new ZabbixWrapper();
                    $request->Create("host.update", $this->GetAuthToken(), $this->nonce++);
                    $request->params = new ZabbixHostTemplateUpdateRequest();
                    $request->params->hostid = $hostID;
                    $request->params->templates = array();

                    foreach ($templateIDs as $templateID)
                    {
                        $item = array("templateid" => $templateID);
                        $request->params->templates[] = $item;
                    }

                    $response = $this->Send($request);
                    $result = ($this->IsValidResponse($response) && $response["result"]["hostids"][0] == $hostID);
                }
            }

            return $result;
        }

        /**
         * Delete the given template(s) by the passed ID.
         *
         * @link    https://www.zabbix.com/documentation/2.2/manual/api/reference/template/delete
         * @param   int|array       $id             The template(s) that will be deleted.
         * @return  bool                            True if the template(s) is/are successfully deleted; otherwise false.
         */
        public function DeleteTemplate($id)
        {
            $isDeleted = false;

            if (is_int($id) || is_array($id))
            {
                if ($this->IsValidAuthToken())
                {
                    $request = new ZabbixWrapper();
                    $request->Create("template.delete", $this->GetAuthToken(), $this->nonce++);
                    $request->params = array();

                    if (is_int($id))
                    {
                        $request->params[] = (string)$id;
                    }
                    elseif (is_array($id) && count($id) > 0)
                    {
                        foreach ($id as $item)
                        {
                            if (is_int($item))
                            {
                                $request->params[] = (string)$item;
                            }
                        }
                    }

                    $response = $this->Send($request);

                    if ($this->IsValidResponse($response) && !is_null($response["result"]["templateids"]) && count($response["result"]["templateids"]) > 0)
                    {
                        $isDeleted = true;
                    }
                }
            }

            return $isDeleted;
        }

        #endregion

        #region Action Methods

        /**
         * Create a new action that automatically adds a host to a host group (and link it to templats).
         *
         * @param   Customer    $customer       The customer that'll be linked to the action.
         * @param   int         $hostGroupID    The host group that'll be linked to the action.
         * @param   array       $templateIDs    The templates that'll be linked to the action.
         * @return  int                         The ID of the created action.
         */
        public function CreateActionForNewHost($customer, $hostGroupID, $templateIDs)
        {
            $actionids = 0;

            if (!is_null($customer) && is_int($hostGroupID) && is_array($templateIDs) && count($templateIDs) > 0)
            {
                if ($this->IsValidAuthToken())
                {
                    $request = new ZabbixWrapper();
                    $request->Create("action.create", $this->GetAuthToken(), $this->nonce++);
                    $request->params = new ZabbixActionCreateRequest();
                    $request->params->name = $this->GetNewActionName($customer);
                    $request->params->def_shortdata = "Auto registration: {HOST.HOST}";
                    $request->params->def_longdata = "Host name: {HOST.HOST}\r\nHost IP: {HOST.IP}\r\nAgent port: {HOST.PORT}";

                    $condition = new ZabbixActionCreateConditions();
                    $condition->conditiontype = 24;
                    $condition->value = (string)$customer->code;
                    $condition->operator = 2;

                    $operationA = new ZabbixActionCreateOperations();
                    $operationA->operationtype = 2;

                    $operationB = new ZabbixActionCreateOperations();
                    $operationB->operationtype = 4;
                    $operationB->opgroup = array();
                    $operationB->opgroup[] = array("groupid" => (string)$hostGroupID);

                    $operationC = new ZabbixActionCreateOperations();
                    $operationC->operationtype = 6;
                    $operationC->optemplate = array();

                    foreach ($templateIDs as $id)
                    {
                        $operationC->optemplate[] = array("templateid" => (string)$id);
                    }

                    $request->params->filter->conditions[] = $condition;
                    $request->params->operations = array($operationA, $operationB, $operationC);

                    $response = $this->Send($request);

                    if ($this->IsValidResponse($response) && !is_null($response["result"]["actionids"]) && count($response["result"]["actionids"]) == 1)
                    {
                        $actionids = $response["result"]["actionids"][0];
                    }
                }
            }

            return $actionids;
        }

        /**
         * Get all auto-registration actions.
         *
         * @link    https://www.zabbix.com/documentation/2.2/manual/api/reference/action/get
         * @return  array                       An array containing the actions if successfull; otherwise an empty array.
         */
        public function GetAutoRegistrationActions()
        {
            $response = array();

            if ($this->IsValidAuthToken())
            {
                $request = new ZabbixWrapper();
                $request->Create("action.get", $this->GetAuthToken(), $this->nonce++);
                $request->params = new ZabbixActionGetRequest();
                $request->params->filter->eventsource = 2;

                $response = $this->Send($request);
            }

            return $response;
        }

        /**
         * Get an action by name.
         *
         * @link    https://www.zabbix.com/documentation/2.2/manual/api/reference/action/get
         * @param   string      $name               The name of the action.
         * @return  Action                          An object representing the Template.
         */
        public function GetActionByName($name)
        {
            $action = new Action();

            if (is_string($name))
            {
                if ($this->IsValidAuthToken())
                {
                    $request = new ZabbixWrapper();
                    $request->Create("action.get", $this->GetAuthToken(), $this->nonce++);
                    $request->params = new ZabbixActionGetRequest();
                    $request->params->output = array("name", "actionid");
                    $request->params->filter = new ZabbixActionGetFilter();
                    $request->params->filter->SetNames($name);

                    $response = $this->Send($request);

                    if ($this->IsValidResponse($response))
                    {
                        foreach ($response["result"] as $key => $value)
                        {
                            $action = Action::WithNameAndID($value["name"], $value["actionid"]);
                        }
                    }
                }
            }

            return $action;
        }

        /**
         * Get an action by its ID.
         *
         * @link    https://www.zabbix.com/documentation/2.2/manual/api/reference/action/get
         * @param   int         $id                 The ID of the action.
         * @return  Action|null                     The action object if successfull; otherwise null.
         */
        public function GetActionByID($id)
        {
            $action = null;

            if ($this->IsValidAuthToken() && is_int($id))
            {
                $request = new ZabbixWrapper();
                $request->Create("action.get", $this->GetAuthToken(), $this->nonce++);
                $request->params = new ZabbixActionGetRequest();
                $request->params->FilterOnID($id);

                $response = $this->Send($request);

                if ($this->IsValidResponse($response) && count($response["result"]) == 1)
                {
                    $action = Action::WithNameAndID($response["result"][0]["name"], $response["result"][0]["actionid"]);
                }
            }

            return $action;
        }

        /**
         * Delete the given action(s) by the passed ID.
         *
         * @link    https://www.zabbix.com/documentation/2.2/manual/api/reference/action/delete
         * @param   int|array       $id             The action(s) that will be deleted.
         * @return  bool                            True if the action(s) is/are successfully deleted; otherwise false.
         */
        public function DeleteAction($id)
        {
            $isDeleted = false;

            if (is_int($id) || is_array($id))
            {
                if ($this->IsValidAuthToken())
                {
                    $request = new ZabbixWrapper();
                    $request->Create("action.delete", $this->GetAuthToken(), $this->nonce++);
                    $request->params = array();

                    if (is_int($id))
                    {
                        $request->params[] = (string)$id;
                    }
                    elseif (is_array($id) && count($id) > 0)
                    {
                        foreach ($id as $item)
                        {
                            if (is_int($item))
                            {
                                $request->params[] = (string)$item;
                            }
                        }
                    }

                    $response = $this->Send($request);

                    if ($this->IsValidResponse($response) && !is_null($response["result"]["actionids"]) && count($response["result"]["actionids"]) > 0)
                    {
                        $isDeleted = true;
                    }
                }
            }

            return $isDeleted;
        }

        #endregion

        #region Naming Methods

        /**
         * Get a new username.
         *
         * @param   int         $customerCode   The customer's code.
         * @return mixed|string                 The name for a new user.
         */
        public function GetNewUsername($customerCode)
        {
            return $this->FormatUsername($customerCode);
        }

        /**
         * Get a new action name.
         *
         * @param   Customer        $customer       The customer (includes the name and code).
         * @return  mixed|string                    The name for a new action.
         */
        public function GetNewActionName($customer)
        {
            return $this->FormatGroupName(NameType::Action, $customer);
        }

        /**
         * Get a new template name.
         *
         * @param   Customer        $customer       The customer (includes the name and code).
         * @return  mixed|string                    The name for a new template.
         */
        public function GetNewTemplateName($customer)
        {
            return $this->FormatGroupName(NameType::Template, $customer);
        }

        /**
         * Get a template alias based on the customer.
         *
         * @param   Customer        $customer       The customer (includes the name and code).
         * @return  string
         */
        public function GetTemplateAlias($customer)
        {
            return "Template - " . $customer->code . " - " . $customer->name;
        }

        /**
         * Get a new user group name.
         *
         * @param   Customer     $customer      The customer (includes the name and code).
         * @return mixed|null|string            The user group name in case a new user group is created.
         */
        public function GetNewUserGroupName($customer)
        {
            return $this->FormatGroupName(NameType::UserGroup, $customer);
        }

        /**
         * Get a new host group name.
         *
         * @param   Customer    $customer       The customer (includes the name and code).
         * @return  mixed|null|string           The host group name in case a new host group is created.
         */
        public function GetNewHostGroupName($customer)
        {
            return $this->FormatGroupName(NameType::HostGroup, $customer);
        }

        /**
         * Format the customer's name so it can be used as a Zabbix group name.
         *
         * @param   int         $nType          The group type.
         * @param   Customer    $customer       The customer (includes the name and code).
         * @return  mixed|null|string           [{UG|HG}{CODE}] {CODE} if successful; otherwise [{UG|HG}{ERROR}]
         */
        public function FormatGroupName($nType, $customer)
        {
            $formattedName = null;

            if (is_int($nType) && !is_null($customer))
            {
                $groupType = "{UG|HG|A|T}";
                $formattedName = "[" . $groupType . "{CODE}] {NAME}";
                $formattedName = str_replace($groupType, $this->nameTypes[$nType], $formattedName);

                if (!empty($customer->name))
                {
                    $formattedName = str_replace("{CODE}", $customer->code, $formattedName);
                    $formattedName = str_replace("{NAME}", $customer->name, $formattedName);
                }
                else
                {
                    $formattedName = str_replace("{CODE}", "ERROR", $formattedName);
                    $formattedName = str_replace("{NAME}", "", $formattedName);
                }
            }

            return $formattedName;
        }

        /**
         * Format the customer's name so it can be used as a Zabbix user name.
         *
         * @param   int     $customerCode       The customer's code.
         * @return mixed|string                 An username in the <c>U{CODE}{USER}</c> format.
         */
        public function FormatUsername($customerCode)
        {
            return $this->FormatUsernameByCodeAndUserID($customerCode, $this->GetUserCount($customerCode) + 1);
        }

        /**
         * Format the customer's name so it can be used as a Zabbix user name.
         *
         * @param   int       $customerCode     The customer's code.
         * @param   int       $userID           The user ID.
         * @return  mixed|string                An username in the U{CODE}{USER} format.
         */
        private function FormatUsernameByCodeAndUserID($customerCode, $userID)
        {
            $formattedName = "";

            if (is_int($customerCode) && is_int($userID))
            {
                $formattedName = "U{CODE}{USER}";

                $formattedName = str_replace("{CODE}", $customerCode, $formattedName);
                $formattedName = str_replace("{USER}", str_pad($userID, 3, "0", STR_PAD_LEFT), $formattedName);
            }

            return $formattedName;
        }

        #endregion
    }
}