<?php

namespace Arvand\ArvandPanel\DB;

defined('ABSPATH') || exit;

class WPAPUserDB
{
    public static function getUserByPhone($phone, $key, $return_id = false)
    {
        global $wpdb;
        $query = "SELECT `user_id` FROM `$wpdb->usermeta` WHERE `meta_value` = %s AND `meta_key` = %s LIMIT 1";
        $res = $wpdb->get_row($wpdb->prepare($query, $phone, $key));

        if (!$res) {
            return false;
        }

        if ($return_id) {
            return $res->user_id;
        }

        $user = get_user_by('id', $res->user_id);

        if (!$user) {
            return false;
        }

        return $user;
    }

    public static function getPhoneByUserId($user_id, $key)
    {
        global $wpdb;
        $query = "SELECT `meta_value` FROM `$wpdb->usermeta` WHERE `user_id` = %d AND `meta_key` = %s LIMIT 1";
        $res = $wpdb->get_row($wpdb->prepare($query, $user_id, $key));
        return $res ? $res->meta_value : false;
    }
}