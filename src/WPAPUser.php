<?php

namespace Arvand\ArvandPanel;

defined('ABSPATH') || exit;

use Arvand\ArvandPanel\DB\WPAPUserDB;

class WPAPUser
{
    public static function accessPrepare($access): array
    {
        $roles = get_editable_roles();
        $allowed = [];
        $role_arr = [];

        foreach ($roles as $role => $details) {
            $allowed[] = $role;
        }

        if (is_array($access)) {
            for ($i = 0; $i < count($access); $i++) {
                if (in_array($access[$i], $allowed)) {
                    $role_arr[] = sanitize_text_field($access[$i]);
                }
            }
        }

        return $role_arr;
    }

    public static function phoneMetaFields($phone = null, $only_keys = false): array
    {
        if ($only_keys) {
            return [
                'wpap_user_phone_number',
                'eh_user_phone',
                'billing_phone',
                'digits_phone',
            ];
        }

        $meta_array = [
            ['key' => 'wpap_user_phone_number', 'phone' => $phone],
            ['key' => 'eh_user_phone', 'phone' => $phone],
            ['key' => 'billing_phone', 'phone' => $phone],
            ['key' => 'billing_phone', 'phone' => substr_replace($phone, '98', 0, 1)],
            ['key' => 'billing_phone', 'phone' => substr_replace($phone, '+98', 0, 1)],
            ['key' => 'billing_phone', 'phone' => substr_replace($phone, '', 0, 1)],
            ['key' => 'digits_phone', 'phone' => $phone],
            ['key' => 'digits_phone', 'phone' => substr_replace($phone, '98', 0, 1)],
            ['key' => 'digits_phone', 'phone' => substr_replace($phone, '+98', 0, 1)],
            ['key' => 'digits_phone_no', 'phone' => substr_replace($phone, '', 0, 1)]
        ];

        return $meta_array;
    }

    public static function checkPhoneFields($phone, $return_id = false)
    {
        $phone = apply_filters('wpap_sent_phone_number', $phone);
        $meta_array = self::phoneMetaFields($phone);

        foreach ($meta_array as $meta) {
            if ($user = WPAPUserDB::getUserByPhone($meta['phone'], $meta['key'], $return_id)) {
                return $user;
            }
        }

        return false;
    }

    public static function userHasPhone($user_id): bool
    {
        $keys = self::phoneMetaFields(null, true);

        foreach ($keys as $key) {
            if (WPAPUserDB::getPhoneByUserId($user_id, $key)) {
                return true;
            }
        }

        return false;
    }

    public static function uploadProfileImage($img_file)
    {
        $allowed_ext = ['image/jpeg', 'image/jpg', 'image/png'];
        $panel = wpap_panel_options();

        if (!in_array($img_file['type'], $allowed_ext)) {
            return ['error' => __('Image format is not allowed.', 'arvand-panel')];
        }

        if ($img_file['size'] > (1204 * $panel['avatar_size'])) {
            return ['error' => sprintf(__('Maximum avatar image size is %d KB.', 'arvand-panel'), $panel['avatar_size'])];
        }

        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        $upload = wp_handle_upload($img_file, ['test_form' => false]);

        if (!$upload or isset($upload['error'])) {
            return ['error' => __('There is a problem uploading avatar.', 'arvand-panel')];
        }

        $user_id = get_current_user_id();

        $get_previous_path = get_user_meta($user_id, 'wpap_profile_img_path', true);
        wp_delete_file($get_previous_path);

        update_user_meta($user_id, 'wpap_profile_img', $upload['url']);

        $img_path = wp_normalize_path(trailingslashit(wp_upload_dir()['path'])) . basename($upload['url']);
        update_user_meta($user_id, 'wpap_profile_img_path', $img_path);

        return true;
    }
}