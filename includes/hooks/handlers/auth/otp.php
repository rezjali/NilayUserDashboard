<?php
defined('ABSPATH') || exit;

use Arvand\ArvandPanel\DB\WPAPSMSDB;
use Arvand\ArvandPanel\Form\WPAPFieldSettings;
use Arvand\ArvandPanel\SMS\WPAPSendCode;
use Arvand\ArvandPanel\WPAPUser;

add_action('wp_ajax_nopriv_sms_register_login_send_code', function () {
    if (empty($_POST['sms_register_login_send_nonce']) || !wp_verify_nonce($_POST['sms_register_login_send_nonce'], 'sms_register_login_send_nonce')) {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid request.', 'arvand-panel')]);
    }

    if (!$phone = wpap_phone_format($_POST['phone'])) {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid mobile number.', 'arvand-panel')]);
    }

    $sms = new WPAPSMSDB();
    $rand = rand(10000, 99999);

    if ($user = WPAPUser::checkPhoneFields($phone)) {
        $register = wpap_register_options();
        $status = get_user_meta($user->ID, 'wpap_user_status', true);
        $admin = user_can($user->ID, 'administrator');

        if ($register['enable_admin_approval'] && !$status && !$admin) {
            wp_send_json(['status' => 'error', 'msg' => __('Your account has not yet been activated by admin.', 'arvand-panel')]);
        }

        if ($sms->insertCode($rand, $phone)) {
            WPAPSendCode::send($phone, $rand);
            wp_send_json(['status' => 'success', 'section' => 'login', 'msg' => __('Enter the code that was sent to your mobile number.', 'arvand-panel')]);
        }

        wp_send_json(['status' => 'error', 'msg' => __('There is error in sending verification code.', 'arvand-panel')]);
    }

    if ($sms->insertCode($rand, $phone)) {
        WPAPSendCode::send($phone, $rand);
        wp_send_json(['status' => 'success', 'section' => 'register', 'msg' => __('Verification code has been sent to your mobile number.', 'arvand-panel')]);
    }

    wp_send_json(['status' => 'error', 'msg' => __('There is error in sending verification code.', 'arvand-panel')]);
});

add_action('wp_ajax_nopriv_sms_register_verify', function () {
    if (empty($_POST['sms_register_verify_nonce']) or !wp_verify_nonce($_POST['sms_register_verify_nonce'], 'sms_register_verify_nonce')) {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid request.', 'arvand-panel')]);
    }

    $code = sanitize_text_field(wpap_en_num($_POST['verification_code']));

    if (!$phone = wpap_phone_format($_POST['user_phone'])) {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid phone number.', 'arvand-panel')]);
    }

    if (!WPAPSMSDB::isValidCode($code, $phone)) {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid verification code.', 'arvand-panel')]);
    }

    $register = wpap_register_options();

    if ($register['sms_reg_password']) {
        $pass_opt = WPAPFieldSettings::password();

        if (strlen($_POST['user_pass']) < $pass_opt['rules']['min_length']) {
            wp_send_json([
                'status' => 'error',
                'msg' => sprintf(__('Minimum password letters must be %d', 'arvand-panel'), esc_html($pass_opt['rules']['min_length']))
            ]);
        }
    }

    $user_id = wp_insert_user([
        'user_login' => $phone,
        'user_pass' => !empty($_POST['user_pass']) ? wpap_en_num($_POST['user_pass']) : wp_generate_password()
    ]);

    if ($user_id) {
        add_user_meta($user_id, 'wpap_user_phone_number', $phone);
        add_user_meta($user_id, 'wpap_user_status', 0);
        wp_clear_auth_cookie();
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);
        wp_send_json(['status' => 'success', 'msg' => __('You have successfully registered. Signing in ...', 'arvand-panel')]);
    }

    wp_send_json(['status' => 'error', 'msg' => __('There is error in verify mobile number.', 'arvand-panel')]);
});

