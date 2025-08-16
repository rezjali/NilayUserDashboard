<?php

namespace Arvand\ArvandPanel\Admin\Handlers;

defined('ABSPATH') || exit;

use Arvand\ArvandPanel\Form\Fields\wpap_field_file;
use Arvand\ArvandPanel\Form\WPAPFieldSettings;
use Arvand\ArvandPanel\WPAPFile;
use Arvand\ArvandPanel\WPAPPluginActivation;

class WPAPAdminHandler
{
    public static function removeRegisterFields(): array
    {
        if (empty($_POST['reset_field_settings'])
            || !wp_verify_nonce($_POST['reset_field_settings'], 'reset_field_settings_nonce')
        ) {
            return [];
        }

        $fields = WPAPFieldSettings::get();
        if ($fields) {
            global $wpdb;

            foreach ($fields as $field) {
                if (!isset($field['meta_key']) || 'mobile' === $field['field_name']) {
                    continue;
                }

                if (isset($field['attrs']['type']) && 'file' === $field['attrs']['type']) {
                    wpap_field_file::removeFiles($field);
                }

                $wpdb->delete($wpdb->usermeta, ['meta_key' => $field['meta_key']], '%s');
            }
        }

        delete_option('wpap_register_fields');
        delete_option('wpap_register_fields_last_id');

        (new WPAPPluginActivation())->generateRequiredRegisterFields();

        return ['ok' => 'true', 'msg' => __('تنظیمات فیلدها به حالت پیشفرض بازگشت.', 'arvand-panel')];
    }

    public static function newPrivateMessage(): array
    {
        if (!isset($_POST['new_message'])) {
            return [];
        }

        if (!isset($_POST['new_message_nonce']) || !wp_verify_nonce($_POST['new_message_nonce'], 'new_message_nonce')) {
            return ['ok' => false, 'msg' => __('Invalid request.', 'arvand-panel')];
        }

        if (empty($_POST['user'])) {
            return ['ok' => false, 'msg' => __('لطفاً نام کاربری گیرنده را وارد کنید.', 'arvand-panel')];
        }

        if (!$user = get_user_by('login', sanitize_text_field($_POST['user']))) {
            return ['ok' => false, 'msg' => __('نام کاربری صحیح نیست.', 'arvand-panel')];
        }

        if (empty($_POST['message_content'])) {
            return ['ok' => false, 'msg' => __('Message must not be empty.', 'arvand-panel')];
        }

        if (!empty($_FILES['wpap_attachment']['tmp_name'])) {
            $ext = pathinfo($_FILES['wpap_attachment']['name'])['extension'];

            if (!in_array($ext, ['jpeg', 'jpg', 'png', 'zip', 'pdf'])) {
                return ['ok' => false, 'msg' =>  __('Attachment file format is not allowed.', 'arvand-panel')];
            }

            $general = wpap_general_options();

            if ($_FILES['wpap_attachment']['size'] > (1204 * (int)$general['private_msg_attachment_size'])) {
                return [
                    'ok' => false,
                    'msg' =>  sprintf(
                        __('Maximum attachment size is %d KB.', 'arvand-panel'),
                        size_format($general['private_msg_attachment_size'])
                    )
                ];
            }
        }

        $insert = wp_insert_post([
            'post_title' => sanitize_text_field($_POST['message_title']),
            'post_content' => wpautop(wp_kses($_POST['message_content'], 'post')),
            'post_type' => 'wpap_private_message',
            'meta_input' => ['wpap_private_msg_recipient' => $user->ID, 'wpap_seen' => 0],
            'post_status' => 'publish'
        ]);

        if (is_wp_error($insert)) {
            return ['ok' => false, 'msg' =>  __('An error occurred in sending message.', 'arvand-panel')];
        }

        if (!empty($_FILES['wpap_attachment']['name'])) {
            $upload = WPAPFile::upload($_FILES['wpap_attachment'], 'attachments/message');

            if (false !== $upload) {
                add_post_meta($insert, 'wpap_msg_attachment', $upload);
            }
        }

        return ['ok' => true, 'msg' =>  __('پیام با موفقیت به کاربر ارسال شد.', 'arvand-panel')];
    }

