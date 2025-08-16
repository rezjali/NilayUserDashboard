<?php

namespace Arvand\ArvandPanel\Form;

defined('ABSPATH') || exit;

class WPAPField
{
    public $type = '';
    public $repeatable = true;
    protected $default_settings;

    public function __construct($default_settings = null)
    {
        $this->default_settings = $default_settings;
    }

    public function settingsValidation($field): array
    {
        $validation = new WPAPFieldSettingsValidation($field, $this);
        return [$validation->validate(), $validation->new_settings];
    }

    public function settingsOutput(array $settings = null, $id = null): string
    {
        $html = new WPAPFieldSettingsHtml($this->default_settings, $settings, $id);
        return $html->settings();
    }

    public function output(array $settings, $value = null): void
    {
        $html = new WPAPFieldHtml($settings, $value);
        $html->wrap($html->text());
    }
}