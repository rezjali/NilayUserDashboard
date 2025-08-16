<?php
defined('ABSPATH') || exit;

use Arvand\ArvandPanel\Mail\WPAPMail;
use Arvand\ArvandPanel\Form\WPAPFieldSettings;
use Arvand\ArvandPanel\WPAPUser;

add_filter('wp_ajax_nopriv_wpap_login', function () {
    if (!isset($_POST['wpap_login_nonce']) || !wp_verify_nonce(wp_unslash($_POST['wpap_login_nonce']), 'wpap_login')) {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid Request.', 'arvand-panel')]);
    }

    if (empty($_POST['user_login'])) {
        wp_send_json(['status' => 'error', 'msg' => __('Username must not be empty.', 'arvand-panel')]);
    }

    if (empty($_POST['user_pass'])) {
        wp_send_json(['status' => 'error', 'msg' => __('Password must not be empty.', 'arvand-panel')]);
    }

    $login = wpap_login_options();
    $google_opt = wpap_google_options();

    if ($google_opt['enable_recaptcha'] && !empty($google_opt['recaptcha_secret_key'])) {
        if (!wpap_recaptcha_validate($google_opt['recaptcha_secret_key'])) {
            wp_send_json(['status' => 'error', 'msg' => __('Recaptcha verification failed, please try again.', 'arvand-panel')]);
        }
    }

    $user_name = sanitize_user($_POST['user_login']);
    $user = get_user_by('login', $user_name);

    if ($user) {
        $register = wpap_register_options();

        if ($register['enable_admin_approval']) {
            $status = get_user_meta($user->ID, 'wpap_user_status', true);
            $admin = user_can($user->ID, 'administrator');

            if (!$status && !$admin) {
                wp_send_json(['status' => 'error', 'msg' => __('Your account has not yet been approved by admin.', 'arvand-panel')]);
            }
        }

        if ($register['register_activation'] && !user_can($user->ID, 'administrator')) {
            $status = get_user_meta($user->ID, 'wpap_user_status', true);

            if (empty($user->user_email) || !$status) {
                wp_send_json(['status' => 'error', 'form' => 'send-activation-link', 'msg' => __('Your account is not active.', 'arvand-panel'), 'user' => $user->ID]);
            }
        }

        if ($login['enable_sms_register_login'] && $login['force_to_add_mobile']) {
            if (!WPAPUser::userHasPhone($user->ID)) {
                wp_send_json(['status' => 'error', 'form' => 'force-add-mobile', 'msg' => __('Please enter and verify your phone number.', 'arvand-panel'), 'user' => $user->ID]);
            }
        }
    }

    $phone = wpap_phone_format($_POST['user_login']);

    if ($phone && $user = WPAPUser::checkPhoneFields($phone)) {
        $user_name = $user->user_login;
    }

    $user = wp_signon([
        'user_login' => wpap_en_num($user_name),
        'user_password' => wpap_en_num($_POST['user_pass']),
        'remember' => isset($_POST['remember'])
    ]);

    if (is_wp_error($user)) {
        wp_send_json(['status' => 'error', 'msg' => __('Please enter login information correctly.', 'arvand-panel')]);
    }

    $redirect_url = '';

    if (user_can($user->ID, 'manage_options')) {
        $redirect_url = admin_url();
    } else {
        $pages_opt = wpap_pages_options();
        $after_login_page_url = get_permalink($pages_opt['after_login_page_id']);

        if ($after_login_page_url) {
            $redirect_url = esc_url($after_login_page_url);
        }
    }

    wp_send_json(['status' => 'success', 'msg' => __('Signing in ...', 'arvand-panel'), 'after_login' => $redirect_url]);
});

add_action('wp_ajax_nopriv_wpap_lost_password', function () {
    if ($_POST['user_login'] === WPAP_DEMO) {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid Request.', 'arvand-panel')]);
    }

    if (empty($_POST['lost_pass_nonce']) or !wp_verify_nonce($_POST['lost_pass_nonce'], 'lost_pass_nonce')) {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid Request.', 'arvand-panel')]);
    }

    $errors = retrieve_password(wpap_en_num($_POST['user_login']));

    if (is_wp_error($errors)) {
        wp_send_json(['status' => 'error', 'msg' => $errors->get_error_message()]);
    }

    wp_send_json(['status' => 'success', 'msg' => __('A password recovery link has been sent to your email. Please check your email.', 'arvand-panel')]);
});

add_action('wp_ajax_nopriv_wpap_reset_password', function () {
    if (!isset($_POST['reset_pass_nonce']) or !wp_verify_nonce($_POST['reset_pass_nonce'], 'reset_pass_nonce')) {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid Request.', 'arvand-panel')]);
    }

    if (empty($_POST['pass1'])) {
        wp_send_json(['status' => 'error', 'msg' => __('Password must not be empty.', 'arvand-panel')]);
    }

    if ($_POST['pass1'] !== $_POST['pass2']) {
        wp_send_json(['status' => 'error', 'msg' => __('Two passwords do not match.', 'arvand-panel')]);
    }

    $pass_settings = WPAPFieldSettings::password();

    if (strlen($_POST['pass1']) < $pass_settings['min']) {
        wp_send_json(['status' => 'error', 'msg' => sprintf(__('Minimum password letters must be %d', 'arvand-panel'), esc_html($pass_settings['min']))]);
    }

    $rp_key = sanitize_text_field($_REQUEST['rp_key']);
    $rp_login = sanitize_text_field($_REQUEST['rp_login']);

    $user = check_password_reset_key($rp_key, $rp_login);
    if (is_wp_error($user)) {
        wp_send_json(['status' => 'error', 'msg' => $user->get_error_message()]);
    }

    reset_password($user, wpap_en_num($_POST['pass1']));
    $pages_opt = wpap_pages_options();
    $login_page_url = get_permalink($pages_opt['login_page_id']);
    $login_url = $login_page_url ? add_query_arg('password', 'changed', esc_url($login_page_url)) : '';
    wp_send_json(['status' => 'success', 'msg' => __('Password changed successfully.', 'arvand-panel'), 'login_url' => $login_url]);
});

add_action('wp_ajax_nopriv_send_activation_link', function () {
    if (!isset($_POST['activation_email_nonce']) or !wp_verify_nonce($_POST['activation_email_nonce'], 'activation_email_nonce')) {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid Request.', 'arvand-panel')]);
    }

    if (empty($_POST['email']) or !is_email($_POST['email'])) {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid email.', 'arvand-panel')]);
    }

    if (empty($_POST['user_id']) or !is_numeric($_POST['user_id'])) {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid Request.', 'arvand-panel')]);
    }

    $user = get_user_by('id', absint($_POST['user_id']));
    if (!$user) {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid Request.', 'arvand-panel')]);
    }

    if (email_exists($_POST['email']) and $_POST['email'] !== $user->user_email) {
        wp_send_json(['status' => 'error', 'msg' => __('Email already exist.', 'arvand-panel')]);
    }

    $update = wp_update_user(['ID' => $user->ID, 'user_email' => sanitize_email($_POST['email'])]);
    if (is_wp_error($update)) {
        wp_send_json(['status' => 'success', 'msg' => $update->get_error_message()]);
    }

    $email_opt = wpap_email_options();

    WPAPMail::registerMail(
        $user->user_email,
        $user,
        $email_opt['activation_email_subject'],
        $email_opt['activation_email'],
        true
    );

    wp_send_json(['status' => 'success', 'msg' => __('An email verification link sent to your email.', 'arvand-panel')]);
});







