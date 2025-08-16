<?php

namespace Arvand\ArvandPanel\Form\Fields;

defined('ABSPATH') || exit;

use Arvand\ArvandPanel\Form\WPAPField;

class wpap_field_display_name extends WPAPField
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
            'field_name' => 'display_name',
            'type' => 'text',
            'label' => __('Display Name', 'arvand-panel'),
            'attrs' => [
                'name' => 'display_name',
                'type' => 'text',
                'placeholder' => '',
            ],
            'rules' => [
                'required' => false,
                'min_length' => 0,
                'max_length' => 20,
            ],
            'description' => '',
            'display' => 'both',
        ];
    }

    public static function adminButton(): array
    {
        return ['ri-eye-line', __('Display Name', 'arvand-panel')];
    }
}