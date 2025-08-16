<?php

namespace Arvand\ArvandPanel\Form\Fields;

defined( 'ABSPATH' ) || exit;

use Arvand\ArvandPanel\Form\WPAPField;

class wpap_field_first_name extends WPAPField
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
            'field_name' => 'first_name',
            'type' => 'text',
            'label' => __('First Name', 'arvand-panel'),
            'attrs' => [
                'name' => 'first_name',
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
        return ['ri-user-line', __('Firstname', 'arvand-panel')];
    }
}