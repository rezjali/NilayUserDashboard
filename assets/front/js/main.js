jQuery(document).ready(function ($) {
    $(document).on('click', '.wpap-dismiss-msg', function (e) {
        e.preventDefault();
        $(this).parent().fadeOut(400);
        setTimeout(() => $(this).parent().remove(), 400);
    });

    $(document).on('click','#wpap-sms-register-login-btn', function (e) {
        e.preventDefault();
        $('.wpap-msg').hide(0);
        $('#wpap-register form, #wpap-login form').hide(0);
        $('#wpap-sms-send-form').show(0);
    });

    $(document).on('click','#wpap-change-phone-btn', function (e) {
        e.preventDefault();
        $('.wpap-msg').hide(0);
        $('#wpap-sms-register-login form').hide(0);
        $('#wpap-sms-send-form').show(0);
    });

    $(document).on('click', '#wpap-def-register-btn', function (e) {
        e.preventDefault();
        $('.wpap-msg').hide(0);
        $('#wpap-register form').hide(0);
        $('#wpap-register-form').show(0);
    });

    $(document).on('click', '#wpap-user-login-btn', function (e) {
        e.preventDefault();
        $('.wpap-msg').hide(0);
        $('#wpap-login form').hide(0);
        $('#wpap-login-form').show(0);
    });

    $(document).on('click','#wpap-sms-pass-lost-btn', function (e) {
        e.preventDefault();
        $('#wpap-lost-password-form').hide(0);
        $('#wpap-sms-lost-pass-form').show(0);
        $(this).hide(0);
    });

    $(document).on('click', '.wpap-profile-menu-btn', function (e) {
        $('#profile-menu-wrap').fadeToggle(100);
        e.stopPropagation();
    })

    $(document).on('click', '#profile-menu-wrap', function (e) {
        e.stopPropagation();
    })

    $(document).on('click', function (e) {
        $('#profile-menu-wrap').fadeOut(200);
    });

    $(document).on('click', '#wpap-header-show-sidebar', function (e) {
        e.preventDefault();
        $('#wpap-sidebar').addClass('wpap-show-sidebar');
    });

    $(document).on('click', '#wpap-hide-sidebar', function (e) {
        e.preventDefault();
        $('#wpap-sidebar').removeClass('wpap-show-sidebar');
    });

    $(document).on('click', '#wpap-nav .wpap-menu .wpap-has-children', function (e) {
        e.preventDefault();

        var toggleButton = $(this);
        var submenu = $(this).siblings(".wpap-submenu");

        $(".wpap-submenu").not(submenu).slideUp(150);
        $(".wpap-has-children").removeClass('wpap-submenu-opened wpap-open');

        submenu.slideToggle(150, function () {
            if (submenu.is(":visible")) {
                toggleButton.addClass('wpap-submenu-opened wpap-open')
            } else {
                toggleButton.removeClass('wpap-submenu-opened wpap-open')
            }
        });
    });

    $('#wpap-header-show-notice-wrap').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $('#wpap-header-notice-wrap').fadeIn(300)
    });

    $('#wpap-header-notice-wrap > div').on('click', function (e) {
        e.stopPropagation();
    });

    $('#wpap-header-hide-notice-wrap').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $('#wpap-header-notice-wrap').fadeOut(300);
    });

    $(document).on('click', function (e) {
        $('#wpap-header-notice-wrap').fadeOut(300);
    });

    $('#wpap-upload-pro-pic').on('change', function () {
        let input = $(this);

        if (input.prop('files') && input.prop('files')[0]) {
            let reader = new FileReader();

            reader.onload = function (e) {
                $('#wpap-user-avatar img').attr('src', e.target.result).attr('srcset', e.target.result);
                $('#wpap-pro-pic img').attr('src', e.target.result).attr('srcset', e.target.result);
            }

            reader.readAsDataURL(input.prop('files')[0]);
        }
    });

    $('.wpap-upload-attachment input').on('input', function () {
        var parent = $(this).parents('.wpap-upload-attachment');
        var fileName = $(this).prop('files')[0].name;
        parent.find('header').html('<span>' + fileName + '</span>');
    })

    $(document).on('click', '.wpap-show-downloadable-product-btn',function () {
        $('.wpap-show-downloadable-product-btn').not($(this)).removeClass('wpap-show-downloadable-product-btn-open');
        $('.wpap-downloads-table-wrap').not($(this).next()).slideUp(100);
        $(this).toggleClass('wpap-show-downloadable-product-btn-open');
        $(this).next().slideToggle(100);
    });
});