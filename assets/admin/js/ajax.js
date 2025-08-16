jQuery(document).ready(function ($) {
    function loading(form, show = true) {
        if (show) {
            form.find(".wpap-btn-text").hide(0);
            form.find(".wpap-loading").show(0);
        } else {
            form.find(".wpap-btn-text").show(0);
            form.find(".wpap-loading").hide(0);
        }
    }

    function success(form) {
        form.find("button .wpap-btn-text").hide(0);
        form.find("button .wpap-success-btn-text").show(0);

        setTimeout(function () {
            form.find("button .wpap-btn-text").show(0);
            form.find("button .wpap-success-btn-text").hide(0);
        }, 1000);

        form.find(".wpap-error-message").remove();
    }

    function message(message, form, type) {
        form.find(`.wpap-${type}-message`).remove();

        let html = `
            <div class="wpap-message wpap-message-${type}">
                <i class="ri-checkbox-circle-line"></i>
                <strong>${message}</strong>
            </div>
        `;

        form.find("button[type=submit]").parent().before(html);
    }

    $(document).on("submit", ".wpap-form, .wpap-popup-form, #wpap-dash-boxes-section, .wpap-sms-settings-form", function (e) {
        e.preventDefault();
        const form = $(this);
        const data = new FormData($(this)[0]);
        const settingsName = form.find("input[name=form]");
        data.append("action", settingsName.val());
        loading(form);

        $.ajax({
            type: "post",
            url: WPAPAjax.ajaxurl,
            data: data,
            cache: false,
            processData: false,
            contentType: false,
            success: function (response) {
                loading(form, false);

                if (response.status === "error") {
                    message(response.msg, form, response.status);
                }

                if (response === "success" || response.status === "success") {
                    success(form);
                }

                if (response.hasOwnProperty("provider")) {
                    $("#wpap-current-provider").text(response.provider);
                }

                if (response.hasOwnProperty("role_name")) {
                    $("#wpap-new-role-msg").remove();
                    $("#wpap-notfound-role").remove();

                    $("#wpap-roles-wrap").append(`
                        <div>
                            <span>${response.role_name}</span>
                            <i id="wpap-remove-role" class="ri-close-line" data-nonce="${response.role_nonce}" data-role="${response.role}"></i>
                        </div>
                    `);
                }
            },
            error: function (error) {
            },
        });
    });

    $(document).on("click", ".wpap-delete-supporter", function (e) {
        e.stopPropagation();
        e.preventDefault();

        if (!confirm("آیا از حذف پشتیبان مطمئنید؟")) {
            return;
        }

        const supportersWrap = $("#wpap-supporters-wrap");
        let parent = $(this).parent();
        let user = $(this).data("user");
        let nonce = $(this).data("nonce");
        let delIcon = $(this).find("i");
        let loading = parent.find(".wpap-loading");

        delIcon.hide(0);
        loading.fadeIn(100);

        $.ajax({
            type: "post",
            url: WPAPAjax.ajaxurl,
            data: {
                action: "wpap_delete_supporter",
                nonce: nonce,
                user: user,
            },
            success: function (response) {
                delIcon.fadeIn(200);
                loading.fadeOut(100);

                if (response === "success") {
                    parent.remove();

                    if (supportersWrap.find(".wpap-supporter").length === 0) {
                        supportersWrap.text(WPAPAjax.notfoundSupporterMessage);
                    }
                }
            },
        });
    });

    $(document).on("click", "#wpap-remove-role", function () {
        const rolesWrap = $("#wpap-roles-wrap");
        const newRole = $("#wpap-new-role-after-delete").val();
        const newRoleDisplayText = $(
            "#wpap-new-role-after-delete option:selected"
        );
        const roleElement = $(this).parent();
        let role = $(this).data("role");
        let nonce = $(this).data("nonce");

        if (newRole === role) {
            alert(WPAPAjax.newRoleError);
            return;
        }

        if (
            !confirm(
                `${WPAPAjax.delRoleConfirm} "${newRoleDisplayText
                    .text()
                    .trim()}".`
            )
        ) {
            return;
        }

        $.ajax({
            type: "post",
            url: WPAPAjax.ajaxurl,
            data: {
                action: "wpap_delete_role",
                role: role,
                new_role: newRole,
                nonce: nonce,
            },
            success: function (response) {
                if (response === "success") {
                    roleElement.remove();
                    $("#wpap-new-role-msg").remove();
                    $("#wpap-notfound-role").remove();

                    rolesWrap.prepend(
                        `<span id="wpap-new-role-msg"><strong>"${newRoleDisplayText.text()}"</strong> ${
                            WPAPAjax.newRoleText
                        }</span>`
                    );

                    if (!rolesWrap.children().length) {
                        rolesWrap.text(WPAPAjax.nothingRole);
                    }
                }
            },
        });
    });

    $(document).on("click", ".wpap-delete-ticket", function (e) {
        e.stopPropagation();
        e.preventDefault();

        if (!confirm(WPAPAjax.delTicketConfirm)) {
            return;
        }

        let parent = $(this).parents('tr');
        let ticket = $(this).data("ticket");

        parent.css("opacity", "0.4");

        $.ajax({
            type: "post",
            url: WPAPAjax.ajaxurl,
            data: {
                action: "wpap_delete_ticket",
                ticket: ticket,
            },
            success: function (response) {
                if (response === "success") {
                    parent.remove();
                }
            },
        });
    });

    $(document).on("click", "#wpap-reset-styles", function (e) {
        if (!confirm(WPAPAjax.resetStylesConfirm)) {
            return;
        }

        const form = $("#wpap-styles-form");
        let nonce = $(this).data("nonce");
        form.css("opacity", "0.4");

        $.ajax({
            type: "post",
            url: WPAPAjax.ajaxurl,
            data: {
                action: "wpap_reset_styles",
                nonce: nonce,
            },
            success: function (response) {
                if (response === "success") {
                    form.css("opacity", "1");

                    $(".wp-picker-container").each(function (i) {
                        let defaultColor = $(this)
                            .find(".wpap-color-field")
                            .data("default-color");
                        $(this)
                            .find(".wp-color-result")
                            .css("background-color", defaultColor);
                    });
                }
            },
        });
    });

    $(document).on("submit", "#wpap-reg-fields-form", function (e) {
        e.preventDefault();
        const form = $(this);
        const data = new FormData(form[0]);
        data.append("action", "wpap_reg_fields");
        loading(form);

        $.ajax({
            type: "post",
            url: WPAPAjax.ajaxurl,
            data: data,
            cache: false,
            processData: false,
            contentType: false,
            success: function (response) {
                loading(form, false);

                if (response.success) {
                    success(form);
                }
            },
            error: function (error) {
            },
        });
    });

    var userSelectField = $('.wpap-ajax-field');
    var searchTimeout;

    userSelectField.find('input').on('keyup', function (e) {
        var input = $(this);

        userSelectField.find('> div').show();
        userSelectField.find('> div ul').html('');

        if ('' === input.val()) {
            return;
        }

        userSelectField.find('> div .wpap-loading').show();

        clearTimeout(searchTimeout);

        searchTimeout = setTimeout(function () {
            $.ajax({
                type: 'post',
                url: WPAPAjax.ajaxurl,
                data: {
                    action: 'wpap_user_select',
                    user: input.val()
                },
                success: function(response) {
                    if (response.success) {
                        userSelectField.find('> div ul').html(response.data)
                    }
                },
                error: function (error) {},
                complete: function () {
                    userSelectField.find('> div .wpap-loading').hide();
                },
            })
        }, 500);
    })
});
