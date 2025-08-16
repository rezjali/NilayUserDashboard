<?php

namespace Arvand\ArvandPanel\Form;

defined('ABSPATH') || exit;

class WPAPFieldSettingsValidation
{
    private $field_class;
    private $field = [];
    private $field_name;
    private $default_settings = [];
    public $new_settings = [];

    public function __construct($field, $field_class)
    {
        $this->field_name = $field['field_name'];
        $this->field_class = new $field_class;
        $this->field = $field;
        $this->default_settings = $field_class::defaultSettings();
    }

    public function validate(callable $custom_validation = null): bool
    {
        $this->new_settings['id'] = absint($this->field['id']);
        $this->new_settings['field_name'] = $this->default_settings['field_name'];
        $this->new_settings['type'] = $this->default_settings['type'];

        $this->label();
        $this->display();
        $this->required();
        $this->description();

        $is_valid = true;

        if (isset($this->default_settings['attrs']['name'])) {
            $is_valid = $this->attr_name();
        }

        if (isset($this->default_settings['attrs']['type'])) {
            $is_valid = $this->attr_type();
        }

        if (isset($this->default_settings['attrs']['placeholder'])) {
            $is_valid = $this->placeholder();
        }

        if (isset($this->default_settings['options'])) {
            $is_valid = $this->options();
        }

        if (isset($this->default_settings['rules']['min_length'])) {
            $is_valid = $this->min_length();
        }

        if (isset($this->default_settings['rules']['max_length'])) {
            $is_valid = $this->max_length();
        }

        if (isset($this->default_settings['meta_key'])) {
            $is_valid = $this->meta_key();
        }

        if ($custom_validation) {
            $is_valid = call_user_func($custom_validation);
        }

        return $is_valid;
    }

    public function display(): bool
    {
        if (isset($this->field['display']) && in_array($this->field['display'], ['both', 'register', 'panel'])) {
            $this->new_settings['display'] = $this->field['display'];
        } else {
            $this->new_settings['display'] = 'both';
        }

        return true;
    }

    public function label(): bool
    {
        if (isset($this->field['label'])) {
            $this->new_settings['label'] = sanitize_text_field($this->field['label']);
        } else {
            $this->new_settings['label'] = '';
        }

        return true;
    }

    public function attr_name(): bool
    {
        if (!$this->field_class->repeatable) {
            $this->new_settings['attrs']['name'] = $this->field_name;
        } else {
            $this->new_settings['attrs']['name'] = 'signup_field_' . absint($this->field['id']);
        }

        return true;
    }

    public function attr_type(): bool
    {
        $this->new_settings['attrs']['type'] = $this->default_settings['attrs']['type'];

        return true;
    }

    public function placeholder(): bool
    {
        $this->new_settings['attrs']['placeholder'] = isset($this->field['placeholder']) ? sanitize_text_field($this->field['placeholder']) : '';

        return true;
    }

    public function options(): bool
    {
        if (!isset($this->field['options']) || !is_array($this->field['options']) || empty(array_filter($this->field['options']))) {
            return false;
        }

        $this->new_settings['options'] = array_map('sanitize_text_field', array_filter($this->field['options']));

        return true;
    }

    public function description(): bool
    {
        $this->new_settings['description'] = isset($this->field['description']) ? sanitize_text_field($this->field['description']) : '';

        return true;
    }

    public function required(): bool
    {
        $required_fields = in_array($this->field_name, ['user_login', 'user_email', 'user_pass']);
        $this->new_settings['rules']['required'] = ($required_fields || isset($this->field['required']));

        return true;
    }

    public function min_length(): bool
    {
        $this->new_settings['rules']['min_length'] = isset($this->field['min_length']) ? (int)$this->field['min_length'] : 0;

        return true;
    }

    public function max_length(): bool
    {
        $this->new_settings['rules']['max_length'] = isset($this->field['max_length']) ? (int)$this->field['max_length'] : 0;

        return true;
    }

    public function meta_key(): bool
    {
        if (empty($this->field['meta_key'])) {
            return false;
        }

        $this->new_settings['meta_key'] = sanitize_text_field($this->field['meta_key']);

        return true;
    }
}