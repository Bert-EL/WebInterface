<?php

/**
 * Created by PhpStorm.
 * User: developer
 * Date: 30/12/2015
 * Time: 15:53
 */

namespace WebServices
{
    require_once dirname(dirname(__DIR__)) . '/include/ZabbixAPI.php';
    require_once dirname(dirname(__DIR__)) . '/include/ZabbixClasses.php';
    require_once dirname(dirname(__DIR__)) . '/include/CustomerParser.php';

    class DeleteCustomer
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
         * DeleteCustomer constructor.
         *
         * @param   ZabbixAPI       $zapi       An object representing the Zabbix API.
         */
        function __construct($zapi)
        {
            $this->zapi = $zapi;
        }

        #endregion

        #region Delete user(s) and/or the user group.

        /**
         * Delete the passed users in the user group.
         *
         * @param   array|int   $users          The users that'll be deleted.
         * @return  string                      Feedback on the operation.
         */
        public function DeleteCustomerUsers($users)
        {
            return $this->DeleteCustomerGroupElements($users, true);
        }

        /**
         * Delete all the users in the user group.
         *
         * @param   int         $userGroupID    The user group ID of the customer.
         * @return  string                      Feedback on the operation.
         */
        public function DeleteAllCustomerUsers($userGroupID)
        {
            $feedback = "";

            $users = $this->GetIDs($userGroupID, true);
            $feedback .= $this->DeleteCustomerGroupElements($users, true);

            return $feedback;
        }

        /**
         * Delete all the users in the user group and the group itself.
         *
         * @param   int         $userGroupID    The user group ID of the customer.
         * @return  string                      Feedback on the operation.
         */
        public function DeleteCustomerUserGroup($userGroupID)
        {
            $feedback = "";

            $users = $this->GetIDs($userGroupID, true);
            $feedback .= $this->DeleteCustomerGroupElements($users, true);
            $feedback .= $this->DeleteCustomerGroup($userGroupID, true);

            return $feedback;
        }

        #endregion

        #region Delete host(s) and/or the host group.

        /**
         * Delete the passed hosts in the host group.
         *
         * @param   array|int   $hosts          The hosts that'll be deleted.
         * @return  string                      Feedback on the operation.
         */
        public function DeleteCustomerHosts($hosts)
        {
            return $this->DeleteCustomerGroupElements($hosts, false);
        }

        /**
         * @param   int         $hostGroupID    The host group ID of the customer.
         * @return  string                      Feedback on the operation.
         */
        public function DeleteAllCustomerHosts($hostGroupID)
        {
            $feedback = "";

            $hosts = $this->GetIDs($hostGroupID, false);
            $feedback .= $this->DeleteCustomerGroupElements($hosts, false);

            return $feedback;
        }

        /**
         * Delete all the hosts in the host group and the group itself.
         *
         * @param   int         $hostGroupID    The host group ID of the customer.
         * @return  string                      Feedback on the operation.
         */
        public function DeleteCustomerHostGroup($hostGroupID)
        {
            $feedback = "";

            $hosts = $this->GetIDs($hostGroupID, false);
            $feedback .= $this->DeleteCustomerGroupElements($hosts, false);
            $feedback .= $this->DeleteCustomerGroup($hostGroupID, false);

            return $feedback;
        }

        #endregion

        #region Delete template(s).

        /**
         * Delete the template.
         *
         * @param   int         $templateID     The ID of the template.
         * @return  string                      Feedback on the operation.
         */
        public function DeleteCustomerTemplate($templateID)
        {
            $feedback = "";

            if (is_int($templateID))
            {
                $template = $this->zapi->GetTemplateByID($templateID);

                if (!is_null($template))
                {
                    $this->zapi->DeleteTemplate($templateID);
                    $feedback .= "Deleted the template '" . $template->name . "' with ID #" . $template->id . "<br/>";
                }
            }

            return $feedback;
        }

        #endregion

        #region Delete action(s).

        /**
         * Delete the action.
         *
         * @param   int         $actionID       The ID of the action.
         * @return  string                      Feedback on the operation.
         */
        public function DeleteCustomerAction($actionID)
        {
            $feedback = "";

            if (is_int($actionID))
            {
                $action = $this->zapi->GetActionByID($actionID);

                if (!is_null($action))
                {
                    $this->zapi->DeleteAction($actionID);
                    $feedback .= "Deleted the action '" . $action->name . "' with ID #" . $action->id . "<br/>";
                }
            }

            return $feedback;
        }

        #endregion

        #region Private Methods

        /**
         * Delete the user or host group.
         *
         * @param   int         $groupID        The ID of the user or host group.
         * @param   boolean     $isUserGroup    Whether the group contains users or hosts.
         * @return  string                      Feedback on the operation.
         */
        private function DeleteCustomerGroup($groupID, $isUserGroup)
        {
            $feedback = "";

            if (is_int($groupID) && is_bool($isUserGroup))
            {
                if ($isUserGroup)
                {
                    $userGroup = $this->zapi->GetUserGroupByID($groupID);

                    if (!is_null($userGroup))
                    {
                        $this->zapi->DeleteUserGroup($groupID);
                        $feedback .= "Deleted the user group '" . $userGroup->name . "' with ID #" . $userGroup->id . ".<br/>";
                    }
                }
                else
                {
                    $hostGroup = $this->zapi->GetHostGroupByID($groupID);

                    if (!is_null($hostGroup))
                    {
                        $this->zapi->DeleteHostGroup($groupID);
                        $feedback .= "Deleted the host group '" . $hostGroup->name . "' with ID #" . $hostGroup->id . ".<br/>";
                    }
                }
            }

            return $feedback;
        }

        /**
         * Delete the passed elements (users or hosts) depending on the 'isUserGroup' parameter.
         *
         * @param   array|int   $elements       The elements (users or hosts) that'll be deleted.
         * @param   boolean     $isUserGroup    Whether the elements are users or hosts.
         * @return  string                      Feedback on the operation.
         */
        private function DeleteCustomerGroupElements($elements, $isUserGroup)
        {
            $feedback = "";

            if (is_array($elements) && count($elements) > 0)
            {
                foreach ($elements as $element)
                {
                    if (is_int((int)$element))
                    {
                        if ($isUserGroup)
                        {
                            $user = $this->zapi->GetUserByID($element);

                            if (!is_null($user))
                            {
                                $this->zapi->DeleteUser($element);                                                         // TODO Replace with the actual delete user code.
                                $feedback .= "Deleted the user '" . $user->name . "' with ID #" . $user->id . ".<br/>";
                            }
                        }
                        else
                        {
                            $host = $this->zapi->GetHostByID($element);

                            if (!is_null($host))
                            {
                                $this->zapi->DeleteHost($element);                                                         // TODO Replace with the actual delete user code.
                                $feedback .= "Deleted the host '" . $host->name . "' with ID #" . $host->id . ".<br/>";
                            }
                        }
                    }
                }
            }

            return $feedback;
        }

        /**
         * Extract the user or host IDs from the passed elements array.
         *
         * @param   int         $groupID        The id of the user or host group.
         * @param   boolean     $isUserGroup    Whether the elements are users or hosts.
         * @return  array                       An array containing the element IDs.
         */
        private function GetIDs($groupID, $isUserGroup)
        {
            $IDs = array();
            $elements = ($isUserGroup) ? ($this->zapi->GetUsersByUserGroup($groupID)) : ($this->zapi->GetHostsByHostGroup($groupID));

            foreach ($elements as $item)
            {
                $IDs[] = $item->id;
            }

            return $IDs;
        }

        #endregion
    }
}