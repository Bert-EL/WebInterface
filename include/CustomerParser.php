<?php

/**
 * Created by PhpStorm.
 * User: developer
 * Date: 10/12/2015
 * Time: 15:27
 */

namespace WebServices
{
    require_once dirname(__FILE__) . "/ZabbixAPI.php";

    /**
     * Class CustomerSettings
     *
     * Contains default settings for the customer.
     *
     * @package WebServices
     */
    abstract  class CustomerSettings
    {
        #region Constants

        /**
         * Contains the default password.
         */
        const DEFAULT_PASSWORD = "Temp*123";

        #endregion
    }

    /**
     * Class CustomerParser
     *
     * Read and process the XML file that contains the paying monitoring customers.
     *
     * @package WebServices
     */
    class CustomerParser
    {
        #region Private Fields

        /**
         * @var     ZabbixAPI                   An instance of the Zabbix API.
         */
        private $zapi;

        /**
         * @var     \SimpleXMLElement           The XML document that contains the paying monitoring customers.
         */
        private $xml;

        #endregion

        #region Constructor

        /**
         * Load the XML containing the monitoring customer information.
         *
         * CustomerParser constructor.
         */
        public function __construct()
        {
            $this->zapi = new ZabbixAPI();
            $this->xml = simplexml_load_file("http://172.16.252.1/zabbix/ZabbixCustomers.xml");
        }

        #endregion

        #region Public Methods

        /**
         * Evaluate whether the XML has a valid structure.
         *
         * @return bool                             True if the XML is valid; otherwise false.
         */
        public function IsValidXML()
        {
            return !empty($this->xml);
        }

        /**
         * Get the names and codes of all processed monitoring customers.
         *
         * @return  array                               The names and codes of the monitoring customers.
         */
        public function GetProcessedCustomers()
        {
            return $this->GetCustomers(true);
        }

        /**
         * Get the name and code of all unprocessed monitoring customers.
         *
         * @return array                                The names and codes of the monitoring customers.
         */
        public function GetUnprocessedCustomers()
        {
            return $this->GetCustomers(false);
        }

        /**
         * Get the names and codes of the monitoring customers.
         *
         * @param   boolean   $areProcessed             Indication whether the customers are processed or not.
         * @return  array                               The names and codes of the monitoring customers.
         */
        public function GetCustomers($areProcessed)
        {
            $customers = array();

            if ($this->IsValidXML() && is_bool($areProcessed))
            {
                $existingGroups = $this->zapi->GetHostGroupNames();                                                         // Retrieve a list of existing host groups.

                foreach ($this->xml->xpath("//customer") as $item)                                                          // Ignore the parent node.
                {
                    $customer = Customer::WithNameAndCode((string)$item->name, (int)$item->code);
                    $formattedName = $this->zapi->FormatGroupName(NameType::HostGroup, $customer);

                    if (in_array($formattedName, $existingGroups) === $areProcessed)
                    {
                        $customers[] = $customer;                                                                           // Only added the non-existing host groups to the return array.
                    }
                }
            }

            return $customers;
        }

        /**
         * Get the names and codes of all monitoring customers.
         *
         * @return  array                           The names and codes of the monitoring customers.
         */
        public function GetAllCustomers()
        {
            $customers = array();

            if ($this->IsValidXML())
            {
                foreach ($this->xml->xpath("//customer") as $item)                                                          // Ignore the parent node.
                {
                    $customer = Customer::WithNameAndCode((string)$item->name, (int)$item->code);
                    $customers[] = $customer;                                                                               // Only added the non-existing host groups to the return array.
                }
            }

            return $customers;
        }

        /**
         * Get the customer code by name.
         *
         * @param   string          $name           The name of the customer.
         * @return  int                             The customer's code.
         */
        public function GetCodeByName($name)
        {
            $code = 0;

            if (is_string($name))
            {
                foreach ($this->GetAllCustomers() as $item)
                {
                    if ($item->name == $name)
                    {
                        $code = (int)$item->code;
                        break;
                    }
                }
            }

            return $code;
        }

        /**
         * @param   int             $code           The customer's code.
         * @return  string                          The name of the customer.
         */
        public function GetNameByCode($code)
        {
            $name = "";

            if (is_int($code))
            {
                foreach ($this->GetAllCustomers() as $item)
                {
                    if ($item["code"] == $code)
                    {
                        $name = $item["name"];
                        break;
                    }
                }
            }

            return $name;
        }

        /**
         * Evaluate whether the export date is recent (not older than the given refresh rate).
         * @remarks The XML is updated every 2 hours by the Zabbix Server.
         *
         * @return  bool                            True if the XML is recent; otherwise false.
         */
        public function EvaluateExportDate()
        {
            $isRecent = false;

            $refreshrate = (int)$this->xml->xpath("//refreshrate")[0];
            $exportDate = (string)$this->xml->xpath("//exportdate")[0];

            $then = date_create($exportDate);
            $now = date_create(date("y-m-d h:i:s"));
            $diff = date_diff($then, $now);

            if ($diff->y == 0 && $diff->m == 0 && $diff->d == 0)
            {
                if (($diff->h < $refreshrate && $diff->i < 60) || ($diff->h == $refreshrate && $diff->i == 0))
                {
                    $isRecent = true;
                }
            }

            return $isRecent;
        }

        #endregion
    }

    /**
     * Class Customer
     *
     * Provides an object to the user that represents a customer with a name and code.
     *
     * @package WebServices
     */
    class Customer
    {
        #region Public Fields

        /**
         * @var     string                      Gets or sets name of the customer.
         */
        public $name = "";

        /**
         * @var     int                         Gets or sets code of the customer.
         */
        public $code = 0;

        #endregion

        #region Constructors

        /**
         * Customer constructor.
         */
        public function __construct()
        { }

        /**
         * @param   string      $name           The name of the customer.
         * @param   string      $code           The code of the customer.
         * @return  Customer                    An instance of the Customr class.
         */
        public static function WithNameAndCode($name, $code)
        {
            $instance = new self();
            $instance->name = $name;
            $instance->code = $code;

            return $instance;
        }

        #endregion
    }
}