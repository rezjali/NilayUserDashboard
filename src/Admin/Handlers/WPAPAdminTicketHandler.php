<?php

namespace Arvand\ArvandPanel\Admin\Handlers;

defined('ABSPATH') || exit;

use Arvand\ArvandPanel\SMS\WPAPSendMessage;
use Arvand\ArvandPanel\WPAPFile;
use Arvand\ArvandPanel\WPAPUser;

class WPAPAdminTicketHandler
{
    public static function newTicket(): array
    {
        if (!isset($_POST['new_ticket'])) {
            return [];
        }

        if (!isset($_POST['new_ticket_nonce']) || !wp_verify_nonce($_POST['new_ticket_nonce'], 'new_ticket')) {
            return ['ok' => false, 'msg' => __('Invalid Request.', 'arvand-panel')];
        }

        if (empty($_POST['ticket_title'])) {
            return ['ok' => false, 'msg' => __('لطفاً عنوان را وارد کنید.', 'arvand-panel')];
        }

        if (empty($_POST['user'])) {
            return ['ok' => false, 'msg' => __('لطفاً نام کاربری گیرنده را وارد کنید.', 'arvand-panel')];
        }

        if (!$user = get_user_by('login', sanitize_text_field($_POST['user']))) {
            return ['ok' => false, 'msg' => __('نام کاربری صحیح نیست.', 'arvand-panel')];
        }

        if (empty($_POST['ticket_content'])) {
            return ['ok' => false, 'msg' => __('لطفاً پیام را وارد کنید.', 'arvand-panel')];
        }

        $ticket_opt = wpap_ticket_options();

        if (!empty($_FILES['wpap_attachment']['tmp_name'])) {
            $extension = pathinfo($_FILES['wpap_attachment']['name'])['extension'];

            if (!in_array($extension, ['jpeg', 'jpg', 'png', 'zip', 'pdf'])) {
                return ['ok' => false, 'msg' => __('Attachment file format is not allowed.', 'arvand-panel')];
            }

            if ($_FILES['wpap_attachment']['size'] > (1204 * $ticket_opt['ticket_attachment_size'])) {
                return [
                    'ok' => false,
                    'msg' => sprintf(__('Maximum attachment size is %d KB.', 'arvand-panel'), $ticket_opt['ticket_attachment_size'])
                ];
            }
        }

        $meta_data = [
            'wpap_from_admin' => get_current_user_id(),
            'wpap_ticket_creator' => get_current_user_id(),
            'wpap_ticket_recipient' => $user->ID,
            'wpap_ticket_status' => 'open'
        ];

        $department_opt = wpap_ticket_department_options();

        if (isset($_POST['ticket_department']) && in_array($_POST['ticket_department'], $department_opt['departments'])) {
            $meta_data['wpap_ticket_department'] = sanitize_text_field($_POST['ticket_department']);
        }

        $insert = wp_insert_post([
            'post_type' => 'wpap_ticket',
            'post_title' => wp_strip_all_tags($_POST['ticket_title']),
            'post_content' => wp_kses_post($_POST['ticket_content']),
            'post_status' => 'publish',
            'meta_input' => $meta_data
        ]);

        if (is_wp_error($insert)) {
            return ['ok' => false, 'msg' => __('An error occurred in creating ticket.', 'arvand-panel')];
        }

        if (!empty($_FILES['wpap_attachment']['tmp_name'])) {
            $upload = WPAPFile::upload($_FILES['wpap_attachment'], 'attachments/ticket');

            if (false !== $upload) {
                add_post_meta($insert, 'wpap_ticket_attachment', $upload);
            }
        }

        $headers = ['Content-Type: text/html; charset=UTF-8'];
        $search = ['[site_name]', '[site_url]', '[ticket_title]'];
        $replace = [get_bloginfo('name'), site_url(), sanitize_text_field($_POST['ticket_title'])];
        $email_opt = wpap_email_options();
        $email_subject = sanitize_text_field($email_opt['new_ticket_email_subject']);
        $email_content = str_replace($search, $replace, $email_opt['new_ticket_email_content']);
        wp_mail($user->user_email, $email_subject, wpap_email_template($email_content), $headers);

        if ($ticket_opt['enable_ticket_sms']) {
            $user_mobile = get_user_meta($user->ID, 'wpap_user_phone_number', true);

            if ($user_mobile) {
                $sms_text = str_replace($search, $replace, $ticket_opt['new_ticket_sms_text']);
                WPAPSendMessage::send($user_mobile, $sms_text);
            }
        }

        return ['ok' => true, 'msg' => __('Ticket created.', 'arvand-panel')];
    }

