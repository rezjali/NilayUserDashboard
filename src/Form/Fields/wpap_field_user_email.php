<?php

namespace Arvand\ArvandPanel\Form\Fields;

defined('ABSPATH') || exit;

use Arvand\ArvandPanel\Form\WPAPField;

class wpap_field_user_email extends WPAPField
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
            'field_name' => 'user_email',
            'type' => 'text',
            'label' => __('Email', 'arvand-panel'),
            'attrs' => [
                'name' => 'user_email',
                'type' => 'email',
                'placeholder' => '',
            ],
            'rules' => [
                'required' => true,
            ],
            'description' => '',
            'display' => 'both'
        ];
    }

    public static function adminButton(): array
    {
        return ['ri-mail-line', __('Email', 'arvand-panel')];
    }

    public function validation(): ?string
    {
        if (!is_email($_POST['user_email'])) {
            return __('Invalid Email', 'arvand-panel');
        } elseif (email_exists($_POST['user_email'])) {
            return __('Email already exist.', 'arvand-panel');
        }

        return null;
    }

    public function value(): string
    {
        return sanitize_email(wpap_en_num($_POST['user_email']));
    }
}