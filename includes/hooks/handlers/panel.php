<?php
defined('ABSPATH') || exit;

use Arvand\ArvandPanel\Form\WPAPFieldSettings;
use Arvand\ArvandPanel\SMS\WPAPSendMessage;
use Arvand\ArvandPanel\WPAPFile;

add_action('wp_ajax_wpap_panel_theme', function () {
    if (!isset($_POST['theme'])) {
        wp_send_json_error();
    }

    update_user_meta(
        get_current_user_id(),
        'wpap_panel_theme',
        sanitize_text_field($_POST['theme'] === 'dark' ? 'dark' : 'light')
    );

    wp_send_json_success();
});

add_action('wp_ajax_wpap_delete_avatar', function () {
    if (wp_get_current_user()->user_login === WPAP_DEMO) {
        wp_send_json(['status' => 'error', 'msg' => __('کاربر دمو قادر به حذف تصویر نیست.', 'arvand-panel')]);
    }

    if (!isset($_POST['user_id']) || !is_numeric($_POST['user_id']) || !get_user_by('id', absint($_POST['user_id']))) {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid Request.', 'arvand-panel')]);
    }

    $user_id = absint($_POST['user_id']);
    $img_path = get_user_meta($user_id, 'wpap_profile_img_path', 1);
    wp_delete_file($img_path);

    if (delete_user_meta($user_id, 'wpap_profile_img')) {
        $url = get_avatar_url($user_id);
        wp_send_json(['status' => 'success', 'msg' => __('Profile image deleted.', 'arvand-panel'), 'url' => $url]);
    }

    wp_send_json(['status' => 'error', 'msg' => __('An error occurred in deleting profile image.', 'arvand-panel')]);
});

add_action('wp_ajax_wpap_change_pass', function () {
    $user = wp_get_current_user();

    if ($user->user_login === WPAP_DEMO) {
        wp_send_json(['status' => 'error', 'msg' => __('کاربر دمو قادر به ذخیره سازی نیست.', 'arvand-panel')]);
    }

    if (
        !isset($_POST['change_password_nonce'])
        || !wp_verify_nonce(wp_unslash($_POST['change_password_nonce']), 'change_password_nonce')
    ) {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid Request.', 'arvand-panel')]);
    }

    if (!isset($_POST['user_pass']) || !isset($_POST['confirm_user_pass'])) {
        wp_send_json(['status' => 'error', 'msg' => __('Password must not be empty.', 'arvand-panel')]);
    }

    if ($_POST['user_pass'] !== $_POST['confirm_user_pass']) {
        wp_send_json(['status' => 'error', 'msg' => __('Two passwords do not match.', 'arvand-panel')]);
    }

    $pass_options = WPAPFieldSettings::password();

    if (strlen($_POST['user_pass']) < $pass_options['rules']['min_length']) {
        wp_send_json([
            'status' => 'error',
            'msg' => sprintf(
                __('Minimum password letters must be %d', 'arvand-panel'),
                esc_html($pass_options['rules']['min_length'])
            )
        ]);
    }

    $user_id = wp_update_user(['ID' => $user->ID, 'user_pass' => wpap_en_num($_POST['user_pass'])]);
    if (is_wp_error($user_id)) {
        wp_send_json(['status' => 'error', 'msg' => $user_id->get_error_message()]);
    }

    wp_send_json(['status' => 'success', 'msg' => __('Password changed.', 'arvand-panel')]);
});