    public static function editTicket(int $post_id): array
    {
        if (isset($_POST['attachment_delete'])) {
            if ($meta = get_post_meta($post_id, 'wpap_ticket_attachment', 1)) {
                delete_post_meta($post_id, 'wpap_ticket_attachment');
                WPAPFile::delete($meta['path']);
                return ['ok' => true, 'msg' => __('فایل ضمیمه خذف شد.', 'arvand-panel')];
            }
        }

        if (!isset($_POST['edit_ticket'])) {
            return [];
        }

        if (!isset($_POST['edit_ticket_nonce']) || !wp_verify_nonce($_POST['edit_ticket_nonce'], 'edit_ticket')) {
            return ['ok' => false, 'msg' => __('Invalid Request.', 'arvand-panel')];
        }

        if (empty($_POST['ticket_content'])) {
            return ['ok' => false, 'msg' => __('لطفاً پیام را وارد کنید.', 'arvand-panel')];
        }

        $ticket_opt = wpap_ticket_options();

        if (!empty($_FILES['wpap_attachment']['tmp_name'])) {
            $extension = pathinfo($_FILES['wpap_attachment']['name'])['extension'];

            if (!in_array($extension, ['jpeg', 'jpg', 'png', 'zip', 'pdf'])) {
                return ['ok' => false, 'msg' => __('Attachment file format is not allowed.', 'arvand-panel')];
            }

            if ($_FILES['wpap_attachment']['size'] > (1204 * $ticket_opt['ticket_attachment_size'])) {
                return [
                    'ok' => false,
                    'msg' => sprintf(__('Maximum attachment size is %d KB.', 'arvand-panel'), $ticket_opt['ticket_attachment_size'])
                ];
            }
        }

        $department_opt = wpap_ticket_department_options();

        if (!empty($_POST['ticket_department']) && in_array($_POST['ticket_department'], $department_opt['departments'])) {
            $meta_data['wpap_ticket_department'] = sanitize_text_field($_POST['ticket_department']);
        } else {
            $meta_data['wpap_ticket_department'] = get_post_meta($post_id, 'wpap_ticket_department', 1);
        }

        $custom_statuses = $ticket_opt['ticket_status']['name'];

        if (!empty($_POST['status'])) {
            if (in_array($_POST['status'], ['open', 'closed', 'solved'])) {
                $meta_data['wpap_ticket_status'] = sanitize_text_field($_POST['status']);
            } elseif (in_array($_POST['status'], $custom_statuses)) {
                $key = array_search($_POST['status'], $custom_statuses);
                $meta_data['wpap_ticket_status'] = $custom_statuses[$key];
                $meta_data['wpap_ticket_status_color'] = $ticket_opt['ticket_status']['color'][$key];
                $meta_data['wpap_ticket_status_text_color'] = $ticket_opt['ticket_status']['text_color'][$key];
            } else {
                $meta_data['wpap_ticket_status'] = get_post_meta($post_id, 'wpap_ticket_status', 1);
            }
        }

        $args = [
            'ID' => $post_id,
            'post_content' => wp_kses_post($_POST['ticket_content']),
            'meta_input' => $meta_data
        ];

        if (!empty($_POST['ticket_title'])) {
            $args['post_title'] = wp_strip_all_tags($_POST['ticket_title']);
        }

        $update = wp_update_post($args);

        if (is_wp_error($update)) {
            return ['ok' => false, 'msg' => __('متأسفانه مشکلی در ویرایش تیکت بوجود آمده.', 'arvand-panel')];
        }

        if (!empty($_FILES['wpap_attachment']['tmp_name'])) {
            $upload = WPAPFile::upload($_FILES['wpap_attachment'], 'attachments/ticket');

            if (false !== $upload) {
                if ($prev_attachment = get_post_meta($post_id, 'wpap_ticket_attachment', 1)) {
                    WPAPFile::delete($prev_attachment['path']);
                }

                update_post_meta($update, 'wpap_ticket_attachment', $upload);
            }
        }

        return ['ok' => true, 'msg' => __('تیکت با موفقیت ویرایش شد.', 'arvand-panel')];
    }

