/**
 * Created by developer on 24/12/2015.
 */

var FormWizard = function()
{
    return{

        init:function()
        {
            if ($().bootstrapWizard)
            {
                var r = $("#fInit");
                var t = $(".alert-danger", r);
                var i = $(".alert-success", r);

                var a = function()
                {
                    $("#tab4 .form-control-static", r).each(function()
                    {
                        var e = $('[name="' + $(this).attr("data-display") + '"]', r);

                        if (e.is(":radio") && (e = $('[name="' + $(this).attr("data-display") + '"]:checked', r)), e.is(":text") || e.is("textarea")) $(this).html(e.val());
                        else if (e.is("select")) $(this).html(e.find("option:selected").text());
                        else if (e.is(":radio") && e.is(":checked")) $(this).html(e.attr("data-title"));
                        else if ("payment[]" == $(this).attr("data-display"))
                        {
                            var t = [];
                            $('[name="payment[]"]:checked', r).each(function()
                            {
                                t.push($(this).attr("data-title"))
                            }), $(this).html(t.join("<br>"))
                        }
                    })
                };

                var o = function(e, r, t)
                {
                    var i = r.find("li").length;
                    var o = t + 1;

                    $(".step-title", $("#form_wizard_1")).text("Step " + (t + 1) + " of " + i), jQuery("li", $("#form_wizard_1")).removeClass("done");

                    for (var n = r.find("li"), s = 0; t > s; s++)
                    {
                        jQuery(n[s]).addClass("done");
                    }

                    1 == o ? $("#form_wizard_1").find(".button-previous").hide() : $("#form_wizard_1").find(".button-previous").show(), o >= i ? ($("#form_wizard_1").find(".button-next").hide(), $("#form_wizard_1").find(".button-submit").show(), a()) : ($("#form_wizard_1").find(".button-next").show(), $("#form_wizard_1").find(".button-submit").hide()), App.scrollTo($(".page-title"))
                };

                $("#form_wizard_1").bootstrapWizard(
                {
                    nextSelector: ".button-next",
                    previousSelector: ".button-previous",
                    onTabClick: function(e, r, t, i)
                    {
                        return !1
                    },
                    onNext: function(e, a, n)
                    {
                        return i.hide(), t.hide(), 0 == r.valid() ? !1 : void o(e, a, n)
                    },
                    onPrevious: function(e, r, a)
                    {
                        i.hide(), t.hide(), o(e, r, a)
                    },
                    onTabShow: function(e, r, t)
                    {
                        var i = r.find("li").length,
                            a = t + 1,
                            o = a / i * 100;
                        $("#form_wizard_1").find(".progress-bar").css(
                            {
                                width: o + "%"
                            })
                    }
                }),$("#form_wizard_1").find(".button-previous").hide(),$("#form_wizard_1 .button-submit").click(function()
                {
                    alert("Finished! Hope you like it :)")
                }).hide()
            }
        }
    }
}();

$(document).ready(function()
{
    FormWizard.init()
});
