<?php

namespace Arvand\ArvandPanel\Form\Fields;

defined('ABSPATH') || exit;

use Arvand\ArvandPanel\Form\WPAPField;
use Arvand\ArvandPanel\Form\WPAPFieldHtml;
use Arvand\ArvandPanel\Form\WPAPFieldSettingsValidation;
use Arvand\ArvandPanel\SMS\WPAPSMS;
use Arvand\ArvandPanel\WPAPUser;

class wpap_field_mobile extends WPAPField
{
    public $type = 'user_meta';
    public $repeatable = false;

    public function __construct()
    {
        parent::__construct(self::defaultSettings());
    }

    public static function defaultSettings(): array
    {
        return [
            'field_name' => 'mobile',
            'type' => 'text',
            'label' => __('Phone', 'arvand-panel'),
            'attrs' => [
                'name' => 'mobile',
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
        return ['ri-smartphone-line', __('Mobile Field', 'arvand-panel')];
    }

    public function settingsValidation($field): array
    {
        $validation = new WPAPFieldSettingsValidation($field, $this);

        return [
            $validation->validate(function () use ($validation) {
                $validation->new_settings['meta_key'] = 'wpap_user_phone_number';
                return true;
            }),
            $validation->new_settings
        ];
    }

    public function output(array $settings, $value = null): void
    {
        $html = new WPAPFieldHtml($settings, $value);
        $desc = '';

        if (is_admin()) {
            $desc = '<p class="description">' . esc_html__('Number registered with Arvand Panel. To change or add, mobile number must be valid and not already registered.', 'arvand-panel') . '</p>';
        }

        $html->wrap($html->text() . $desc);
    }

    public function adminValidation(): bool
    {
        $phone = wpap_phone_format($_POST['mobile']);

        if (!$phone || WPAPUser::checkPhoneFields($phone)) {
            return false;
        }

        return true;
    }

    public function validation($field_settings = null): ?string
    {
        $phone = wpap_phone_format($_POST['mobile']);

        if ($field_settings['rules']['required'] && empty($_POST['mobile'])) {
            return __('لطفاً شماره همراه خود را وارد کنید.', 'arvand-panel');
        }

        if (!empty($_POST['mobile']) && !$phone) {
            return __('لطفاً شماره همراه معتبری وارد کنید.', 'arvand-panel');
        }

        if ($phone && WPAPUser::checkPhoneFields($phone)) {
            return __('شماره همراه قبلا ثبت شده است.', 'arvand-panel');
        }

        return null;
    }

    public function value(): string
    {
        return sanitize_text_field(wpap_en_num($_POST['mobile']));
    }
}