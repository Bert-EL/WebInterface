<?php

/**
 * Created by PhpStorm.
 * User: developer
 * Date: 1/12/2015
 * Time: 10:22
 */

namespace WebServices
{
    abstract class WebServiceAPI
    {
        /**
         * @var     resource                   Contains an object that allows PHP to transfer data by HTTP (...).
         */
        private $Curl;

        /**
         * @var     string                      Gets or sets the username that's required to use the API.
         */
        protected $Username = "";

        /**
         * @va      string                      Gets or sets the password that's required to use the API.
         */
        protected $Password = "";

        /**
         * @var     string                      Gets or sets the host URL.
         */
        protected $HostURL = "";

        /**
         * @var     string                      Gets or sets the JSON content type.
         */
        protected $ContentType = "";

        /**
         * @var     int                         Gets or sets the identification number for the API call.
         */
        protected $Nonce = 0;

        /**
         * Initialze the webservice API and set up the Curl communication object.
         *
         * @param   string      $username       The username that'll be used to connect with the API.
         * @param   string      $password       The password that'll be used to connect with the API.
         * @param   string      $contentType    The content type that the API expects.
         * @param   string      $hostURL        The URL to the API.
         */
        function Initialize($username, $password, $contentType, $hostURL)
        {
            $this->Username = $username;
            $this->Password = $password;
            $this->ContentType = $contentType;
            $this->HostURL = $hostURL;

            $this->Curl = curl_init($hostURL);
        }

        /*
         * Send the passed JSON data to the Zabbix API.
         *
         * @param   string      $data           The data that'll be passed to the Zabbix API.
         */
        public function Send($data)
        {
            curl_setopt($this->Curl, CURL_HTTP_VERSION_1_0, true);
            curl_setopt($this->Curl, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($this->Curl, CURLOPT_HTTPHEADER, array("Content-Type: " . $this->ContentType));                         // Set the 'Content-Type' of the HTTP Request.
            curl_setopt($this->Curl, CURLOPT_POSTFIELDS, json_encode($data));                                                   // Post the JSON encoded data; this may/can be a class.
            curl_setopt($this->Curl, CURLOPT_RETURNTRANSFER, true);                                                             // Return the actual response of the HTTP Request; no only 'true' or 'false'.

            $result = curl_exec($this->Curl);

            if (curl_errno($this->Curl))
            {
                $result = curl_error($this->Curl);
            }

            return json_decode($result, true);                                                                                  // Decode the resulting JSON message to an array.
        }

        /**
         * Authenticate the user.
         *
         * @return  void
         */
        abstract function Authenticate();

        /**
         * Evaluate whether the authentication token is valid.
         *
         * @return  bool                        True if the authentication token is valid; otherwise false.
         */
        abstract function IsValidAuthToken();

        /**
         * Gets the API authorisation token;
         *
         * @return  string                      The authentication token if successful; otherwise an empty string.
         */
        abstract function GetAuthToken();

        /**
         * Evaluate whether the API response is valid;
         *
         * @param   string      $response       The API response.
         * @return  bool                        True if the API response is valid; otherwise false.
         */
        abstract  function IsValidResponse($response);
    }
}