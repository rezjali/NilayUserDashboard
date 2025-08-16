<?php

namespace Arvand\ArvandPanel\DB;

defined('ABSPATH') || exit;

class WPAPSMSDB
{
    public function insertCode($code, $phone): bool
    {
        global $wpdb, $table_prefix;
        $table = "{$table_prefix}wpap_sms_verification";

        if ($this->getPhone($phone)) {
            return (bool) $wpdb->update($table, ['code' => $code], ['mobile' => $phone], ['%d'], ['%s']);
        }

        return (bool) $wpdb->insert($table, ['mobile' => $phone, 'code' => $code], ['%s', '%d']);
    }

    public function getPhone($phone)
    {
        global $wpdb, $table_prefix;
        $table = "{$table_prefix}wpap_sms_verification";
        $res = $wpdb->get_row($wpdb->prepare("SELECT mobile FROM $table WHERE mobile = %s", [$phone]));
        return $res ?: false;
    }

    static function isValidCode($code, $phone): bool
    {
        global $wpdb;
        $code = intval($code);
        $table = $wpdb->prefix . 'wpap_sms_verification';
        return (bool) $wpdb->get_row($wpdb->prepare("SELECT `code` FROM `$table` WHERE `code` = %d AND `mobile` = %s", $code, $phone));
    }
}