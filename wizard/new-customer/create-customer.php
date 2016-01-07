<?php

/**
 * Created by PhpStorm.
 * User: developer
 * Date: 6/01/2016
 * Time: 11:54
 */

namespace WebServices
{
    require_once dirname(dirname(__DIR__)) . '/include/ZabbixAPI.php';
    require_once dirname(dirname(__DIR__)) . '/include/ZabbixClasses.php';
    require_once dirname(dirname(__DIR__)) . '/include/CustomerParser.php';

    class CreateCustomer
    {
        #region Private Fields

        /**
         *
         * Gets or sets the Zabbix API.
         *
         * @var ZabbixAPI
         */
        private $zapi;

        #endregion

        #region Construtor

        /**
         * CreateCustomer constructor.
         *
         * @param   ZabbixAPI       $zapi       An object representing the Zabbix API.
         */
        function __construct($zapi)
        {
            $this->zapi = $zapi;
        }

        #endregion

        #region Public Methods

        /**
         * Evaluate the passed element and provid feedback.
         *
         * @param   int             $eType      Value that represents the NameType.
         * @param   BaseItem        $e          The base item that'll be evaluated.
         * @return  string                      Feedback on the operation.
         */
        public function EvaluateElement($eType, $e)
        {
            $feedback = "";

            if (is_int($eType) && !is_null($e))
            {
                if ($e->id !== 0)
                {
                    $feedback .= "Created the " . $this->zapi->longNameTypes[$eType] . " '" . $e->name . "' with ID #" . $e->id . ".<br/>";
                }
                else
                {
                    $feedback .= "Failed to create the " . $this->zapi->longNameTypes[$eType] . "!<br/>";
                }
            }

            return $feedback;
        }

        #endregion
    }
}