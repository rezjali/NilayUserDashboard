<?php

namespace Arvand\ArvandPanel\Form\Fields;

defined('ABSPATH') || exit;

use Arvand\ArvandPanel\Form\WPAPField;
use Arvand\ArvandPanel\Form\WPAPFieldHtml;
use Arvand\ArvandPanel\Form\WPAPFieldSettingsHtml;
use Arvand\ArvandPanel\Form\WPAPFieldSettingsValidation;

class wpap_field_drop_down extends WPAPField
{
    public $type = 'user_meta';

    public static function defaultSettings(): array
    {
        return [
            'field_name' => 'drop_down',
            'type' => 'select',
            'select_text' => __('--select--', 'arvand-panel'),
            'label' => __('Dropdown', 'arvand-panel'),
            'meta_key' => '',
            'attrs' => [
                'name' => 'drop_down',
            ],
            'options' => ['Option 1'],
            'rules' => [
                'required' => false,
            ],
            'description' => '',
            'display' => 'both'
        ];
    }

    public static function adminButton(): array
    {
        return ['ri-menu-fill', __('Dropdown', 'arvand-panel')];
    }

    public function settingsOutput(array $settings = null, $id = null): string
    {
        $html = new WPAPFieldSettingsHtml(self::defaultSettings(), $settings, $id);

        return $html->settings(function () use ($html) {
            $html->wrap(sprintf(
                '<label>%s</label><input class="wpap-field-select-text" type="text" name="fields[%s][select_text]" value="%s"/>',
                esc_html__('Select text', 'arvand-panel'),
                esc_attr($html->id),
                esc_attr($html->settings['select_text'])
            ));
        });
    }

    public function output(array $settings, $value = null): void
    {
        $class = is_admin() ? 'class="regular-text"' : '';
        $options = '';

        if (!empty($settings['select_text'])) {
            $options .= sprintf('<option value="-1">%s</option>', esc_html($settings['select_text']));
        }

        foreach ($settings['options'] as $option) {
            $options .=  sprintf(
                '<option value="%1$s" %2$s>%1$s</option>',
                esc_html($option),
                selected($value, $option, false)
            );
        }

        $html = new WPAPFieldHtml($settings, $value);
        $html->wrap(sprintf('<select name="%s" %s>%s</select>', $settings['attrs']['name'], $class, $options));
    }

    public function settingsValidation($field): array
    {
        $validation = new WPAPFieldSettingsValidation($field, $this);

        return [
            $validation->validate(function () use ($validation, $field) {
                if (empty($field['select_text'])) {
                    $validation->new_settings['select_text'] = self::defaultSettings()['select_text'];
                } else {
                    $validation->new_settings['select_text'] = sanitize_text_field($field['select_text']);
                }

                return true;
            }),
            $validation->new_settings
        ];
    }

    public function adminValidation($field_settings): bool
    {
        $value = $_POST[$field_settings['attrs']['name']] ?? '';

        if ('-1' != $value && !in_array($value, $field_settings['options'])) {
            return false;
        }

        return true;
    }

    public function validation($settings): ?string
    {
        $attr_name = $settings['attrs']['name'];
        $display_name = $settings['label'];

        if ($settings['rules']['required'] && '-1' === $_POST[$attr_name]) {
            return sprintf(__('%s is required.', 'arvand-panel'), $display_name);
        }

        if ($_POST[$attr_name] !== '-1' && !in_array($_POST[$attr_name], $settings['options'])) {
            return sprintf(__('%s field is not selected correctly.', 'arvand-panel'), $display_name);
        }

        return null;
    }
}