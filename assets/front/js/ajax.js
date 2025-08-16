import {wpapLoading, wpapFloatMessage} from './functions.js';

jQuery(document).ready(function ($) {
    $('#wpap-register-form').on('submit', function (e) {
        e.preventDefault();
        var parent = $(this).parent();
        var form = $(this);
        var data = new FormData(form[0]);
        var redirectTo = form.find('input[name=redirect_to]').val();
        data.append('action', 'wpap_register');

        $.ajax({
            type: 'post',
            url: WPAPAjax.ajaxUrl,
            data: data,
            cache: false,
            processData: false,
            contentType: false,
            beforeSend: function () {
                wpapLoading();
            },
            success: function (response) {
                var data = response.data;

                wpapFloatMessage(
                    data.hasOwnProperty('msg') ? data.msg : data,
                    response.success ? 'success' : 'error'
                );

                if (response.success) {
                    form.hide(0);

                    if (data.hasOwnProperty('redirect') && data.redirect) {
                        window.location.href = redirectTo;
                    }
                } else if (typeof grecaptcha !== 'undefined') {
                    grecaptcha.reset();
                }
            },
            error: function (error) {
            },
            complete: function () {
                wpapLoading(false);
            }
        })
    });

    $('#wpap-login-form').on('submit', function (e) {
        e.preventDefault();

        const parent = $(this).parent();
        const form = $(this);
        const data = new FormData(form[0]);

        data.append('action', 'wpap_login');

        wpapLoading();

        $.ajax({
            method: 'post',
            url: form.attr('action'),
            data: data,
            cache: false,
            processData: false,
            contentType: false,
            success: function (response) {
                wpapLoading(false);
                wpapFloatMessage(response.msg, response.status);

                if (response.hasOwnProperty('form')) {
                    sessionStorage.setItem('wpapUserId', response.user)
                    form.hide(0);
                    $(`#wpap-${response.form}-form`).show(0);
                }

                if (response.status === 'success') {
                    window.location.href = response.after_login;
                } else if (typeof grecaptcha !== 'undefined') {
                    grecaptcha.reset();
                }
            },
            error: function (error) {
            }
        })
    });

    $('#wpap-lost-password-form').on('submit', function (e) {
        e.preventDefault();
        const parent = $(this).parent();
        const form = $(this);
        const data = new FormData(form[0]);
        data.append('action', 'wpap_lost_password');
        wpapLoading();

        $.ajax({
            type: 'post',
            url: WPAPAjax.ajaxUrl,
            data: data,
            cache: false,
            processData: false,
            contentType: false,
            success: function (response) {
                wpapLoading(false);
                wpapFloatMessage(response.msg, response.status);
            },
            error: function (error) {
            }
        })
    });

    $('#wpap-reset-password-form').on('submit', function (e) {
        e.preventDefault();
        const parent = $(this).parent();
        const form = $(this);
        const data = new FormData(form[0]);
        data.append('action', 'wpap_reset_password');
        wpapLoading();

        $.ajax({
            type: 'post',
            url: WPAPAjax.ajaxUrl,
            data: data,
            cache: false,
            processData: false,
            contentType: false,
            success: function (response) {
                wpapLoading(false);
                wpapFloatMessage(response.msg, response.status);

                if (response.status === 'success') {
                    window.location.href = response.login_url;
                }
            },
            error: function (error) {
            }
        })
    });

    $('#wpap-send-activation-link-form').on('submit', function (e) {
        e.preventDefault();
        const parent = $(this).parent();
        const form = $(this);
        const data = new FormData(form[0]);
        data.append('action', 'send_activation_link');
        data.append('user_id', sessionStorage.getItem('wpapUserId'));
        wpapLoading();

        $.ajax({
            type: 'post',
            url: WPAPAjax.ajaxUrl,
            data: data,
            cache: false,
            processData: false,
            contentType: false,
            success: function (response) {
                wpapLoading(false);
                wpapFloatMessage(response.msg, response.status);

                if (response.status === 'success') {
                    sessionStorage.removeItem('wpapUserId');
                }
            },
            error: function (error) {
            }
        })
    });

    $('#wpap-force-add-mobile-form').on('submit', function (e) {
        e.preventDefault();
        const parent = $(this).parent();
        const form = $(this);
        const data = new FormData(form[0]);
        const phone = form.find('input[name=phone]');
        data.append('action', 'force_add_mobile');
        data.append('mobile_num_owner', sessionStorage.getItem('wpapUserId'));
        sessionStorage.setItem('wpapPhone', phone.val());

        if (phone.val() === '') {
            wpapFloatMessage(WPAPAjax.emptyMobileText, 'error');
            return;
        }

        wpapLoading();

        $.ajax({
            type: 'post',
            url: WPAPAjax.ajaxUrl,
            cache: false,
            processData: false,
            contentType: false,
            data: data,
            success: function (response) {
                wpapLoading(false);

                if (response.type === 'error') {
                    form.find('.wpap-loading').hide(0);
                    wpapFloatMessage(response.msg, 'error');
                }

                if (response.type === 'success') {
                    form.find('.wpap-loading').hide(0);
                    wpapFloatMessage(response.msg, 'success');
                    $('#wpap-force-add-mobile-form').hide(0);
                    $('#wpap-force-verification-form').show(0);
                }
            }
        })
    })

    $('#wpap-force-verification-form').submit(function (e) {
        e.preventDefault();
        const parent = $(this).parent();
        const form = $(this);
        const data = new FormData(form[0]);
        data.append('action', 'force_verify');
        data.append('user_phone', sessionStorage.getItem('wpapPhone'));
        data.append('mobile_num_owner', sessionStorage.getItem('wpapUserId'));

        if (form.find('input[type=verification_code]').val() === '') {
            wpapFloatMessage(WPAPAjax.emptyVerifyCodeText, 'error');
            return;
        }

        wpapLoading();

        $.ajax({
            type: "post",
            url: WPAPAjax.ajaxUrl,
            cache: false,
            processData: false,
            contentType: false,
            data: data,
            success: function (response) {
                wpapLoading(false);

                if (response.type === 'error') {
                    form.find('.wpap-loading').hide(0);
                    wpapFloatMessage(response.msg, 'error');
                }

                if (response.type === 'success') {
                    sessionStorage.removeItem('wpapPhone');
                    sessionStorage.removeItem('wpapUserId');
                    form.find('.wpap-loading').hide(0);
                    $('#wpap-force-verification-form').hide(0);
                    wpapFloatMessage(response.msg, 'success');
                    window.location.href = `${WPAPAjax.panelUrl}`;
                }
            },
            error: function (error) {
            }
        })
    });

    $('#wpap-sms-send-form').on('submit', function (e) {
        e.preventDefault();
        const parent = $(this).parent();
        const form = $(this);
        const data = new FormData(form[0]);
        const phone = form.find('input[name=phone]').val();
        data.append('action', 'sms_register_login_send_code');
        data.append('user_phone', phone);
        sessionStorage.setItem('wpapPhone', phone);

        if (phone === '') {
            wpapFloatMessage(WPAPAjax.emptyMobileText, 'error');
            return;
        }

        wpapLoading();

        $.ajax({
            type: 'post',
            url: WPAPAjax.ajaxUrl,
            data: data,
            cache: false,
            processData: false,
            contentType: false,
            success: function (response) {
                wpapLoading(false);
                wpapFloatMessage(response.msg, response.status);

                if (response.hasOwnProperty('section')) {
                    form.hide(0)
                    $(`#wpap-sms-${response.section}-form`).show();
                }
            }
        })
    })

    $('#wpap-sms-register-form').submit(function (e) {
        e.preventDefault();
        const parent = $(this).parent();
        const form = $(this);
        const data = new FormData(form[0]);
        const redirectTo = form.find('input[name=redirect_to]').val();
        const code = form.find('input[name=verification_code]');
        const pass = form.find('input[name=user_pass]');
        const agree = $('#wpap-sms-reg-agree');
        data.append('action', 'sms_register_verify');
        data.append('user_phone', sessionStorage.getItem('wpapPhone'));

        if (code.val() === '') {
            wpapFloatMessage(WPAPAjax.emptyVerifyCodeText, 'error');
            return;
        }

        if (pass.length > 0) {
            if (pass.val() === '') {
                wpapFloatMessage(WPAPAjax.emptyPass, 'error');
                return;
            }

            if (pass.val().length < WPAPAjax.passMin) {
                wpapFloatMessage(WPAPAjax.passMinText, 'error');
                return;
            }
        }

        if (agree.length > 0 && !agree.is(':checked')) {
            wpapFloatMessage(WPAPAjax.agreeErrorText, 'error');
            return;
        }

        wpapLoading();

        $.ajax({
            type: "post",
            url: WPAPAjax.ajaxUrl,
            cache: false,
            processData: false,
            contentType: false,
            data: data,
            success: function (response) {
                wpapLoading(false);
                wpapFloatMessage(response.msg, response.status);

                if (response.status === 'success') {
                    $('#wpap-sms-register-form').hide(0);
                    $('#wpap-form-links').hide(0);
                    window.location.replace(redirectTo);
                }
            }
        })
    });

    $('#wpap-sms-login-form').submit(function (e) {
        e.preventDefault();
        const parent = $(this).parent();
        const form = $(this);
        const data = new FormData(form[0]);
        const code = form.find('input[name=verification_code]');
        const redirectTo = form.find('input[name=redirect_to]').val();
        data.append('action', 'sms_login_verify');
        data.append('user_phone', sessionStorage.getItem('wpapPhone'));

        if (code.val() === '') {
            wpapFloatMessage(WPAPAjax.emptyVerifyCodeText, 'error');
            return;
        }

        wpapLoading();

        $.ajax({
            type: "post",
            url: WPAPAjax.ajaxUrl,
            cache: false,
            processData: false,
            contentType: false,
            data: data,
            success: function (response) {
                wpapLoading(false);
                wpapFloatMessage(response.msg, response.status);

                if (response.status === 'success') {
                    $('#wpap-sms-login-form').hide(0);
                    $('#wpap-form-links').hide(0);
                    window.location.replace(redirectTo);
                }
            }
        })
    });

    $('#wpap-sms-lost-pass-form').on('submit', function (e) {
        e.preventDefault();
        const parent = $(this).parent();
        const form = $(this);
        const data = new FormData(form[0]);
        const phone = form.find('input[name=phone]').val();
        data.append('action', 'wpap_sms_lost_pass');
        sessionStorage.setItem('wpapPhone', phone);

        if (phone === '') {
            wpapFloatMessage(WPAPAjax.emptyMobileText, 'error');
            return;
        }

        wpapLoading();

        $.ajax({
            type: 'post',
            url: WPAPAjax.ajaxUrl,
            data: data,
            cache: false,
            processData: false,
            contentType: false,
            success: function (response) {
                wpapLoading(false);
                wpapFloatMessage(response.msg, response.status)

                if (response.status === 'success') {
                    $('#wpap-sms-lost-pass-form').hide(0);
                    $('#wpap-sms-reset-pass-verify-form').show(0);
                }
            }
        })
    })

    $('#wpap-sms-reset-pass-verify-form').submit(function (e) {
        e.preventDefault();
        const parent = $(this).parent();
        const form = $(this);
        const data = new FormData(form[0]);
        const newPass = form.find('input[name=new_password]').val();
        const code = form.find('input[name=verification_code]').val();
        data.append('action', 'sms_pass_reset_verify');
        data.append('user_phone', sessionStorage.getItem('wpapPhone'));

        if (newPass === '') {
            wpapFloatMessage(WPAPAjax.emptyPass, 'error');
            return;
        }

        if (code === '') {
            wpapFloatMessage(WPAPAjax.emptyVerifyCodeText, 'error');
            return;
        }

        wpapLoading();

        $.ajax({
            type: "post",
            url: WPAPAjax.ajaxUrl,
            data: data,
            cache: false,
            processData: false,
            contentType: false,
            success: function (response) {
                wpapLoading(false);
                wpapFloatMessage(response.msg, response.status)

                if (response.status === 'success') {
                    sessionStorage.removeItem('wpapPhone');
                    $('#wpap-sms-reset-pass-verify-form').hide(0);
                }
            }
        })
    });

    $('#wpap-theme-toggle').on('click', function (e) {
        e.preventDefault();
        var panel = $('#wpap-user-panel');
        var toggleButton = $(this);
        var theme = panel.attr('data-theme');

        $.ajax({
            type: 'post',
            url: WPAPAjax.ajaxUrl,
            data: {
                action: 'wpap_panel_theme',
                theme: theme === 'dark' ? 'light' : 'dark'
            },
            beforeSend: function () {
                wpapFloatMessage(WPAPAjax.themeChangeLoadingMsg);
            },
            success: function (response) {
                if (response.success) {
                    panel.attr('data-theme', theme === 'dark' ? 'light' : 'dark');
                    toggleButton.find('i').toggleClass('ri-contrast-2-line ri-sun-fill');
                    wpapFloatMessage(WPAPAjax.themeChangeSuccessMsg, 'success');
                }
            },
            error: function (error) {
            }
        })
    });

    $('#wpap-delete-avatar-btn').on('click', function (e) {
        e.preventDefault();
        const btn = $(this);
        const userID = $(this).data('user');
        const sidebarAvatar = $('#wpap-user-avatar img');
        const avatar = $('#wpap-pro-pic img');
        sidebarAvatar.css('opacity', '0.6');
        avatar.css('opacity', '0.6');

        $.ajax({
            type: 'post',
            url: WPAPAjax.ajaxUrl,
            data: {
                action: 'wpap_delete_avatar',
                user_id: userID
            },
            success: function (response) {
                sidebarAvatar.css('opacity', '1');
                avatar.css('opacity', '1');

                if (response.status === 'success') {
                    sidebarAvatar.attr('src', response.url);
                    avatar.attr('src', response.url);
                    btn.remove();
                } else {
                    wpapFloatMessage(response.msg, response.status);
                }
            },
            error: function (error) {
            }
        })
    });
});
