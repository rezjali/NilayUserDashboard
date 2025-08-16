<?php

namespace Arvand\ArvandPanel\Form\Fields;

defined('ABSPATH') || exit;

use Arvand\ArvandPanel\Form\WPAPField;

class wpap_field_number_field extends WPAPField
{
    public $type = 'user_meta';

    public function __construct()
    {
        parent::__construct(self::defaultSettings());
    }

    public static function defaultSettings(): array
    {
        return [
            'field_name' => 'number_field',
            'type' => 'text',
            'label' => __('Number Field', 'arvand-panel'),
            'meta_key' => '',
            'attrs' => [
                'name' => 'number_field',
                'type' => 'number',
                'placeholder' => ''
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
        return ['ri-hashtag', __('Number Field', 'arvand-panel')];
    }
}