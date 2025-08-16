<?php
defined('ABSPATH') || exit;

use Arvand\ArvandPanel\Form\WPAPFieldSettings;
use Arvand\ArvandPanel\WPAPFile;
use Arvand\ArvandPanel\WPAPUser;

add_action('wp_ajax_nopriv_wpap_register', function () {
    if (!check_ajax_referer('register_nonce', 'wpap_register_nonce')) {
        wp_send_json_error(__('Invalid Request.', 'arvand-panel'));
    }

    if (!isset($_POST['user_login'])) {
        wp_send_json_error(__('Username is required.', 'arvand-panel'));
    }

    if (!isset($_POST['user_email'])) {
        wp_send_json_error(__('Email is required.', 'arvand-panel'));
    }

    if (!isset($_POST['user_pass'])) {
        wp_send_json_error(__('Password is required.', 'arvand-panel'));
    }

    $fields = WPAPFieldSettings::get();
    $messages = [];

    foreach ($fields as $field) {
        if (!in_array($field['display'], ['register', 'both'])) {
            continue;
        }

        $name = $field['field_name'];
        $field_class = 'Arvand\ArvandPanel\Form\Fields\wpap_field_' . $name;

        if (!class_exists($field_class)) {
            continue;
        }

        $display_name = esc_html($field['label']);
        $attr_name = $field['attrs']['name'];
        $attr_type = $field['attrs']['type'];

        if ($field['rules']['required']) {
            if (('file' !== $attr_type && empty($_POST[$attr_name])) || ('file' === $attr_type && empty($_FILES[$attr_name]['tmp_name']))) {
                $messages[] = sprintf(__('%s must not be empty.', 'arvand-panel'), $display_name);
            }

            if (isset($field['rules']['min_length']) && strlen($_POST[$attr_name]) < $field['rules']['min_length']) {
                $messages[] = sprintf(__('Minimum %s letters must be %d', 'arvand-panel'), $display_name, $field['rules']['min_length']);
            }
        }

        $field_class = new $field_class;

        if (method_exists($field_class, 'validation') && $error = $field_class->validation($field)) {
            $messages[] = $error;
        }
    }

    $register = wpap_register_options();

    if ($register['enable_agree'] && $register['agree_required'] && !isset($_POST['agree'])) {
        $messages[] = __('You must agree to the terms.', 'arvand-panel');
    }

    $google_opt = wpap_google_options();

    if ($google_opt['enable_recaptcha'] && !empty($google_opt['recaptcha_secret_key'])) {
        if (!wpap_recaptcha_validate($google_opt['recaptcha_secret_key'])) {
            $messages[] = __('Recaptcha verification failed, please try again.', 'arvand-panel');
        }
    }

    do_action('wpap_register_fields_validate', $messages);

    if (!empty($messages)) {
        wp_send_json_error($messages);
    }

    wpap_register_fields_save($fields);
});

add_action('wp_ajax_wpap_user_edit', function () {
    $current_user = wp_get_current_user();

    if ($current_user->user_login === WPAP_DEMO) {
        wp_send_json(['status' => 'error', 'msg' => __('کاربر دمو قادر به ذخیره سازی نیست.', 'arvand-panel')]);
    }

    if (!isset($_POST['user_edit_nonce']) || !wp_verify_nonce($_POST['user_edit_nonce'], 'user_edit_nonce')) {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid Request.', 'arvand-panel')]);
    }

    $messages = [];

    if (isset($_FILES['profile_pic']) && !empty($_FILES['profile_pic']['name'])) {
        $response = WPAPUser::uploadProfileImage($_FILES['profile_pic']);

        if (isset($response['error'])) {
            $messages[] = $response['error'];
        }
    }

    $fields = WPAPFieldSettings::get();
    $user_data = [];
    $user_meta = [];
    $files = [];

    foreach ($fields as $field) {
        $name = $field['field_name'];
        $field_class = 'Arvand\ArvandPanel\Form\Fields\wpap_field_' . $name;

        if (!class_exists($field_class)) {
            continue;
        }

        if (!in_array($field['display'], ['panel', 'both'])) {
            continue;
        }

        if (in_array($field['field_name'], ['user_login', 'user_pass', 'user_email', 'mobile'])) {
            continue;
        }

        $display_name = esc_html($field['label']);
        $attr_name = $field['attrs']['name'];
        $attr_type = $field['attrs']['type'];

        if ($field['rules']['required']) {
            if ('file' !== $attr_type && empty($_POST[$attr_name])) {
                $messages[] = sprintf(__('%s must not be empty.', 'arvand-panel'), $display_name);
            }

            if ('file' === $attr_type && empty($_FILES[$field['attrs']['name']]['tmp_name']) && !get_user_meta($current_user->ID, $field['meta_key'], 1)) {
                $messages[] = sprintf(__('%s must not be empty.', 'arvand-panel'), $display_name);
            }

            if (isset($field['rules']['min_length']) && strlen($_POST[$attr_name]) < $field['rules']['min_length']) {
                $messages[] = sprintf(__('Minimum %s letters must be %d', 'arvand-panel'), $display_name, $field['rules']['min_length']);
            }
        }

        $field_class = new $field_class;

        if (method_exists($field_class, 'validation') && $error = $field_class->validation($field)) {
            $messages[] = $error;
        }

        if ('users' === $field_class->type) {
            $user_data[$attr_name] = sanitize_text_field($_POST[$attr_name]);
        }

        if ('file' !== $field['attrs']['type'] && 'user_meta' === $field_class->type) {
            if (method_exists($field_class, 'value')) {
                $user_meta[$field['meta_key']] = $field_class->value($field);
            } else {
                $user_meta[sanitize_text_field($field['meta_key'])] = sanitize_text_field($_POST[$attr_name] ?? '');
            }
        }

        if ('file' === $field['attrs']['type'] && 'user_meta' === $field_class->type) {
            if (empty($_FILES[$field['attrs']['name']]['tmp_name'])) {
                continue;
            }

            $files[] = ['file' => $_FILES[$field['attrs']['name']], 'meta_key' => $field['meta_key']];
        }
    }

    do_action('wpap_user_edit_validate_fields', $current_user, $messages);

    if (count($messages) > 0) {
        wp_send_json(['status' => 'error', 'msg' => $messages]);
    }

    $user_data['ID'] = $current_user->ID;
    $user_id = wp_update_user($user_data + ['meta_input' => $user_meta]);

    if (is_wp_error($user_id)) {
        wp_send_json(['status' => 'error', 'msg' => __('There is a problem editing the profile.', 'arvand-panel')]);
    }

    if (!empty($files)) {
        foreach ($files as $item) {
            $file_meta = get_user_meta($current_user->ID, $item['meta_key'], 1);

            if ($file_meta) {
                unlink(wp_upload_dir()['basedir'] . '/' . $file_meta['path']);
            }

            $upload = WPAPFile::upload($item['file'], 'user');

            if (false !== $upload) {
                update_user_meta($user_id, sanitize_text_field($item['meta_key']), $upload);
            }
        }
    }

    do_action('wpap_user_edit_fields_save', $current_user);
    wp_send_json(['status' => 'success', 'msg' => __('Changes applied successfully.', 'arvand-panel')]);
});
