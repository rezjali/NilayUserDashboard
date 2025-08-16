<?php
defined('ABSPATH') || exit;

add_action('wp_ajax_wpap_melipayamak', function () {
    if (!isset($_POST['sms_nonce']) or !wp_verify_nonce($_POST['sms_nonce'], 'sms_nonce')) {
        wp_send_json('error');
    }

    $data = [
        'username' => sanitize_text_field($_POST['username']),
        'password' => sanitize_text_field($_POST['password']),
        'from' => sanitize_text_field($_POST['from']),
        'pattern_code' => sanitize_text_field($_POST['pattern_code']),
    ];

    update_option('wpap_sms_melipayamak', $data);
    wp_send_json(['status' => 'success', 'provider' => wpap_gateway_settings_save()]);
});

add_action('wp_ajax_wpap_farapayamak', function () {
    if (!isset($_POST['sms_nonce']) or !wp_verify_nonce($_POST['sms_nonce'], 'sms_nonce')) {
        wp_send_json('error');
    }

    $data = [
        'username' => sanitize_text_field($_POST['username']),
        'password' => sanitize_text_field($_POST['password']),
        'from' => sanitize_text_field($_POST['from']),
        'pattern_code' => sanitize_text_field($_POST['pattern_code']),
    ];

    update_option('wpap_sms_farapayamak', $data);
    wp_send_json(['status' => 'success', 'provider' => wpap_gateway_settings_save()]);
});

add_action('wp_ajax_wpap_sms_ir', function () {
    if (!isset($_POST['sms_nonce']) or !wp_verify_nonce($_POST['sms_nonce'], 'sms_nonce')) {
        wp_send_json('error');
    }

    $data = [
        'api_key' => sanitize_text_field($_POST['api_key']),
        'secret_key' => sanitize_text_field($_POST['secret_key']),
        'from' => sanitize_text_field($_POST['from']),
        'template_id' => sanitize_text_field($_POST['template_id']),
    ];

    update_option('wpap_sms_sms_ir', $data);
    wp_send_json(['status' => 'success', 'provider' => wpap_gateway_settings_save()]);
});

add_action('wp_ajax_wpap_farazsms', function () {
    if (!isset($_POST['sms_nonce']) or !wp_verify_nonce($_POST['sms_nonce'], 'sms_nonce')) {
        wp_send_json('error');
    }

    $data = [
        'username' => sanitize_text_field($_POST['username']),
        'password' => sanitize_text_field($_POST['password']),
        'from' => sanitize_text_field($_POST['from']),
        'pattern_code' => sanitize_text_field($_POST['pattern_code']),
    ];

    update_option('wpap_sms_farazsms', $data);
    wp_send_json(['status' => 'success', 'provider' => wpap_gateway_settings_save()]);
});

add_action('wp_ajax_wpap_kavenegar', function () {
    if (!isset($_POST['sms_nonce']) or !wp_verify_nonce($_POST['sms_nonce'], 'sms_nonce')) {
        wp_send_json('error');
    }

    $data = [
        'api_key' => sanitize_text_field($_POST['api_key']),
        'from' => sanitize_text_field($_POST['from']),
        'pattern_code' => sanitize_text_field($_POST['pattern_code']),
    ];

    update_option('wpap_sms_kavenegar', $data);
    wp_send_json(['status' => 'success', 'provider' => wpap_gateway_settings_save()]);
});

add_action('wp_ajax_wpap_modirpayamak', function () {
    if (!isset($_POST['sms_nonce']) or !wp_verify_nonce($_POST['sms_nonce'], 'sms_nonce')) {
        wp_send_json('error');
    }

    $data = [
        'username' => sanitize_text_field($_POST['username']),
        'password' => sanitize_text_field($_POST['password']),
        'from' => sanitize_text_field($_POST['from']),
        'pattern_code' => sanitize_text_field($_POST['pattern_code']),
    ];

    update_option('wpap_sms_modirpayamak', $data);
    wp_send_json(['status' => 'success', 'provider' => wpap_gateway_settings_save()]);
});

add_action('wp_ajax_wpap_parsgreen', function () {
    if (!isset($_POST['sms_nonce']) or !wp_verify_nonce($_POST['sms_nonce'], 'sms_nonce')) {
        wp_send_json('error');
    }

    $data = ['api_key' => sanitize_text_field($_POST['api_key'])];
    update_option('wpap_sms_parsgreen', $data);
    wp_send_json(['status' => 'success', 'provider' => $this->saveSMSProvider()]);
});

add_action('wp_ajax_wpap_raygansms', function () {
    if (!isset($_POST['sms_nonce']) or !wp_verify_nonce($_POST['sms_nonce'], 'sms_nonce')) {
        wp_send_json('error');
    }

    $data = [
        'username' => sanitize_text_field($_POST['username']),
        'password' => sanitize_text_field($_POST['password']),
        'from' => sanitize_text_field($_POST['from']),
        'text' => sanitize_textarea_field($_POST['text']),
    ];

    update_option('wpap_sms_raygansms', $data);
    wp_send_json(['status' => 'success', 'provider' => wpap_gateway_settings_save()]);
});

add_action('wp_ajax_wpap_webone_sms', function () {
    if (!isset($_POST['sms_nonce']) or !wp_verify_nonce($_POST['sms_nonce'], 'sms_nonce')) {
        wp_send_json('error');
    }

    $data = [
        'username' => sanitize_text_field($_POST['username']),
        'password' => sanitize_text_field($_POST['password']),
        'from' => sanitize_text_field($_POST['from']),
        'text' => sanitize_textarea_field($_POST['text']),
    ];

    update_option('wpap_sms_webone_sms', $data);
    wp_send_json(['status' => 'success', 'provider' => wpap_gateway_settings_save()]);
});