    public static function privateMessageReply($data = []): array
    {
        if (!isset($_POST['private_message_reply'])) {
            return [];
        }

        extract($data);

        if (empty($_POST['private_msg_nonce']) || !wp_verify_nonce($_POST['private_msg_nonce'], 'private_msg_nonce')) {
            return ['ok' => false, 'msg' => __('Invalid request.', 'arvand-panel')];
        }

        if (!empty($_FILES['wpap_attachment']['tmp_name'])) {
            $file = $_FILES['wpap_attachment'];

            if (!in_array(pathinfo($file['name'])['extension'], ['jpeg', 'jpg', 'png', 'zip', 'pdf'])) {
                return ['ok' => false, 'msg' => __('پسوند فایل ضمیمه اید مجاز نمی باشد.', 'arvand-panel')];
            }

            if ($file['size'] > (1204 * $general['private_msg_attachment_size'])) {
                return [
                    'ok' => false,
                    'msg' => sprintf(
                        __('Maximum attachment size is %d KB.', 'arvand-panel'),
                        $general['private_msg_attachment_size']
                    )
                ];
            }
        }

        if (empty($_POST['private_message_content'])) {
            return ['ok' => false, 'msg' => __('Message content must not be empty.', 'arvand-panel')];
        }

        $insert = wp_insert_post([
            'post_content' => wpautop(wp_kses($_POST['private_message_content'], 'post')),
            'post_type' => 'wpap_private_message',
            'meta_input' => ['wpap_private_msg_recipient' => $user->ID, 'wpap_seen' => 0],
            'post_status' => 'publish',
            'post_parent' => $post->ID
        ]);

        if (!is_wp_error($insert)) {
            if (!empty($_FILES['wpap_attachment']['tmp_name'])) {
                $upload = WPAPFile::upload($_FILES['wpap_attachment'], 'attachments/message');

                if (false !== $upload) {
                    add_post_meta($insert, 'wpap_msg_attachment', $upload);
                }
            }

            return ['ok' => true, 'msg' => __('Message sent.', 'arvand-panel')];
        }

        return [];
    }

    public static function editPrivateMessage($data = []): array
    {
        extract($data);

        if (isset($_POST['attachment_delete'])) {
            if ($meta = get_post_meta($post->ID, 'wpap_msg_attachment', 1)) {
                delete_post_meta($post->ID, 'wpap_msg_attachment');
                WPAPFile::delete($meta['path']);
                return ['ok' => true, 'msg' => __('فایل ضمیمه خذف شد.', 'arvand-panel')];
            }
        }

        if (!isset($_POST['private_message_edit'])) {
            return [];
        }

        if (empty($_POST['edit_message_nonce']) || !wp_verify_nonce($_POST['edit_message_nonce'], 'edit_message')) {
            return ['ok' => false, 'msg' => __('Invalid request.', 'arvand-panel')];
        }

        if (!empty($_FILES['wpap_attachment']['tmp_name'])) {
            $file = $_FILES['wpap_attachment'];

            if (!in_array(pathinfo($file['name'])['extension'], ['jpeg', 'jpg', 'png', 'zip', 'pdf'])) {
                return ['ok' => false, 'msg' => __('پسوند فایل ضمیمه اید مجاز نمی باشد.', 'arvand-panel')];
            }

            if ($file['size'] > (1204 * $general['private_msg_attachment_size'])) {
                return [
                    'ok' => false,
                    'msg' => sprintf(
                        __('حداکثر حجم ضمیمه %s می باشد.', 'arvand-panel'),
                        size_format(1024 * $general['private_msg_attachment_size'])
                    )
                ];
            }
        }

        if (empty($_POST['message_content'])) {
            return ['ok' => false, 'msg' => __('Message content must not be empty.', 'arvand-panel')];
        }

        $insert = wp_update_post([
            'ID' => $post->ID,
            'post_title' => empty($_POST['message_title']) ? '' : wp_strip_all_tags($_POST['message_title']),
            'post_content' => wp_kses_post($_POST['message_content'])
        ]);

        if (is_wp_error($insert)) {
            return ['ok' => false, 'msg' => __('متأسفانه مشکلی در ارسال پیام بوجود آمده.', 'arvand-panel')];
        }

        if (!empty($_FILES['wpap_attachment']['tmp_name'])) {
            $upload = WPAPFile::upload($_FILES['wpap_attachment'], 'attachments/message');

            if (false !== $upload) {
                if ($prev_attachment = get_post_meta($post->ID, 'wpap_msg_attachment', 1)) {
                    WPAPFile::delete($prev_attachment['path']);
                }

                update_post_meta($insert, 'wpap_msg_attachment', $upload);
            }
        }

        return ['ok' => true, 'msg' => __('پیام با موفقیت ویرایش شد.', 'arvand-panel')];
    }
}