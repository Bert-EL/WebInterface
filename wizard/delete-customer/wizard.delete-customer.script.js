/**
 * Created by developer on 29/12/2015.
 */

$(window).load(function()
{
    $("html").css("cursor", "default");
});

$(document).ready(function()
{
    var customer = $("#cbox_Customer");
    var s2Users = $("#s2_Users");
    var s2Hosts = $("#s2_Hosts");
    var userGroup = $("#chbox_UserGroup");
    var hostGroup = $("#chbox_HostGroup");
    var template = $("#chbox_Template");
    var action = $("#chbox_Action");
    var s2Elements = $(".select2-multiple");

    var ClearError = function(node)
    {
        if (node.closest(".form-group").hasClass("has-error"))
        {
            node.closest(".form-group").removeClass("has-error");
            $(node.selector + "-error").remove();
        }
    };

    $("#btn_Clear").click(function()
    {
        customer.val([]);

        if (userGroup.is(":checked"))
        {
            s2Users.prop("disabled", true);
            userGroup.parent().addClass("checked");
        }

        if (hostGroup.is(":checked"))
        {
            s2Hosts.prop("disabled", true);
            hostGroup.parent().addClass("checked");
        }

        template.parent().addClass("checked");
        action.parent().addClass("checked");
        s2Users.select2("val", "");
        s2Hosts.select2("val", "");

        ClearError(customer);
        ClearError(s2Users);
        ClearError(s2Hosts);
    });

    s2Elements.select2(
    {
        placeholder: "Selecteer geen, één of meer elementen.",
        minimumResultsForSearch: Infinity
    });

    customer.change(function()
    {
        $("html").css("cursor", "wait");
        s2Users.addClass("ignore");
        s2Hosts.addClass("ignore");
        $("#form-delete").submit();
    });

    s2Elements.change(function(e)
    {
        var node = $(e.target);
        var parent = node.parent();
        var length = parent.find("li.select2-selection__choice").length;

        if (length >= 1)
        {
            if ($(e).closest(".form-group").hasClass("has-error"))
            {
                node.closest(".form-group").addClass("has-success").removeClass("has-error");
                $("#" + e.target.id + "-error").remove();
            }
        }

        return (length >= 1);
    });

    $.validator.addMethod("s2More", function(value, e)
    {
        var node = $(e);
        var parent = node.parent();
        var length = parent.find("li.select2-selection__choice").length;

        return (length >= 1);
    }, "Dit veld is vereist.");

    userGroup.change(function()
    {
        s2Users.prop("disabled", userGroup.is(":checked"));
    });

    hostGroup.change(function()
    {
        s2Hosts.prop("disabled", hostGroup.is(":checked"));
    });

    /**
     * http://jqueryvalidation.org/validate
     */
    $("#form-delete").validate(
    {
        ignore: ".ignore",
        errorElement: "span",
        errorClass: "help-block help-block-error",
        rules:
        {
            cbox_Customer: "required"
        },
        highlight: function(element)
        {
            $(element).closest(".form-group").addClass("has-error").removeClass("has-success");
        },
        unhighlight: function(element) //, errorClass, validClass
        {
            if ($(element).closest(".form-group").hasClass("has-error"))
            {
                $(element).closest(".form-group").addClass("has-success").removeClass("has-error");
            }
        },
        errorPlacement: function(error, element)
        {
            if (element.parent(".input-group").length)
            {
                error.insertAfter(element.parent());
            }
            else if (element.hasClass("select2-multiple"))                                                                  // Select2 combobox support.
            {
                error.insertAfter(element.next("span"));
            }
            else
            {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form)
        {
            form.submit();
        }
    });

    $.extend($.validator.messages,
    {
        required: "Dit veld is vereist.",
        digits: "Geef enkel cijfers in."
    });
});
