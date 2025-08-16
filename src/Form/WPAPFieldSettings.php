<?php

namespace Arvand\ArvandPanel\Form;

defined('ABSPATH') || exit;

class WPAPFieldSettings
{
    public static function getDefault($field_name = null): ?array
    {
        $settings = require(WPAP_INC_PATH . 'field-settings.php');

        if ($field_name) {
            return $settings[$field_name] ?? null;
        }

        return $settings;
    }

    public static function get(string $field_name = null)
    {
        if (!$fields = get_option('wpap_register_fields')) {
            return false;
        }

        $fields = json_decode($fields, true);

        if ($field_name) {
            return $fields[$field_name] ?? false;
        }

        return $fields;
    }

    public static function password(): array
    {
        $fields = get_option('wpap_register_fields');

        if ($fields) {
            $fields = json_decode($fields, true);

            if (isset($fields['user_pass'])) {
                return $fields['user_pass'];
            }
        }

        return self::getDefault('user_pass');
    }
}