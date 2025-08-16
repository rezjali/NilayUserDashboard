<?php
defined( 'ABSPATH' ) || exit;

use Arvand\ArvandPanel\WPAPUser;

add_action('add_meta_boxes', function () {
    add_meta_box(
        'wpap-important-notice',
        __('Important Notice', 'arvand-panel'),
        function ($post) {
            require_once WPAP_ADMIN_TEMPLATES_PATH . 'meta-box/important-notice.php';
        },
        'wpap_notifications',
        'side'
    );
});

add_action('save_post', function ($post_id, $post) {
    if (!isset($_POST['important_notice_nonce']) || !wp_verify_nonce($_POST['important_notice_nonce'], 'important_notice_nonce')) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (wp_is_post_autosave($post_id)) {
        return;
    }

    if (wp_is_post_revision($post_id)) {
        return;
    }

    if (isset($_POST['wpap_important_notice'])) {
        update_post_meta($post_id, 'wpap_important_notice', 1);

        update_post_meta(
            $post_id,
            'wpap_important_notice_place',
            sanitize_text_field($_POST['important_notice_display_place'] ?? 'dash')
        );

        update_post_meta(
            $post_id,
            'wpap_important_notice_type',
            in_array($_POST['important_notice_type'] ?? 'info', ['info', 'error', 'success', 'warning'])
                ? $_POST['important_notice_type']
                : 'info'
        );
    } else {
        update_post_meta($post_id, 'wpap_important_notice', 0);
    }

    if (isset($_POST['notice_recipient_type'])) {
        update_post_meta(
            $post_id,
            'wpap_notice_recipient_type',
            in_array($_POST['notice_recipient_type'], ['roles', 'user'])
                ? $_POST['notice_recipient_type']
                : 'all'
        );
    }

    if (isset($_POST['important_notice_roles'])) {
        update_post_meta(
            $post_id,
            'wpap_important_notice_roles',
            array_intersect(
                array_map('sanitize_text_field', $_POST['important_notice_roles']),
                array_keys(wp_roles()->get_names())
            )
        );
    }

    if (!isset($_POST['important_notice_user'])) {
        return;
    }

    $user = sanitize_text_field($_POST['important_notice_user']);
    $user_id = 0;

    $users = get_users([
        'search' => $user,
        'search_columns' => ['ID', 'user_login', 'user_email', 'display_name']
    ]);

    if (!empty($users)) {
        $user_id = $users[0]->ID;
    }

    if (!$user_id > 0 && $phone = wpap_phone_format($user)) {
        $user_id = WPAPUser::checkPhoneFields($phone, true) ?: 0;
    }

    update_post_meta($post_id, 'wpap_important_notice_user', $user);
    update_post_meta($post_id, 'wpap_important_notice_user_id', $user_id);
}, 10, 2);