add_action('wp_ajax_wpap_email_send', function () {
    $current_user = wp_get_current_user();

    if (WPAP_DEMO === $current_user->user_login) {
        wp_send_json(['status' => 'error', 'msg' => __('کاربر دمو قادر به ذخیره سازی نیست.', 'arvand-panel')]);
    }

    if (
        !isset($_POST['send_email_verification_code_nonce'])
        || !wp_verify_nonce(wp_unslash($_POST['send_email_verification_code_nonce']), 'send_email_verification_code_nonce')
    ) {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid request.', 'arvand-panel')]);
    }

    if (!isset($_POST['email']) || !is_email($_POST['email'])) {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid email.', 'arvand-panel')]);
    }

    $email = sanitize_email($_POST['email']);
    $headers = ['Content-Type: text/html; charset=UTF-8'];
    $message = '<h3 style="color: #0078ff">' . __('کد تایید ایمیل', 'arvnad-panel') . '</h3>';
    $message .= '<p>' . __('کد تایید ایمیل شما:', 'arvnad-panel') . '</p>';
    $code = rand(100000, 999999);
    $message .= '<p style="font-size: 22px">' . $code . '</p>';
    $message .= '<p style="color: #0078ff">' . site_url() . '</p>';

    if (email_exists($email)) {
        $status = get_user_meta($current_user->ID, 'wpap_user_status', true);

        if ($_POST['user_email'] === $current_user->user_email && !$status) {
            wp_update_user(['ID' => $current_user->ID, 'user_activation_key' => $code]);
            update_user_meta($current_user->ID, 'wpap_user_status', 0);
            wp_mail($email, __("تغییر ایمیل", 'arvand-panel'), wpap_email_template($message), $headers);

            wp_send_json([
                'status' => 'success',
                'msg' => __('یک کد تایید جدید به ایمیلی که وارد کرده اید ارسال شد. لطفا این کد را وارد بفرمائید.', 'arvand-panel')
            ]);
        }

        wp_send_json(['status' => 'error', 'msg' => __('ایمیل از قبل وجود دارد.', 'arvand-panel')]);
    }

    wp_update_user(['ID' => $current_user->ID, 'user_activation_key' => $code]);
    update_user_meta($current_user->ID, 'wpap_user_status', 0);
    wp_mail($email, __("Change Email", 'arvand-panel'), wpap_email_template($message), $headers);

    wp_send_json([
        'status' => 'success',
        'msg' => __('یک کد تایید به ایمیلی که وارد کرده اید ارسال شد. لطفا این کد را وارد بفرمائید.', 'arvand-panel')
    ]);
});

add_action('wp_ajax_wpap_email_verify', function () {
    $current_user = wp_get_current_user();

    if (WPAP_DEMO === $current_user->user_login) {
        wp_send_json(['status' => 'error', 'msg' => __('کاربر دمو قادر به ذخیره سازی نیست.', 'arvand-panel')]);
    }

    if (
        !isset($_POST['verify_email_nonce'])
        || !wp_verify_nonce(wp_unslash($_POST['verify_email_nonce']), 'verify_email_nonce')
    ) {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid request.', 'arvand-panel')]);
    }

    if (empty($_POST['session_email']) || !is_email($_POST['session_email']) || email_exists($_POST['session_email'])) {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid request.', 'arvand-panel')]);
    }

    if (trim(wpap_en_num($_POST['verification_code'])) !== $current_user->user_activation_key) {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid code.', 'arvand-panel')]);
    }

    $update_email = wp_update_user(['ID' => $current_user->ID, 'user_email' => sanitize_email(wpap_en_num($_POST['session_email']))]);

    if (is_wp_error($update_email)) {
        wp_send_json(['status' => 'error', 'msg' => __('An error occurred in email verification.', 'arvand-panel')]);
    }

    $admin = get_user_by('email', get_bloginfo('admin_email'));

    if ($admin && $current_user->ID == $admin->ID) {
        update_option('admin_email', sanitize_email(wpap_en_num($_POST['email'])));
    }

    wp_update_user(['ID' => $current_user->ID, 'user_activation_key' => '']);
    update_user_meta($current_user->ID, 'wpap_user_status', 1);
    wp_send_json(['status' => 'success', 'msg' => __('Your email successfully verified.', 'arvand-panel')]);
});

