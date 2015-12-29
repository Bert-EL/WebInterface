<?php

/**
 * Created by PhpStorm.
 * User: developer
 * Date: 24/12/2015
 * Time: 10:54
 */

namespace WebServices
{

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

        #endregion

        #region Constructor

        /**
         * BaseItem constructor.
         *
         * Initialize a new instance of the BaseItem class.
         *
         */
        public function __construct()
        {
//            $this->name = $name;
//            $this->id = $ID;
        }

        /**
         * Initialze a new instance of the BaseItem class with a name and ID.
         *
         * @param   string      $name           The name of the base item.
         * @param   int         $ID             The ID of the base item.
         * @return  static
         */
        public static function WithNameAndID($name, $ID)
        {
            $instance = new static();
            $instance->name = $name;
            $instance->id = $ID;

            return $instance;
        }

        #endregion
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
         */
        public function __construct()
        {
            parent::__construct();
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
         */
        public function __construct()
        {
            parent::__construct();
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
         */
        public function __construct()
        {
            parent::__construct();
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
         */
        public function __construct()
        {
            parent::__construct();
        }
    }

    /**
     * Class Template
     *
     * Provides a class that represents a Template.
     *
     * @package WebServices
     */
    class Action extends BaseItem
    {
        /**
         * Template constructor.
         *
         * Initialize a new instance of the Template class.
         *
         */
        public function __construct()
        {
            parent::__construct();
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
         */
        public function __construct()
        {
            parent::__construct();
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
        #region Public Fields

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

        #endregion

        #region Constructor

        /**
         * HostInterface constructor.
         *
         * @param   int  $type      The interface that'll be created.
         * @param   bool $isDefault Whether the interface should be default.
         * @param   bool $useIP     Whether the interface should work by IP.
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

        #endregion
    }
}