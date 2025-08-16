<?php
defined('ABSPATH') || exit;

use Arvand\ArvandPanel\Form\WPAPFieldSettings;

// Styles
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'wpap_front_style',
        WPAP_ASSETS_URL . 'front/css/main.css',
        [],
        current_time('timestamp')
    );

    wp_enqueue_style(
        'wpap_remix_icons',
        WPAP_ASSETS_URL . 'icons/remixicon.css',
        [],
        current_time('timestamp')
    );

    // ------ TEMP ------ //
    wp_enqueue_style(
        'wpap_icons',
        WPAP_ASSETS_URL . 'icons/bootstrap-icons.min.css',
        [],
        current_time('timestamp')
    );
}, PHP_INT_MAX);

// Scripts
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script(
        'wpap_main',
        WPAP_ASSETS_URL . 'front/js/main.js',
        ['jquery'],
        current_time('timestamp'),
        true
    );

    wp_enqueue_script(
        'wpap_ajax',
        WPAP_ASSETS_URL . 'front/js/ajax.js',
        ['jquery'],
        current_time('timestamp'),
        true
    );

    wp_register_script(
        'wpap_panel_form_handler',
        WPAP_ASSETS_URL . 'front/js/panel-form-handler.js',
        ['jquery'],
        current_time('timestamp'),
        true
    );

    wp_register_script(
        'wpap_password_strength',
        WPAP_ASSETS_URL . 'front/js/password-strength.js',
        ['password-strength-meter'],
        current_time('timestamp'),
        true
    );
}, PHP_INT_MAX);

// Scripts data
add_action('wp_enqueue_scripts', function () {
    wp_localize_script(
        'wpap_main',
        'WPAPMain',
        [
            'ajaxURL' => admin_url('admin-ajax.php'),
            'uploadAvatarBtnText' => __('Change Avatar', 'arvand-panel')
        ]
    );

    $pass_opt = WPAPFieldSettings::password();

    wp_localize_script(
        'wpap_ajax',
        'WPAPAjax',
        [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'panelUrl' => wpap_panel_url(),
            'themeChangeLoadingMsg' => __('در حال تغییر حالت...', 'arvand-panel'),
            'themeChangeSuccessMsg' => __('حالت پنل تغییر کرد.', 'arvand-panel'),
            'invalidMobileText' => __('Invalid mobile number.', 'arvand-panel'),
            'emptyMobileText' => __('Mobile number must not be empty', 'arvand-panel'),
            'emptyVerifyCodeText' => __('Verification code must not be empty', 'arvand-panel'),
            'agreeErrorText' => __('You must agree to the terms.', 'arvand-panel'),
            'sendAgainText' => __('Send Again', 'arvand-panel'),
            'emptyPass' => __('Password must not be empty.', 'arvand-panel'),
            'passMin' => $pass_opt['rules']['min_length'],
            'passMinText' => sprintf(__('Minimum password letters must be %d', 'arvand-panel'), $pass_opt['rules']['min_length']),
            'passMismatch' => __('Two passwords do not match.', 'arvand-panel'),
        ]
    );

    wp_localize_script(
        'wpap_panel_form_handler',
        'WPAPPanelFormHandler', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'closeMessageBtnText' => __('Close', 'arvand-panel')
        ]
    );
}, PHP_INT_MAX);

// ------ TEMP ------ //
add_action('wp_head', function () {
    echo '<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>';
});

add_filter('script_loader_tag', function ($tag, $handle, $src) {
    if ('wpap_ajax' === $handle) {
        $tag = '<script type="module" src="' . esc_url($src) . '"></script>';
    }

    if ('wpap_panel_form_handler' === $handle) {
        $tag = '<script type="module" src="' . esc_url($src) . '"></script>';
    }

    return $tag;
}, 10, 3);