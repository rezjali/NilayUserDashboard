<?php
defined('ABSPATH') || exit;

function wpap_general_options(): array
{
    return wp_parse_args(get_option('wpap_general'), [
        'admin_bar_access' => [],
        'admin_area_access' => [],
        'private_msg_attachment_size' => 1000,
        'add_to_list' => false,
        'add_to_list_btn_display' => false,
        'delete_plugin_data' => false
    ]);
}

function wpap_register_options(): array
{
    $default = [
        'enable_def_reg' => true,
        'default_register' => false,
        'pass_strength' => true,
        'register_activation' => true,
        'enable_admin_approval' => false,
        'sms_reg_password' => true,
        'enable_agree' => false,
        'agree_text' => sprintf(__('I agree to terms', 'arvand-panel'), '<a href="#">' . __('terms', 'arvand-panel') . '</a>'),
        'agree_required' => true,
    ];

    return wp_parse_args(get_option('wpap_register'), $default);
}

function wpap_login_options(): array
{
    $default = [
        'enable_def_login' => true,
        'default_login' => false,
        'enable_sms_register_login' => false,
        'force_to_add_mobile' => false,
        'reset_pass_method' => 'email',
    ];

    return wp_parse_args(get_option('wpap_login'), $default);
}

function wpap_google_options(): array
{
    $default = [
        'enable_recaptcha' => false,
        'recaptcha_site_key' => '',
        'recaptcha_secret_key' => '',
    ];

    return wp_parse_args(get_option('wpap_google_options'), $default);
}

function wpap_account_menu_options(): array
{
    return wp_parse_args(get_option('wpap_account_menu'), [
        'non_logged_in_btn' => 'signup_login',
        'logged_in_btn' => 'avatar',
        'logged_in_btn_icon' => 'ri-user-3-line',
        'logged_in_btn_text' => __('My Account', 'arvand-panel'),
        'menus' => ['user_edit', 'notifications', 'tickets', 'private_msg']
    ]);
}

function wpap_panel_options(): array
{
    $default = [
        'fullscreen_compatibility' => false,
        'display_top_sidebar' => true,
        'display_sidebar_links' => true,
        'logo_url' => '',
        'upload_avatar' => true,
        'avatar_size' => 100,
        'notifications_per_page' => 8,
        'comments_per_page' => 8,
    ];

    return wp_parse_args(get_option('wpap_panel'), $default);
}

function wpap_dash_options(): array
{
    $default = [
        'dash_widgets' => ['coupons', 'tickets', 'orders', 'products', 'notices'],
    ];

    return wp_parse_args(get_option('wpap_dash'), $default);
}

function wpap_dash_box_options(): array
{
    $opt = get_option('wpap_dash_box');
    $defaults = require(WPAP_INC_PATH . 'default-dash-boxes.php');
    return is_array($opt) ? $opt : $defaults;
}

function wpap_ticket_options(): array
{
    $default = [
        'ticket_status' => ['name' => [], 'color' => [], 'text_color' => []],
        'ticket_attachment_size' => 1000,
        'tickets_per_page' => 8,
        'ticket_replies_per_page' => 8,
        'enable_ticket_sms' => false,
        'ticket_sms_text' => __('A ticket reply was sent to you on [site_name].', 'arvand-panel'),
        'new_ticket_sms_text' => __('New ticket was sent to you on [site_name].', 'arvand-panel'),
    ];

    return wp_parse_args(get_option('wpap_ticket'), $default);
}

function wpap_ticket_department_options(): array
{
    return wp_parse_args(get_option('wpap_ticket_department'), [
        'departments' => [0 => __('Admin', 'arvand-panel')],
    ]);
}

function wpap_wallet_options(): array
{
    return wp_parse_args(get_option('wpap_wallet_options'), [
        'enabled' => true,
        'min_amount' => 10000,
    ]);
}

function wpap_roles_options(): array
{
    $default = ['new_role' => 'subscriber'];
    return wp_parse_args(get_option('wpap_roles'), $default);
}

function wpap_sms_options(): array
{
    $default = ['provider' => 'melipayamak'];
    return wp_parse_args(get_option('wpap_sms'), $default);
}

function wpap_sms_provider_options($provider_name): array
{
    $default = [
        'username' => '',
        'password' => '',
        'from' => '',
        'pattern_code' => ''
    ];

    $provider['melipayamak'] = wp_parse_args(get_option('wpap_sms_melipayamak'), $default);
    $provider['farapayamak'] = wp_parse_args(get_option('wpap_sms_farapayamak'), $default);

    $default = [
        'api_key' => '',
        'secret_key' => '',
        'from' => '',
        'template_id' => '',
    ];

    $provider['sms_ir'] = wp_parse_args(get_option('wpap_sms_sms_ir'), $default);

    $default = [
        'username' => '',
        'password' => '',
        'from' => '',
        'pattern_code' => '',
    ];

    $provider['farazsms'] = wp_parse_args(get_option('wpap_sms_farazsms'), $default);

    $default = [
        'api_key' => '',
        'from' => '',
        'pattern_code' => '',
    ];

    $provider['kavenegar'] = wp_parse_args(get_option('wpap_sms_kavenegar'), $default);

    $default = ['api_key' => ''];
    $provider['parsgreen'] = wp_parse_args(get_option('wpap_sms_parsgreen'), $default);

    $default = [
        'username' => '',
        'password' => '',
        'from' => '',
        'pattern_code' => '',
    ];

    $provider['modirpayamak'] = wp_parse_args(get_option('wpap_sms_modirpayamak'), $default);

    $default = [
        'username' => '',
        'password' => '',
        'from' => '',
        'text' => "کاربر گرامی [site_name] از شما سپاسگذاریم.\nکد تایید: [verification_code]",
    ];

    $provider['raygansms'] = wp_parse_args(get_option('wpap_sms_raygansms'), $default);
    $provider['webone_sms'] = wp_parse_args(get_option('wpap_sms_webone_sms'), $default);

    return $provider[$provider_name];
}

