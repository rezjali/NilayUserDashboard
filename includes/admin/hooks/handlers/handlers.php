<?php
defined('ABSPATH') || exit;

use Arvand\ArvandPanel\DB\WPAPMenuDB;
use Arvand\ArvandPanel\Form\Fields\wpap_field_file;
use Arvand\ArvandPanel\Form\WPAPFieldSettings;
use Arvand\ArvandPanel\WPAPFile;
use Arvand\ArvandPanel\WPAPUser;

add_action('wp_ajax_wpap_add_register_field', function () {
    if (empty($_POST['field_name'])) {
        wp_send_json_error();
    }

    $field_class = 'Arvand\ArvandPanel\Form\Fields\wpap_field_' . $_POST['field_name'];

    if (!class_exists($field_class)) {
        wp_send_json_error();
    }

    $field_class = new $field_class;

    $id = (get_option('wpap_register_fields_last_id') ?: 0) + 1;
    update_option('wpap_register_fields_last_id', $id);
    wp_send_json_success($field_class->settingsOutput(null, $id));
});

add_action('wp_ajax_wpap_reg_fields', function () {
    if (empty($_POST['fields'])) {
        wp_send_json_error();
    }

    $fields = [];
    $prev_options = WPAPFieldSettings::get();
    $new_meta_keys = [];
    $meta_keys_to_update = [];

    foreach ($_POST['fields'] as $field) {
        if (empty($field['field_name']) || empty($field['id'])) {
            continue;
        }

        $field_class = 'Arvand\ArvandPanel\Form\Fields\wpap_field_' . $field['field_name'];

        if (!class_exists($field_class)) {
            continue;
        }

        $field_class = new $field_class;
        $field_validation = call_user_func([new $field_class, 'settingsValidation'], $field);

        if (!$field_validation[0]) {
            continue;
        }

        $settings = $field_validation[1];
        $fields[$settings['attrs']['name']] = $settings;

        if ($field_class->repeatable) {
            if ($prev_options && isset($prev_options[$settings['attrs']['name']])) {
                $prev_key = $prev_options[$settings['attrs']['name']]['meta_key'];

                if ($prev_key !== $settings['meta_key']) {
                    $meta_keys_to_update[] = ['prev_key' => $prev_key, 'new_key' => $settings['meta_key']];
                }
            }

            $new_meta_keys[] = $settings['meta_key'];
        }
    }

    $update = update_option('wpap_register_fields', json_encode($fields));

    if ($update) {
        global $wpdb;

        // update meta keys
        if (count($meta_keys_to_update) > 0) {
            foreach ($meta_keys_to_update as $key) {
                $wpdb->update($wpdb->usermeta,
                    ['meta_key' => sanitize_text_field($key['new_key'])],
                    ['meta_key' => sanitize_text_field($key['prev_key'])],
                    '%s',
                    '%s'
                );
            }
        }

        // delete old meta keys
        if ($prev_options) {
            foreach ($prev_options as $prev_option) {
                if (!isset($prev_option['meta_key'])) {
                    continue;
                }

                if ('file' === $prev_option['attrs']['type']) {
                    wpap_field_file::removeFiles($prev_option);
                }

                if (!in_array($prev_option['meta_key'], $new_meta_keys)) {
                    $wpdb->delete($wpdb->usermeta, ['meta_key' => sanitize_text_field($prev_option['meta_key'])], ['%s']);
                }
            }
        }
    }

    wp_send_json_success();
});

