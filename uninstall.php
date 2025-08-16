<?php
defined('WP_UNINSTALL_PLUGIN') || exit;

if (get_option('wpap_general')['delete_plugin_data']) {
    $post_ids = get_posts([
        'post_type' => [
            'wpap_notifications',
            'wpap_ticket',
            'wpap_register_field',
            'wpap_private_message'
        ],
        'fields' => 'ids',
        'numberposts' => -1
    ]);

    foreach ($post_ids as $post_id) {
        wp_delete_post($post_id, true);
    }

    delete_option('wpap_plugin_is_installed');

    // Options
    delete_option('wpap_general');
    delete_option('wpap_register');
    delete_option('wpap_login');
    delete_option('wpap_account_menu');
    delete_option('wpap_panel');
    delete_option('wpap_dash');
    delete_option('wpap_dash_box');
    delete_option('wpap_ticket');
    delete_option('wpap_ticket_department');
    delete_option('wpap_roles');

    // SMS Options
    delete_option('wpap_sms');
    delete_option('wpap_sms_melipayamak');
    delete_option('wpap_sms_farapayamak');
    delete_option('wpap_sms_sms_ir');
    delete_option('wpap_sms_farazsms');
    delete_option('wpap_sms_kavenegar');
    delete_option('wpap_sms_parsgreen');
    delete_option('wpap_sms_modirpayamak');
    delete_option('wpap_sms_raygansms');
    delete_option('wpap_sms_webone_sms');
    delete_option('wpap_plugin_pages');
    delete_option('wpap_shortcode');
    delete_option('wpap_email');
    delete_option('wpap_styles');

    // Register fields
    delete_option('wpap_register_fields');
    delete_option('wpap_register_fields_last_id');

    delete_option('wpap_menus_cache');
}