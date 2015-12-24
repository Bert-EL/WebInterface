/**
 * Created by developer on 18/12/2015.
 */

/**
 * Preview the hostname by parsing the prefix and name with the customer code.
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
 * Toggle and clear the UseIP and DNS textboxes based on the status of the UseIP checkbox.
 */
function UseIPCheckedChanged()
{
    var isUseIPChecked = document.getElementById("chbox_UseIP").checked;

    if (isUseIPChecked)
    {
        document.getElementById("tbox_DNS").value = "";
        document.getElementById("tbox_DNS").setAttribute("readonly", "readonly");

        document.getElementById("tbox_IP").removeAttribute("readonly");
    }
    else
    {
        document.getElementById("tbox_IP").value = "";
        document.getElementById("tbox_IP").setAttribute("readonly", "readonly");

        document.getElementById("tbox_DNS").removeAttribute("readonly");
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

/**
 * jQuery scripts
 *
 * Won't work unless the page finished loading (references!).
 * Another solution would be to place the JavaScript at the bottom of the page.
 */
$(document).ready(function()
{
    /**
     * Submit the form when an item is selected in the combobox.
     */
    $("#cbox_Customer").change(function()
    {
        $("#fInit").submit();
    });

    //$("#tbox_IP").bind("change paste keyup", function()
    //{
    //    $("#hidden_IP").val($("#tbox_IP").val());
    //});
    //
    //$("#tbox_DNS").bind("change paste keyup", function()
    //{
    //    $("#hidden_DNS").val($("#tbox_DNS").val());
    //});

    $.validator.addMethod("validIP", function(value, e)
    {
        var split = value.split('.');

        if (split.length != 4)
        {
            return false;
        }

        for (var i = 0; i < split.length; i++)
        {
            var s = split[i];

            if (s.length == 0 || isNaN(s) || s<0 || s>255)
            {
                return false;
            }
        }

        return true;
    }, "Geef een geldig IPv4 adres in. Bijvoorbeeld: 192.168.1.1");

    /**
     * http://jqueryvalidation.org/validate
     */
    $("#fHost").validate(
    {
        errorElement: "span",
        errorClass: "help-block help-block-error",
        rules:
        {
            cbox_HostGroup:
            {
                required: true
            },
            tbox_Prefix:
            {
                required: true
            },
            tbox_Host:
            {
                required: true
            },
            tbox_IP:
            {
                // Place in the submitHandler?
                required: function(element)
                {
                    var isChecked = $("#chbox_UseIP").is(":checked");

                    if (!isChecked)
                    {
                        $(element).closest(".form-group").removeClass("has-error");
                    }

                    return isChecked;
                },
                validIP: {depends: "#chbox_UseIP:checked"}
            },
            tbox_DNS:
            {
                required: "#chbox_UseIP:unchecked"
            },
            tbox_Port:
            {
                required: true,
                digits: true
            }
        },
        highlight: function(element)
        {
            $(element).closest(".form-group").addClass("has-error");
        },
        unhighlight: function(element) //, errorClass, validClass
        {
            $(element).closest(".form-group").removeClass("has-error");
        },
        submitHandler: function(form)
        {
            form.submit();
        }
   });
});

