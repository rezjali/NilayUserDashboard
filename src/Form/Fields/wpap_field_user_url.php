<?php

namespace Arvand\ArvandPanel\Form\Fields;

defined('ABSPATH') || exit;

use Arvand\ArvandPanel\Form\WPAPField;

class wpap_field_user_url extends WPAPField
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
            'field_name' => 'user_url',
            'type' => 'text',
            'label' => __('Website', 'arvand-panel'),
            'attrs' => [
                'name' => 'user_url',
                'type' => 'text',
                'placeholder' => '',
            ],
            'rules' => [
                'required' => false,
            ],
            'description' => '',
            'display' => 'both'
        ];
    }

    public static function adminButton(): array
    {
        return ['ri-link', __('Website', 'arvand-panel')];
    }

    public function value(): string
    {
        return sanitize_url($_POST['user_url']);
    }
}