function wpap_pages_options(): array
{
    $default = [
        'register_page_id' => false,
        'login_page_id' => false,
        'sms_register_login_page_id' => false,
        'after_register_page_id' => false,
        'after_login_page_id' => false,
        'after_sms_register_login_page_id' => false,
        'lost_pass_page_id' => false,
        'reset_pass_page_id' => false,
        'after_logout_page_id' => false,
        'panel_page_id' => false
    ];

    return wp_parse_args(get_option('wpap_plugin_pages'), $default);
}

function wpap_shortcode_options(): array
{
    $default = [
        'before_sidebar_nav' => '',
        'after_sidebar_nav' => '',
        'panel_header' => '',
        'after_logo' => '',
        'dash_top_shortcode' => '',
        'before_dash_info_boxes' => '',
        'after_dash_info_boxes' => '',
        'dash_bottom_shortcode' => ''
    ];

    return wp_parse_args(get_option('wpap_shortcode'), $default);
}

function wpap_email_options(): array
{
    $default_reg_email = '<p>' . __('New user with this email:', 'arvand-panel') . '</p>';
    $default_reg_email .= '<p>[user_email]</p>';
    $default_reg_email .= '<p>' . __('registered on your site.', 'arvand-panel') . '</p>';
    $default_reg_email .= '<p style="font-size: 12px"><a href="' . site_url() . '">' . get_bloginfo('name') . '</a></p>';
    $activation_email = '<p>' . __('Click on the account activation link below.', 'arvand-panel') . '</p>';
    $activation_email .= '<p>[activation_link]</p>';
    $admin_approval_email = '<p>' . __('Your user account has been approved by admin.', 'arvand-panel') . '</p>';
    $admin_approval_email .= '<p><a href="' . esc_url(site_url()) . '" style="font-size: 14px">' . get_bloginfo('name') . '</a></p>';

    $default = [
        'reg_email_subject' => __('New user registration', 'arvand-panel'),
        'reg_email_content' => $default_reg_email,
        'activation_email_subject' => __('Account Activation', 'arvand-panel'),
        'activation_email' => $activation_email,
        'admin_approval_email_subject' => __('Account Approval', 'arvand-panel'),
        'admin_approval_email' => $admin_approval_email,
        'enable_ticket_email' => true,
        'ticket_email_subject' => __('Ticket Reply', 'arvand-panel'),
        'ticket_email_content' => __('A ticket reply was sent to you on [site_name].', 'arvand-panel'),
        'new_ticket_email_subject' => __('New Ticket', 'arvand-panel'),
        'new_ticket_email_content' => __('New ticket was sent to you on [site_name].', 'arvand-panel')
    ];

    return wp_parse_args(get_option('wpap_email'), $default);
}

function wpap_styles(): array
{
    $default = [
        'panel_z_index' => 100,
        'panel_logo_width' => 0,
        'panel_logo_height' => 0,
        'panel_logo_align' => 'right',
    ];

    return wp_parse_args(get_option('wpap_styles'), $default);
}

function wpap_colors(): array
{
    $default = [
        'panel_color_1' => '#f2f8ff',
        'panel_color_2' => '#0062f5',
        'panel_bg_color_1' => '#ffffff',
        'panel_bg_color_2' => '#f7f7f7',
        'panel_text_color_1' => '#303030',
        'panel_text_color_2' => '#707070',
        'panel_text_color_3' => '#ffffff',
        'panel_border_color_1' => '#e6e6e6',
        'panel_border_color_2' => '#ffffff',

        'panel_dark_color_1' => '#2e303c',
        'panel_dark_color_2' => '#3e8dff',
        'panel_dark_bg_color_1' => '#282828',
        'panel_dark_bg_color_2' => '#202020',
        'panel_dark_text_color_1' => '#e5e5e5',
        'panel_dark_text_color_2' => '#a5a5a5',
        'panel_dark_text_color_3' => '#ffffff',
        'panel_dark_border_color_1' => '#424242',
        'panel_dark_border_color_2' => '#ffffff',

        'auth_color_1' => '#f2f8ff',
        'auth_color_2' => '#0062f5',
        'auth_bg_color_1' => '#ffffff',
        'auth_bg_color_2' => '#f7f7f7',
        'auth_text_color_1' => '#303030',
        'auth_text_color_2' => '#707070',
        'auth_text_color_3' => '#ffffff',
        'auth_border_color_1' => '#e6e6e6',
        'auth_border_color_2' => '#ffffff',

        'am_color_1' => '#f2f8ff',
        'am_color_2' => '#0062f5',
        'am_bg_color_1' => '#ffffff',
        'am_bg_color_2' => '#f7f7f7',
        'am_text_color_1' => '#303030',
        'am_text_color_2' => '#707070',
        'am_text_color_3' => '#ffffff',
        'am_border_color_1' => '#e6e6e6',
        'am_border_color_2' => '#ffffff',
    ];

    return wp_parse_args(get_option('wpap_colors'), $default);
}