add_action('wp_ajax_wpap_send_ticket', function () {
    $current_user = wp_get_current_user();

    if ($current_user->user_login === WPAP_DEMO) {
        wp_send_json(['status' => 'error', 'msg' => __('کاربر دمو قادر به ذخیره سازی نیست.', 'arvand-panel')]);
    }

    if (!isset($_POST['new_ticket_nonce']) || !wp_verify_nonce(wp_unslash($_POST['new_ticket_nonce']), 'new_ticket')) {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid Request.', 'arvand-panel')]);
    }

    if (empty($_POST['ticket_subject'])) {
        wp_send_json(['status' => 'error', 'msg' => __('Title must not be empty.', 'arvand-panel')]);
    }

    if (empty($_POST['ticket_content'])) {
        wp_send_json(['status' => 'error', 'msg' => __('Message must not be empty.', 'arvand-panel')]);
    }

    $ticket_opt = wpap_ticket_options();

    if (!empty($_FILES['wpap_attachment']['tmp_name'])) {
        $extension = pathinfo($_FILES['wpap_attachment']['name'])['extension'];

        if (!in_array(strtolower($extension), ['jpeg', 'jpg', 'png', 'zip', 'pdf'])) {
            wp_send_json(['status' => 'error', 'msg' => esc_html__('Attachment file format is not allowed.', 'arvand-panel')]);
        }

        if ($_FILES['wpap_attachment']['size'] > (1204 * $ticket_opt['ticket_attachment_size'])) {
            wp_send_json(['status' => 'error', 'msg' => sprintf(__('Maximum ticket attachment size size is %d KB.', 'arvand-panel'), $ticket_opt['ticket_attachment_size'])]);
        }
    }

    $meta_data = [
        'wpap_ticket_priority' => sanitize_text_field($_POST['ticket_priority']),
        'wpap_ticket_status' => 'open',
        'wpap_ticket_creator' => $current_user->ID,
    ];

    $ticket_department_opt = wpap_ticket_department_options();

    if (isset($_POST['ticket_department']) && in_array($_POST['ticket_department'], $ticket_department_opt['departments'])) {
        $meta_data['wpap_ticket_department'] = sanitize_text_field($_POST['ticket_department']);
    }

    $subject = sanitize_text_field(wp_strip_all_tags($_POST['ticket_subject']));

    $insert = wp_insert_post([
        'post_type' => 'wpap_ticket',
        'post_status' => 'publish',
        'post_title' => $subject,
        'post_content' => wp_kses($_POST['ticket_content'], 'post'),
        'meta_input' => $meta_data,
    ]);

    if (is_wp_error($insert)) {
        wp_send_json(['status' => 'error', 'msg' => __('An error occurred in creating ticket.', 'arvand-panel')]);
    }

    if (!empty($_FILES['wpap_attachment']['tmp_name'])) {
        $upload = WPAPFile::upload($_FILES['wpap_attachment'], 'attachments/ticket');

        if (false !== $upload) {
            add_post_meta($insert, 'wpap_ticket_attachment', $upload);
        }
    }

    $ticket_dep = get_post_meta($insert, 'wpap_ticket_department', true);
    $department_users = get_users([
        'meta_query' => [
            [
                'key' => 'wpap_user_ticket_department',
                'value' => '"' . $ticket_dep . '"',
                'compare' => 'LIKE'
            ]
        ]
    ]);

    $email_opt = wpap_email_options();
    $search = ['[site_name]', '[site_url]', '[ticket_title]'];
    $replace = [get_bloginfo('name'), site_url(), $subject];

    if ($email_opt['enable_ticket_email']) {
        $headers = ['Content-Type: text/html; charset=UTF-8'];
        $email_content = str_replace($search, $replace, $email_opt['new_ticket_email_content']);

        foreach ($department_users as $user) {
            wp_mail(
                $user->user_email,
                sanitize_text_field($email_opt['new_ticket_email_subject']),
                wpap_email_template($email_content),
                $headers
            );
        }
    }

    if ($ticket_opt['enable_ticket_sms']) {
        $sms_text = str_replace($search, $replace, $ticket_opt['new_ticket_sms_text']);

        foreach ($department_users as $user) {
            $user_mobile = get_user_meta($user->ID, 'wpap_user_phone_number', true);
            if ($user_mobile) {
                WPAPSendMessage::send($user_mobile, $sms_text);
            }
        }
    }

    wp_send_json(['status' => 'success', 'msg' => __('Ticket created. To view the ticket, go to the "Tickets" section.', 'arvand-panel')]);
});

