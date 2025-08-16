<?php
defined( 'ABSPATH' ) || exit;

return [
    'first_name' => [
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
    ],
    'last_name' => [
        'field_name' => 'last_name',
        'type' => 'text',
        'label' => __('Last Name', 'arvand-panel'),
        'attrs' => [
            'name' => 'last_name',
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
    ],
    'display_name' => [
        'field_name' => 'display_name',
        'type' => 'text',
        'label' => __('Display Name', 'arvand-panel'),
        'attrs' => [
            'name' => 'display_name',
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
    ],
    'user_login' => [
        'field_name' => 'user_login',
        'type' => 'text',
        'label' => __('Username', 'arvand-panel'),
        'attrs' => [
            'name' => 'user_login',
            'type' => 'text',
            'placeholder' => '',
        ],
        'rules' => [
            'required' => true,
            'min_length' => 6,
            'max_length' => 20,
        ],
        'description' => '',
        'display' => 'both',
    ],
    'user_email' => [
        'field_name' => 'user_email',
        'type' => 'text',
        'label' => __('Email', 'arvand-panel'),
        'attrs' => [
            'name' => 'user_email',
            'type' => 'email',
            'placeholder' => '',
        ],
        'rules' => [
            'required' => true,
        ],
        'description' => '',
        'display' => 'both'
    ],
    'user_pass' => [
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
    ],
    'user_url' => [
        'field_name' => 'user_url',
        'type' => 'text',
        'label' => __('Website', 'arvand-panel'),
        'attrs' => [
            'name' => 'user_url',
            'type' => 'text',
            'placeholder' => '',
        ],
        'rules' => [
            'required' => false,
        ],
        'description' => '',
        'display' => 'both'
    ],
    'mobile' => [
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
    ],
    'text_field' => [
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
    ],
    'radio' => [
        'field_name' => 'radio',
        'type' => 'radio',
        'label' => __('Radio button', 'arvand-panel'),
        'meta_key' => '',
        'attrs' => [
            'name' => 'radio',
            'type' => 'radio',
        ],
        'options' => ['Option 1'],
        'rules' => [
            'required' => false,
        ],
        'description' => '',
        'display' => 'both'
    ],
    'checkbox' => [
        'field_name' => 'checkbox',
        'type' => 'checkbox',
        'label' => __('Checkbox', 'arvand-panel'),
        'meta_key' => '',
        'attrs' => [
            'name' => 'checkbox',
            'type' => 'checkbox',
        ],
        'options' => ['Option 1'],
        'rules' => [
            'required' => false,
        ],
        'description' => '',
        'display' => 'both'
    ],
    'drop_down' => [
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
    ],
    'number_field' => [
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
    ],
    'textarea' => [
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
    ],
];