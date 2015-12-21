/**
 * Created by developer on 18/12/2015.
 */

/**
 *
 */
function PreviewHostname()
{
    var code = document.getElementById("hidden_code").value;
    var prefix = document.getElementById("tbox_Prefix").value;
    var host = document.getElementById("tbox_Host").value;

    if (prefix.length > 0 && host.length > 0)
    {
        var name = PadLeft(code, 4) + "-" + prefix + "-" + host;
        document.getElementById("tbox_Preview").value = name;
        document.getElementById("hidden_hostname").value = name;
    }
    else
    {
        document.getElementById("tbox_Preview").value = "";
        document.getElementById("hidden_hostname").value = "";
    }
}

/**
 * Pad the given string with zero's.
 *
 * @param           {string}    value           The string that'll be padded with zero's.
 * @param           {int}       length          The amount of zero's that'll be padded to the given string.
 * @returns         {string}                    The padded string.
 */
function PadLeft(value, length)
{
    if (value.toString().length < length)
    {
        return PadLeft("0" + value, length)
    }
    else
    {
        return value.toString();
    }
}

/**
 *
 */
function UseIPCheckedChanged()
{
    var isUseIPChecked = document.getElementById("chbox_UseIP").checked;

    if (isUseIPChecked)
    {
        document.getElementById("tbox_DNS").value = "";
        document.getElementById("hidden_DNS").value = "";
        document.getElementById("tbox_DNS").setAttribute("disabled", "disabled");

        document.getElementById("tbox_IP").removeAttribute("disabled");
    }
    else
    {
        document.getElementById("tbox_IP").value = "";
        document.getElementById("hidden_IP").value = "";
        document.getElementById("tbox_IP").setAttribute("disabled", "disabled");

        document.getElementById("tbox_DNS").removeAttribute("disabled");
    }
}

/**
 * Evaluate if the interface fields are valid.
 *
 * @returns         {boolean}   True if the fields are valid; otherwise false.
 */
function EvaluateInterfaceFields()
{
    var result = false;
    var useip = document.getElementById("chbox_UseIP").checked;
    var ip = document.getElementById("tbox_IP").value;
    var dns = document.getElementById("tbox_DNS").value;
    var port = document.getElementById("tbox_Port").value;

    result &= (useip && !IsEmpty(ip)) || (!useip && !IsEmpty(dns));
    result &= !IsEmpty(port);

    return result;
}

/**
 * Evaluate whether the string is empty or null.
 *
 * @param   s       {string}    The string that'll be evaluated.
 * @returns         {boolean}   True if the string is empty/null; otherwise false.
 */
function IsEmpty(s)
{
    return (!s || s.length === 0);
}

$(document).ready(function()                                                                                    // Won't work unless the page finished loading (references!).
{                                                                                                               // Another solution would be to place the JavaScript at the bottom of the page
    $("#cbox_Customer").change(function()
    {
        $("#fInit").submit();
    });

    $("#tbox_IP").bind("change paste keyup", function()
    {
        $("#hidden_IP").val($("#tbox_IP").val());
    });

    $("#tbox_DNS").bind("change paste keyup", function()
    {
        $("#hidden_DNS").val($("#tbox_DNS").val());
    });
});