add_action('wp_ajax_wpap_send_ticket_reply', function () {
    $current_user = wp_get_current_user();

    if ($current_user->user_login === WPAP_DEMO) {
        wp_send_json(['status' => 'error', 'msg' => __('کاربر دمو قادر به ذخیره سازی نیست.', 'arvand-panel')]);
    }

    if (empty($_POST['ticket_reply_nonce']) || !wp_verify_nonce(wp_unslash($_POST['ticket_reply_nonce']), 'ticket_reply')) {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid Request.', 'arvand-panel')]);
    }

    $opt = wpap_ticket_options();

    if (!empty($_FILES['wpap_attachment']['tmp_name'])) {
        $allowed_ext = ['image/jpeg', 'image/jpg', 'image/png', 'application/zip', 'application/x-zip-compressed', 'application/pdf'];

        if (!in_array($_FILES['wpap_attachment']['type'], $allowed_ext)) {
            wp_send_json(['status' => 'error', 'msg' => __('Attachment file format is not allowed.', 'arvand-panel')]);
        }

        if ($_FILES['wpap_attachment']['size'] > (1204 * $opt['ticket_attachment_size'])) {
            wp_send_json(['status' => 'error', 'msg' => sprintf(__('Maximum ticket attachment size size is %d KB.', 'arvand-panel'), $opt['ticket_attachment_size'])]);
        }
    }

    if (empty($_POST['ticket_content'])) {
        wp_send_json(['status' => 'error', 'msg' => __('Reply must not be empty.', 'arvand-panel')]);
    }

    if (empty($_POST['post']) || !is_numeric($_POST['post'])) {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid Request.', 'arvand-panel')]);
    }

    $post = get_post(absint($_POST['post']));

    if (!$post || $post->post_type !== 'wpap_ticket') {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid Request.', 'arvand-panel')]);
    }

    if (!empty($_POST['recipient']) && !is_numeric($_POST['recipient'])) {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid Request.', 'arvand-panel')]);
    }

    if ($_POST['recipient'] && !get_user_by('id', absint($_POST['recipient']))) {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid Request.', 'arvand-panel')]);
    }

    $post_data = [
        'post_type' => 'wpap_ticket',
        'post_content' => wpautop(wp_kses($_POST['ticket_content'], 'post')),
        'post_parent' => $post->ID,
        'post_status' => 'publish',
    ];

    $post_data['meta_input']['wpap_ticket_creator'] = $post->post_author;

    if ($_POST['recipient']) {
        $post_data['meta_input']['wpap_ticket_recipient'] = $_POST['recipient'];
    }

    $insert = wp_insert_post($post_data);

    if (is_wp_error($insert)) {
        wp_send_json(['status' => 'error', 'msg' => __('An error occurred in sending reply.', 'arvand-panel')]);
    }

    $pre_status = get_post_meta($post->ID, 'wpap_ticket_status', true);
    $status = (in_array($_POST['ticket_status'], ['open', 'solved', 'closed']) or in_array($_POST['ticket_status'], $opt['ticket_status']['name'])) ? $_POST['ticket_status'] : $pre_status;
    update_post_meta($post->ID, 'wpap_ticket_status', $status);

    if (in_array($_POST['ticket_status'], $opt['ticket_status']['name'])) {
        $key = array_search($_POST['ticket_status'], $opt['ticket_status']['name']);
        update_post_meta($post->ID, 'wpap_ticket_status_color', $opt['ticket_status']['color'][$key]);
        update_post_meta($post->ID, 'wpap_ticket_status_text_color', $opt['ticket_status']['text_color'][$key]);
    }

    if (!empty($_FILES['wpap_attachment']['tmp_name'])) {
        $upload = WPAPFile::upload($_FILES['wpap_attachment'], 'attachments/ticket');

        if (false !== $upload) {
            add_post_meta($insert, 'wpap_ticket_attachment', $upload);
        }
    }

    $time = current_time('mysql');

    wp_update_post([
        'ID' => $post->ID,
        'post_modified' => $time,
        'post_modified_gmt' => get_gmt_from_date($time)
    ]);

    $headers = ['Content-Type: text/html; charset=UTF-8'];
    $search = ['[site_name]', '[site_url]', '[ticket_title]'];
    $replace = [get_bloginfo('name'), site_url(), $post->post_title];
    $email_opt = wpap_email_options();
    $email_content = str_replace($search, $replace, $email_opt['ticket_email_content']);
    $sms_text = str_replace($search, $replace, $opt['ticket_sms_text']);
    $ticket_dep = get_post_meta($post->ID, 'wpap_ticket_department', true);

    $users = get_users([
        'meta_query' => [
            ['key' => 'wpap_user_ticket_department', 'value' => '"' . $ticket_dep . '"', 'compare' => 'LIKE']
        ]
    ]);

    if ($post->post_author == $current_user->ID) {
        if ($email_opt['enable_ticket_email']) {
            foreach ($users as $user) {
                wp_mail($user->user_email, sanitize_text_field($email_opt['ticket_email_subject']), wpap_email_template($email_content), $headers);
            }
        }

        if ($opt['enable_ticket_sms']) {
            foreach ($users as $user) {
                $user_mobile = get_user_meta($user->ID, 'wpap_user_phone_number', true);

                if ($user_mobile) {
                    WPAPSendMessage::send($user_mobile, $sms_text);
                }
            }
        }
    } else {
        $user = get_user_by('id', $post->post_author);

        if ($email_opt['enable_ticket_email']) {
            wp_mail($user->user_email, sanitize_text_field($email_opt['ticket_email_subject']), wpap_email_template($email_content), $headers);
        }

        if ($opt['enable_ticket_sms']) {
            $user_mobile = get_user_meta($user->ID, 'wpap_user_phone_number', true);

            if ($user_mobile) {
                WPAPSendMessage::send($user_mobile, $sms_text);
            }
        }
    }

    wp_send_json(['status' => 'success', 'msg' => __('Reply Sent.', 'arvand-panel'), 'reload' => 'reload']);
});