add_action('wp_ajax_nopriv_sms_login_verify', function () {
    if (empty($_POST['sms_login_verify_nonce']) or !wp_verify_nonce($_POST['sms_login_verify_nonce'], 'sms_login_verify_nonce')) {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid request.', 'arvand-panel')]);
    }

    $code = wpap_en_num($_POST['verification_code']);
    $phone = wpap_phone_format($_POST['user_phone']);

    if (!WPAPSMSDB::isValidCode($code, $phone)) {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid verification code.', 'arvand-panel')]);
    }

    $user = WPAPUser::checkPhoneFields($phone);

    if ($user) {
        if (!get_user_meta($user->ID, 'wpap_user_phone_number', true)) {
            update_user_meta($user->ID, 'wpap_user_phone_number', $phone);
        }

        wp_clear_auth_cookie();
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID);
        wp_send_json(['status' => 'success', 'msg' => __('You have successfully logged in.', 'arvand-panel')]);
    }

    wp_send_json(['status' => 'error', 'msg' => __('There is error in verify mobile number.', 'arvand-panel')]);
});

add_action('wp_ajax_nopriv_force_add_mobile', function () {
    if (empty($_POST['force_add_mobile_nonce']) or !wp_verify_nonce($_POST['force_add_mobile_nonce'], 'force_add_mobile_nonce')) {
        wp_send_json(['type' => 'error', 'msg' => __('Invalid request.', 'arvand-panel')]);
    }

    $phone = wpap_phone_format($_POST['phone']);

    if (!$phone) {
        wp_send_json(['type' => 'error', 'msg' => __('Invalid mobile number.', 'arvand-panel')]);
    }

    if (WPAPUser::checkPhoneFields($phone)) {
        wp_send_json(['type' => 'error', 'msg' => __('This phone number does not belong to your account.', 'arvand-panel')]);
    }

    if (!is_numeric($_POST['mobile_num_owner']) or !absint($_POST['mobile_num_owner'])) {
        wp_send_json(['type' => 'error', 'msg' => __('Invalid request.', 'arvand-panel')]);
    }

    if (!$user = get_user_by('id', $_POST['mobile_num_owner'])) {
        wp_send_json(['type' => 'error', 'msg' => __('User not found.', 'arvand-panel')]);
    }

    $register = wpap_register_options();
    $status = get_user_meta($_POST['mobile_num_owner'], 'wpap_user_status', true);
    $admin = user_can($_POST['mobile_num_owner'], 'administrator');

    if ($register['enable_admin_approval'] and $user and !$admin and !$status) {
        wp_send_json(['type' => 'error', 'msg' => __('Your number verified but your account has not yet been activated by admin.', 'arvand-panel')]);
    }

    $sms = new WPAPSMSDB();
    $code = rand(10000, 99999);

    if ($sms->insertCode($code, $phone)) {
        WPAPSendCode::send($phone, $code);
        wp_send_json(['type' => 'success', 'msg' => __('Verification code has been sent to your mobile number.', 'arvand-panel')]);
    }
});

add_action('wp_ajax_nopriv_force_verify', function () {
    if (empty($_POST['force_verification_nonce']) or !wp_verify_nonce($_POST['force_verification_nonce'], 'force_verification_nonce')) {
        wp_send_json(['type' => 'error', 'msg' => __('Invalid request.', 'arvand-panel')]);
    }

    $code = wpap_en_num($_POST['verification_code']);
    $phone = wpap_phone_format($_POST['user_phone']);

    if (!WPAPSMSDB::isValidCode($code, $phone)) {
        wp_send_json(['type' => 'error', 'msg' => __('Invalid verification code.', 'arvand-panel')]);
    }

    if (!is_numeric($_POST['mobile_num_owner']) or !absint($_POST['mobile_num_owner'])) {
        wp_send_json(['type' => 'error', 'msg' => __('Invalid request.', 'arvand-panel')]);
    }

    if (!$user = get_user_by('id', $_POST['mobile_num_owner'])) {
        wp_send_json(['type' => 'error', 'msg' => __('User not found.', 'arvand-panel')]);
    }

    update_user_meta($user->ID, 'wpap_user_phone_number', $phone);
    wp_clear_auth_cookie();
    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID);
    wp_send_json(['type' => 'success', 'msg' => __('Signing in ...', 'arvand-panel')]);
});

add_action('wp_ajax_nopriv_wpap_sms_lost_pass', function () {
    if (empty($_POST['sms_reset_pass_nonce']) or !wp_verify_nonce($_POST['sms_reset_pass_nonce'], 'sms_reset_pass_nonce')) {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid request.', 'arvand-panel')]);
    }

    $phone = wpap_phone_format($_POST['phone']);

    if (!$phone) {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid mobile number.', 'arvand-panel')]);
    }

    $user = WPAPUser::checkPhoneFields($phone);

    if (!$user) {
        wp_send_json(['status' => 'error', 'msg' => __('The mobile number or user does not exist.', 'arvand-panel')]);
    }

    $rand = rand(10000, 99999);
    WPAPSendCode::send($phone, $rand);
    $sms = new WPAPSMSDB();

    if ($sms->insertCode($rand, $phone)) {
        wp_send_json(['status' => 'success', 'msg' => __('Verification code sent to your mobile number.', 'arvand-panel')]);
    }

    wp_send_json(['status' => 'error', 'msg' => __('There is error in sending code.', 'arvand-panel')]);
});

