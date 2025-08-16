<?php

namespace Arvand\ArvandPanel\Form\Fields;

defined('ABSPATH') || exit;

use Arvand\ArvandPanel\Form\WPAPField;

class wpap_field_user_login extends WPAPField
{
    public $type = 'users';
    public $repeatable = false;

    public function __construct()
    {
        parent::__construct(self::defaultSettings());
    }

    public static function defaultSettings(): array
    {
        return [
            'field_name' => 'user_login',
            'type' => 'text',
            'label' => __('Username', 'arvand-panel'),
            'attrs' => [
                'name' => 'user_login',
                'type' => 'text',
                'placeholder' => '',
            ],
            'rules' => [
                'required' => true,
                'min_length' => 6,
                'max_length' => 20,
            ],
            'description' => '',
            'display' => 'both',
        ];
    }

    public static function adminButton(): array
    {
        return ['ri-login-circle-line', __('Username', 'arvand-panel')];
    }

    public function validation(): ?string
    {
        if (username_exists($_POST['user_login'])) {
            return __('Username already exist.', 'arvand-panel');
        }

        return null;
    }

    public function value(): string
    {
        return sanitize_user(wpap_en_num($_POST['user_login']));
    }
}