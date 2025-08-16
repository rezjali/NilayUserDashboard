<?php

namespace Arvand\ArvandPanel\Form\Fields;

defined('ABSPATH') || exit;

use Arvand\ArvandPanel\Form\WPAPField;
use Arvand\ArvandPanel\Form\WPAPFieldHtml;
use Arvand\ArvandPanel\Form\WPAPFieldSettingsHtml;
use Arvand\ArvandPanel\Form\WPAPFieldSettingsValidation;

class wpap_field_user_pass extends WPAPField
{
    public $type = 'users';
    public $repeatable = false;

    public static function defaultSettings(): array
    {
        return [
            'field_name' => 'user_pass',
            'type' => 'text',
            'label' => __('Password', 'arvand-panel'),
            'attrs' => [
                'name' => 'user_pass',
                'type' => 'password',
                'placeholder' => '',
            ],
            'rules' => [
                'required' => true,
                'min_length' => 6,
            ],
            'description' => '',
            'display' => 'both',
            'pass2_label' => __('Confirm password', 'arvand-panel'),
            'pass2_placeholder' => '',
            'pass2_description' => '',
        ];
    }

    public static function adminButton(): array
    {
        return ['ri-key-2-line', __('Password', 'arvand-panel')];
    }

    public function settingsValidation($field): array
    {
        $validation = new WPAPFieldSettingsValidation($field, $this);

        $validate = $validation->validate(function () use ($validation, $field) {
            $validation->new_settings['pass2_label'] = isset($field['pass2_label']) ? sanitize_text_field($field['pass2_label']) : '';
            $validation->new_settings['pass2_placeholder'] = isset($field['pass2_placeholder']) ? sanitize_text_field($field['pass2_placeholder']) : '';
            $validation->new_settings['pass2_description'] = isset($field['pass2_description']) ? sanitize_text_field($field['pass2_description']) : '';
            return true;
        });

        return [$validate, $validation->new_settings];
    }

    public function settingsOutput(array $settings = null, $id = null): string
    {
        $html = new WPAPFieldSettingsHtml(self::defaultSettings(), $settings, $id);

        return $html->settings(function () use ($html, $settings) {
            $html->wrap(sprintf(
                '<label>%s</label><input class="wpap-field-pass2-label" type="text" name="fields[%s][pass2_label]" value="%s" />',
                esc_html__('Confirm password label', 'arvand-panel'),
                esc_attr($html->id),
                esc_attr($html->settings['pass2_label'])
            ));

            $html->wrap(sprintf(
                '<label>%s</label><input class="wpap-field-pass2-placeholder" type="text" name="fields[%s][pass2_placeholder]" value="%s" />',
                esc_html__('Confirm password placeholder', 'arvand-panel'),
                esc_attr($html->id),
                esc_attr($html->settings['pass2_placeholder'])
            ));

            $html->wrap(sprintf(
                '<label>%s</label><textarea class="wpap-field-pass2-description" name="fields[%s][pass2_description]">%s</textarea>',
                esc_html__('Confirm password description', 'arvand-panel'),
                esc_attr($html->id),
                esc_attr($html->settings['pass2_description'])
            ));
        });
    }

    public function output(array $settings, $value = null): void
    {
        $html = new WPAPFieldHtml($settings, $value);
        $html->wrap($html->text());

        printf('<label class="wpap-field-wrap">%s%s%s</label>',
            sprintf('<span class="wpap-field-label">%s</span>', esc_html($settings['pass2_label'])),
            sprintf("<input type='password' name='password2' placeholder='%s'/>", esc_attr($settings['pass2_placeholder'])),
            sprintf("<span class='wpap-input-info'>%s</span>", esc_html($settings['pass2_description']))
        );

        if (wpap_register_options()['pass_strength']) {
            echo '<label class="wpap-field-wrap"><span id="wpap-password-strength"></span></label>';
        }
    }

    public function validation(): ?string
    {
        if ($_POST['user_pass'] !== $_POST['password2']) {
            return __('Two passwords do not match.', 'arvand-panel');
        }

        return null;
    }

    public function value(): string
    {
        return sanitize_text_field(wpap_en_num($_POST['user_pass']));
    }
}