add_action('wp_ajax_nopriv_sms_pass_reset_verify', function () {
    if (empty($_POST['sms_reset_pass_verify_nonce']) || !wp_verify_nonce($_POST['sms_reset_pass_verify_nonce'], 'sms_reset_pass_verify_nonce')) {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid request.', 'arvand-panel')]);
    }

    $phone = wpap_phone_format($_POST['user_phone']);
    if (!$phone) {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid request.', 'arvand-panel')]);
    }

    $pass_opt = WPAPFieldSettings::password();

    if (strlen($_POST['new_password']) < $pass_opt['rules']['min_length']) {
        wp_send_json([
            'status' => 'error',
            'msg' => sprintf(
                __('Minimum password letters must be %d', 'arvand-panel'),
                esc_html($pass_opt['rules']['min_length'])
            )
        ]);
    }

    $code = wpap_en_num($_POST['verification_code']);
    $sms = new WPAPSMSDB();

    if (!$sms->isValidCode($code, $phone)) {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid verification code.', 'arvand-panel')]);
    }

    $user = WPAPUser::checkPhoneFields($phone);

    if (!$user) {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid verification code.', 'arvand-panel')]);
    }

    $update_pass = wp_update_user(['ID' => $user->ID, 'user_pass' => wpap_en_num($_POST['new_password'])]);

    if ($update_pass) {
        wp_send_json(['status' => 'success', 'msg' => __('Your password successfully changed. Go to login.', 'arvand-panel')]);
    }

    wp_send_json(['status' => 'error', 'msg' => __('There is error in verify mobile number.', 'arvand-panel')]);
});

add_action('wp_ajax_wpap_add_phone', function () {
    if (WPAP_DEMO === wp_get_current_user()->user_login) {
        wp_send_json(['status' => 'error', 'msg' => __('کاربر دمو قادر به ثبت شماره همراه نیست.', 'arvand-panel')]);
    }

    if (empty($_POST['phone'])) {
        wp_send_json(['status' => 'error', 'msg' => __('Mobile number must not be empty.', 'arvand-panel')]);
    }

    $phone = wpap_phone_format($_POST['phone']);

    if (!$phone) {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid mobile number.', 'arvand-panel'), 'form' => 'send']);
    }

    $sms = new WPAPSMSDB();
    $rand = rand(10000, 99999);

    if (WPAPUser::checkPhoneFields($phone)) {
        wp_send_json(['status' => 'error', 'msg' => __('This mobile number already exists.', 'arvand-panel')]);
    }

    $sms->insertCode($rand, $phone);
    WPAPSendCode::send($phone, $rand);
    wp_send_json(['status' => 'success', 'form' => 'verify', 'msg' => __('Verification code has been sent to your mobile number.', 'arvand-panel')]);
});

add_action('wp_ajax_wpap_add_phone_verify', function () {
    $current_user = wp_get_current_user();

    if (WPAP_DEMO === $current_user->user_login) {
        wp_send_json(['status' => 'error', 'form' => 'send', 'msg' => __('کاربر دمو قادر به ثبت شماره همراه نیست.', 'arvand-panel')]);
    }

    $phone = wpap_phone_format($_POST['phone']);

    if (!$phone) {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid mobile number.', 'arvand-panel')]);
    }

    if (empty($_POST['code'])) {
        wp_send_json(['status' => 'error', 'msg' => __('Verification code must not be empty.', 'arvand-panel')]);
    }

    $code = wpap_en_num($_POST['code']);

    if (!WPAPSMSDB::isValidCode($code, $phone)) {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid code.', 'arvand-panel')]);
    }

    if (wpap_phone_format($current_user->display_name)) {
        wp_update_user(['ID' => $current_user->ID, 'display_name' => $phone]);
    }

    update_user_meta($current_user->ID, 'wpap_user_phone_number', $phone);
    wp_send_json(['status' => 'success', 'msg' => __('Your mobile number successfully verified.', 'arvand-panel')]);
});