    public static function ticketReply($post): array
    {
        if (!isset($_POST['ticket_reply'])) {
            return [];
        }

        $ticket_opt = wpap_ticket_options();

        if (!isset($_POST['ticket_reply_nonce'])) {
            return ['ok' => false, 'msg' => __('Invalid Request.', 'arvand-panel')];
        }

        if (!wp_verify_nonce($_POST['ticket_reply_nonce'], 'ticket_reply')) {
            return ['ok' => false, 'msg' => __('Invalid Request.', 'arvand-panel')];
        }

        if (!empty($_FILES['wpap_attachment']['name'])) {
            $allowed_ext = ['jpeg', 'jpg', 'png', 'zip', 'pdf'];
            $ext = pathinfo($_FILES['wpap_attachment']['name'])['extension'];

            if (!in_array($ext, $allowed_ext)) {
                return ['ok' => false, 'msg' => __('پسوند فایل ضمیمه مجاز نمی باشد.', 'arvand-panel')];
            }

            if ($_FILES['wpap_attachment']['size'] > (1204 * $ticket_opt['ticket_attachment_size'])) {
                return [
                    'ok' => false,
                    'msg' => sprintf(__('Maximum attachment size is %d KB.', 'arvand-panel'), $ticket_opt['ticket_attachment_size'])
                ];
            }
        }

        if (empty($_POST['ticket_content'])) {
            return ['ok' => false, 'msg' => __('لطفا پیام تیکت را وارد کنید.', 'arvand-panel')];
        }

        $recipient = get_post_meta($post->ID, 'wpap_ticket_recipient', 1);
        $from_admin = get_post_meta($post->ID, 'wpap_from_admin', 1);
        $user_id = $post->post_author;

        if ($from_admin && $post->post_author == get_current_user_id()) {
            $user_id = $recipient;
        }

        $meta_data = [
            'wpap_ticket_creator' => $post->post_author,
            'wpap_ticket_recipient' => $user_id
        ];

        $insert = wp_insert_post([
            'post_type' => 'wpap_ticket',
            'post_content' => wpautop(wp_kses($_POST['ticket_content'], 'post')),
            'post_parent' => $post->ID,
            'post_status' => 'publish',
            'meta_input' => $meta_data
        ]);

        if (is_wp_error($insert)) {
            return ['ok' => false, 'msg' => __('متأسفانه مشکلی در ارسال پاسخ بوجود آمده.', 'arvand-panel')];
        }

        if (!empty($_FILES['wpap_attachment']['name'])) {
            $upload = WPAPFile::upload($_FILES['wpap_attachment'], 'attachments/ticket');

            if (false !== $upload) {
                add_post_meta($insert, 'wpap_ticket_attachment', $upload);
            }
        }

        $time = current_time('mysql');

        wp_update_post([
            'ID' => $post->ID,
            'post_modified' => $time,
            'post_modified_gmt' => get_gmt_from_date($time),
        ]);

        $user_email = get_the_author_meta('email', $user_id);
        $headers = ['Content-Type: text/html; charset=UTF-8'];
        $search = ['[site_name]', '[site_url]', '[ticket_title]'];
        $replace = [get_bloginfo('name'), site_url(), $post->post_title];
        $email_opt = wpap_email_options();
        $email_content = str_replace($search, $replace, $email_opt['ticket_email_content']);
        wp_mail($user_email, sanitize_text_field($email_opt['ticket_email_subject']), wpap_email_template($email_content), $headers);

        if ($ticket_opt['enable_ticket_sms']) {
            $user_mobile = get_user_meta($user_id, 'wpap_user_phone_number', 1);

            if ($user_mobile) {
                $sms_text = str_replace($search, $replace, $ticket_opt['ticket_sms_text']);
                WPAPSendMessage::send($user_mobile, $sms_text);
            }
        }

        return ['ok' => true, 'msg' => __('پاسخ با موفقیت ارسال شد.', 'arvand-panel')];
    }

    public static function listFilter(): array
    {
        $args = [];

        if (!empty($_POST['ticket_number'])) {
            $args['post__in'] = [(int)$_POST['ticket_number']];
        }

        if (!empty($_POST['ticket_search'])) {
            $args['s'] = wp_strip_all_tags(sanitize_text_field($_POST['ticket_search']));
        }

        $status = '';

        if (!empty($_POST['ticket_status'])) {
            $status = sanitize_text_field($_POST['ticket_status']);
        }

        if (!empty($status)) {
            $args['meta_query'][] = [
                ['key' => 'wpap_ticket_status', 'value' => $status]
            ];
        }

        if (!empty($_POST['ticket_author_user_login'])) {
            $user = get_user_by('login', sanitize_user($_POST['ticket_author_user_login']));

            if ($user) {
                $args['author'] = $user->ID;
            }
        }

        if (!empty($_POST['ticket_creator_phone'])) {
            $user_id = WPAPUser::checkPhoneFields($_POST['ticket_creator_phone'], true);

            if ($user_id) {
                $args['author'] = $user_id;
            }
        }

        if (!empty($_POST['ticket_recipient_phone'])) {
            $user_id = WPAPUser::checkPhoneFields($_POST['ticket_recipient_phone'], true);

            if ($user_id) {
                $args['meta_query'][] = [
                    ['key' => 'wpap_ticket_recipient', 'value' => $user_id]
                ];
            }
        }

        return $args;
    }
}