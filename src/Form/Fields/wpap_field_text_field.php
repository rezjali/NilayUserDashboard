<?php

namespace Arvand\ArvandPanel\Form\Fields;

defined('ABSPATH') || exit;

use Arvand\ArvandPanel\Form\WPAPField;

class wpap_field_text_field extends WPAPField
{
    public $type = 'user_meta';

    public function __construct()
    {
        parent::__construct(self::defaultSettings());
    }

    public static function defaultSettings(): array
    {
        return [
            'field_name' => 'text_field',
            'type' => 'text',
            'label' => __('Text Field', 'arvand-panel'),
            'meta_key' => '',
            'attrs' => [
                'name' => 'text_field',
                'type' => 'text',
                'placeholder' => '',
            ],
            'rules' => [
                'required' => false,
                'min_length' => 0,
                'max_length' => 20,
            ],
            'description' => '',
            'display' => 'both'
        ];
    }

    public static function adminButton(): array
    {
        return ['ri-text-block', __('Text Field', 'arvand-panel')];
    }
}