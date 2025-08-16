import {wpapLoading, wpapFloatMessage} from "./functions.js";

jQuery(document).ready(function ($) {
    const requestSettings = {
        type: "post",
        url: WPAPPanelFormHandler.ajaxUrl,
        cache: false,
        processData: false,
        contentType: false,
    };

    let formIds = "#user-edit-form";
    formIds += ",#wpap-change-password";
    formIds += ",#wpap-new-ticket-form";
    formIds += ",#wpap-reply-ticket-form";
    formIds += ",#wpap-message-reply-form";

    $(formIds).on("submit", function (e) {
        e.preventDefault();
        const parent = $(this).parents("#wpap-content");
        const form = $(this);
        const data = new FormData(form[0]);
        let formAction = form.find("input[name=action]").val();
        data.append("action", `wpap_${formAction}`);
        wpapLoading();

        $.ajax({
            ...requestSettings,
            data: data,
            success: function (response) {
                wpapLoading(false);

                if (response.hasOwnProperty("reload")) {
                    wpapFloatMessage(response.msg, response.status);
                } else {
                    wpapFloatMessage(response.msg, response.status);
                }
            },
            error: function (error) {},
        });
    });

    $("#wpap-add-phone-form").on("submit", function (e) {
        e.preventDefault();
        const parent = $(this).parents("#wpap-content");
        const form = $(this);
        const data = new FormData(form[0]);
        data.append("action", "wpap_add_phone");
        sessionStorage.setItem("wpapPhone", form.find("input[name=phone]").val());
        wpapLoading();

        $.ajax({
            ...requestSettings,
            data: data,
            success: function (response) {
                wpapLoading(false);
                wpapFloatMessage(response.msg, response.status);

                if (response.status === "success") {
                    form.hide(0);
                    $("#wpap-verify-add-phone-form").show();
                }
            },
            error: function (error) {
            },
        });
    });

    $("#wpap-verify-add-phone-form").on("submit", function (e) {
        e.preventDefault();
        const parent = $(this).parents("#wpap-content");
        const form = $(this);
        const data = new FormData(form[0]);
        data.append("action", "wpap_add_phone_verify");
        data.append("phone", sessionStorage.getItem("wpapPhone"));
        wpapLoading();

        $.ajax({
            ...requestSettings,
            data: data,
            success: function (response) {
                wpapLoading(false);
                wpapFloatMessage(response.msg, response.status);

                if (response.status === "success") {
                    sessionStorage.removeItem("wpapPhone");
                }
            },
            error: function (error) {
            },
        });
    });

    $("#wpap-email-send-code-form").on("submit", function (e) {
        e.preventDefault();
        const parent = $(this).parents("#wpap-content");
        const form = $(this);
        const data = new FormData(form[0]);
        data.append("action", "wpap_email_send");
        sessionStorage.setItem("wpapEmail", form.find("input[name=email]").val());
        wpapLoading();

        $.ajax({
            ...requestSettings,
            data: data,
            success: function (response) {
                wpapLoading(false);
                wpapFloatMessage(response.msg, response.status);

                if (response.status === "success") {
                    form.hide(0);
                    $("#wpap-email-verify-form").show(0);
                }
            },
            error: function (error) {
            },
        });
    });

    $("#wpap-email-verify-form").on("submit", function (e) {
        e.preventDefault();
        const parent = $(this).parents("#wpap-content");
        const form = $(this);
        const data = new FormData(form[0]);
        data.append("action", "wpap_email_verify");
        data.append("session_email", sessionStorage.getItem("wpapEmail"));
        wpapLoading();

        $.ajax({
            ...requestSettings,
            data: data,
            success: function (response) {
                wpapLoading(false);
                wpapFloatMessage(response.msg, response.status);

                if (response.status === "success") {
                    sessionStorage.removeItem("wpapEmail");
                }
            },
            error: function (error) {},
        });
    });
});