add_action('wp_ajax_wpap_user_select', function () {
    if (empty($_POST['user'])) {
        wp_send_json_error();
    }

    $current_user_id = get_current_user_id();

    $users = get_users([
        'search' => '*' . sanitize_text_field($_POST['user']) . '*',
        'search_columns' => ['ID', 'user_login', 'user_email'],
        'exclude' => $current_user_id,
        'number' => 10
    ]);

    $html = '';

    if ($phone = wpap_phone_format($_POST['user'])) {
        $user = WPAPUser::checkPhoneFields($phone);

        if ($user->ID != $current_user_id) {
            $html .= '<li data-id="' . esc_attr($user->ID) . '" data-name="' . esc_attr($user->user_login) . '">' . get_avatar($user->ID, 30);
            $name = $user->user_login != $user->display_name ? ' (' . $user->display_name . ')' : '';
            $html .= '<span>' . esc_html($user->user_login . $name) . '</span></li>';
            wp_send_json_success($html);
        }
    }

    foreach ($users as $user) {
        $html .= '<li data-id="' . esc_attr($user->ID) . '" data-name="' . esc_attr($user->user_login) . '">' . get_avatar($user->ID, 30);
        $name = $user->user_login != $user->display_name ? ' (' . $user->display_name . ')' : '';
        $html .= '<span>' . esc_html($user->user_login . $name) . '</span></li>';
    }

    $not_found = '<span>' . esc_html__('کاربری یافت نشد.', 'arvand-panel') . '</span>';
    wp_send_json_success(empty($html) ? $not_found : $html);
});

add_action('wp_ajax_wpap_hide_menu', function () {
    if (!check_ajax_referer('hide_menu', 'nonce')) {
        wp_send_json_error();
    }

    if (empty($_POST['id'])) {
        wp_send_json_error();
    }

    $id = (int)$_POST['id'];

    global $wpdb;
    $table = $wpdb->prefix . 'wpap_menus';
    $menu = $wpdb->get_row($wpdb->prepare("SELECT `menu_display` FROM `$table` WHERE `menu_id` = %d", $id));

    if (!$menu) {
        wp_send_json_error();
    }

    $display = 'hide' === $menu->menu_display ? 'show' : 'hide';
    $update = $wpdb->update($table, ['menu_display' => $display], ['menu_id' => $id], null, '%d');

    if (!$update) {
        wp_send_json_error();
    }

    update_option('wpap_menus_cache', '');

    wp_send_json_success($display);
});

add_action('wp_ajax_wpap_delete_menu', function () {
    if (!check_ajax_referer('delete_menu', 'nonce')) {
        wp_send_json_error();
    }

    if (empty($_POST['id'])) {
        wp_send_json_error();
    }

    $menu_db = new WPAPMenuDB;
    $id = (int)$_POST['id'];

    if (!$menu_db->delete($id)) {
        wp_send_json_error();
    }

    $children = $menu_db->getMenus($id);

    if ($children) {
        global $wpdb;

        foreach ($children as $child) {
            $wpdb->update("{$wpdb->prefix}wpap_menus", ['menu_parent' => 0], ['menu_parent' => $id], null, ['%d']);
        }
    }

    update_option('wpap_menus_cache', '');

    wp_send_json_success();
});

add_action('admin_init', function () {
    if (empty($_POST['wpap_private_message_delete']) || empty($_POST['wpap_message_delete_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['wpap_message_delete_nonce'], 'wpap_message_delete')) {
        return;
    }

    $post = get_post(absint($_POST['wpap_private_message_delete']));
    if (!$post) {
        return;
    }

    $post_id = $post->ID;

    $attachment = get_post_meta($post_id, 'wpap_msg_attachment', 1);
    if ($attachment) {
        WPAPFile::delete($attachment['path']);
    }

    if (!$post->post_parent > 0) {
        $children = get_posts([
            'post_type' => 'wpap_private_message',
            'numberposts' => -1,
            'post_parent' => $post_id
        ]);

        if (!empty($children)) {
            foreach ($children as $child) {
                $attachment = get_post_meta($child->ID, 'wpap_msg_attachment', 1);

                if ($attachment) {
                    WPAPFile::delete($attachment['path']);
                }

                wp_delete_post($child->ID, true);
            }
        }
    }

    wp_delete_post($post_id, true);

    if ($post->post_parent) {
        wp_redirect(add_query_arg(['section' => 'single', 'msg' => $post->post_parent], admin_url('admin.php?page=wpap-private-message')));
    } else {
        wp_redirect(admin_url('admin.php?page=wpap-private-message'));
    }

    exit;
});