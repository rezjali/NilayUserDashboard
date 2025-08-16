<?php

namespace Arvand\ArvandPanel\Form\Fields;

defined('ABSPATH') || exit;

use Arvand\ArvandPanel\Form\WPAPField;
use Arvand\ArvandPanel\Form\WPAPFieldHtml;

class wpap_field_textarea extends WPAPField
{
    public $type = 'user_meta';

    public function __construct()
    {
        parent::__construct(self::defaultSettings());
    }

    public static function defaultSettings(): array
    {
        return [
            'field_name' => 'textarea',
            'type' => 'textarea',
            'label' => __('Textarea', 'arvand-panel'),
            'meta_key' => '',
            'attrs' => [
                'name' => 'textarea',
                'placeholder' => ''
            ],
            'rules' => [
                'required' => false,
                'min_length' => 0,
                'max_length' => 0,
            ],
            'description' => '',
            'display' => 'both'
        ];
    }

    public static function adminButton(): array
    {
        return ['ri-text-snippet', __('Textarea Field', 'arvand-panel')];
    }

    public function output(array $settings, $value = null): void
    {
        $html = new WPAPFieldHtml($settings, $value);
        $html->wrap(sprintf("<textarea %s>%s</textarea>", sanitize_text_field($html->attrs()), esc_html($value)));
    }
}