add_action('wp_ajax_wpap_send_msg_reply', function () {
    $current_user = wp_get_current_user();

    if ($current_user->user_login === WPAP_DEMO) {
        wp_send_json(['status' => 'error', 'msg' => __('کاربر دمو قادر به ذخیره سازی نیست.', 'arvand-panel')]);
    }

    if (!isset($_POST['private_msg_nonce']) || !wp_verify_nonce(wp_unslash($_POST['private_msg_nonce']), 'private_msg_nonce')) {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid Request.', 'arvand-panel')]);
    }

    if (empty($_POST['message'])) {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid Request.', 'arvand-panel')]);
    }

    $message = get_post(absint($_POST['message']));

    if (!$message or $message->post_type !== 'wpap_private_message') {
        wp_send_json(['status' => 'error', 'msg' => __('Invalid Request.', 'arvand-panel')]);
    }

    if (empty($_POST['private_message_content'])) {
        wp_send_json(['status' => 'error', 'msg' => __('Message content must not be empty.', 'arvand-panel')]);
    }

    $allowed_ext = ['image/jpeg', 'image/jpg', 'image/png', 'application/zip', 'application/x-zip-compressed', 'application/pdf'];

    if (!empty($_FILES['wpap_attachment']['tmp_name']) && !in_array($_FILES['wpap_attachment']['type'], $allowed_ext)) {
        wp_send_json(['status' => 'error', 'msg' => __('Attachment file format is not allowed.', 'arvand-panel')]);
    }

    $general = wpap_general_options();

    if (!empty($_FILES['wpap_attachment']['name']) && $_FILES['wpap_attachment']['size'] > (1204 * $general['private_msg_attachment_size'])) {
        wp_send_json(['status' => 'error', 'msg' => sprintf(__('Maximum attachment size is %d KB.', 'arvand-panel'), $general['private_msg_attachment_size'])]);
    }

    $insert = wp_insert_post([
        'post_content' => wpautop(wp_kses($_POST['private_message_content'], 'post')),
        'post_type' => 'wpap_private_message',
        'meta_input' => [
            'wpap_private_msg_recipient' => $message->post_author,
            'wpap_admin_seen' => 0
        ],
        'post_status' => 'publish',
        'post_parent' => $message->ID
    ]);

    if (is_wp_error($insert)) {
        wp_send_json(['status' => 'error', 'msg' => __('An error occurred in sending reply.', 'arvand-panel')]);
    }

    if (!empty($_FILES['wpap_attachment']['tmp_name'])) {
        $upload = WPAPFile::upload($_FILES['wpap_attachment'], 'attachments/message');

        if (false !== $upload) {
            add_post_meta($insert, 'wpap_msg_attachment', $upload);
        }
    }

    wp_send_json(['status' => 'success', 'msg' => __('Message sent.', 'arvand-panel'), 'reload' => 'reload']);
});