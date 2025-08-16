<?php

namespace Arvand\ArvandPanel\Form;

defined('ABSPATH') || exit;

class WPAPFieldHtml
{
    public $settings;
    public $attr_name;
    public $value;
    public $label;

    public function __construct(array $settings, $value = null)
    {
        $this->settings = $settings;
        $this->label = esc_html($settings['label']);
        $this->attr_name = esc_attr($settings['attrs']['name']);
        $this->value = $value;
    }

    private function adminWrap(string $field): string
    {
        return sprintf('<tr><th>%s</th><td>%s</td></tr>', $this->label(), $field);
    }

    public function wrap(string $field): void
    {
        if (is_admin()) {
            echo $this->adminWrap($field);
            return;
        }

        printf('<label class="wpap-field-wrap">%s%s%s</label>', $this->label(), $field, $this->description());
    }

    public function attrs(): string
    {
        return implode(
            ' ',
            array_map(
                function ($key, $value) {
                    if (!empty($value)) {
                        return "$key='$value'";
                    }
                },
                array_keys($this->settings['attrs']),
                $this->settings['attrs']
            )
        );
    }

    private function label(): ?string
    {
        $required = $this->settings['rules']['required'] ? '<span class="wpap-field-required">*</span>' : '';

        if (!empty($this->label)) {
            return sprintf('<span class="wpap-field-label">%s %s</span>', $this->label, $required);
        }

        return null;
    }

    private function description(): ?string
    {
        if (!empty($this->settings['description'])) {
            return sprintf("<span class='wpap-input-info'>%s</span>", $this->settings['description']);
        }

        return null;
    }

    public function text(): string
    {
        $value = $this->value ? 'value="' . esc_attr($this->value) . '"' : '';
        $class = is_admin() ? 'class="regular-text"' : '';
        $readonly = '';

        if (is_user_logged_in() and in_array($this->settings['field_name'], ['user_login', 'user_email', 'mobile'])) {
            $readonly = !is_admin() ? 'readonly' : '';
        }

        return sprintf('<input %s %s %s %s/>', sanitize_text_field($this->attrs()), $class, $value, $readonly);
    }
}