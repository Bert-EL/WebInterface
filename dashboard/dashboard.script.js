/**
 * Created by developer on 23/12/2015.
 */

/**
 * jQuery scripts
 *
 * Won't work unless the page finished loading (references!).
 * Another solution would be to place the JavaScript at the bottom of the page.
 */
$(document).ready(function()
{
    /**
     * Navigate the iFrame to the Zabbix Webportal when the page is loaded.
     */
    $(window).load(function()
    {
        $("#monitoringboard").attr("src", "http://172.16.252.1//zabbix/index.php");
    });
});