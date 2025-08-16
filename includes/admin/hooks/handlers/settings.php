<?php
defined('ABSPATH') || exit;

use Arvand\ArvandPanel\WPAPUser;

add_action('wp_ajax_wpap_general', function () {
    if (!isset($_POST['general_nonce']) || !wp_verify_nonce($_POST['general_nonce'], 'general_nonce')) {
        wp_send_json('error');
    }

    if (isset($_POST['admin_bar_access'])) {
        $general_data['admin_bar_access'] = WPAPUser::accessPrepare($_POST['admin_bar_access']);
    }

    if (isset($_POST['admin_area_access'])) {
        $general_data['admin_area_access'] = WPAPUser::accessPrepare($_POST['admin_area_access']);
    }

    $general_data['add_to_list'] = isset($_POST['add_to_list']);
    $general_data['add_to_list_btn_display'] = isset($_POST['add_to_list_btn_display']);
    $general_data['delete_plugin_data'] = isset($_POST['delete_plugin_data']);

    $general_data['private_msg_attachment_size'] = (
        is_numeric($_POST['private_msg_attachment_size']) && $_POST['private_msg_attachment_size'] >= 100)
        ? intval($_POST['private_msg_attachment_size'])
        : 1000;

    update_option('wpap_general', $general_data);
    wp_send_json('success');
});

add_action('wp_ajax_wpap_register', function () {
    if (!isset($_POST['register_nonce']) || !wp_verify_nonce($_POST['register_nonce'], 'register_nonce')) {
        wp_send_json('error');
    }

    $register_data['enable_def_reg'] = isset($_POST['enable_def_reg']);
    $register_data['default_register'] = isset($_POST['default_register']);
    $register_data['pass_strength'] = isset($_POST['pass_strength']);
    $register_data['register_activation'] = isset($_POST['register_activation']);
    $register_data['sms_reg_password'] = isset($_POST['sms_reg_password']);
    $register_data['enable_admin_approval'] = isset($_POST['enable_admin_approval']);
    $register_data['enable_agree'] = isset($_POST['enable_agree']);
    $register_data['agree_required'] = isset($_POST['agree_required']);
    $register_data['agree_text'] = wp_kses($_POST['agree_text'], 'post');
    update_option('wpap_register', $register_data);
    wp_send_json('success');
});

add_action('wp_ajax_wpap_login', function () {
    if (!isset($_POST['login_nonce']) || !wp_verify_nonce($_POST['login_nonce'], 'login_nonce')) {
        wp_send_json('error');
    }

    $login_data['enable_def_login'] = isset($_POST['enable_def_login']);
    $login_data['default_login'] = isset($_POST['default_login']);
    $login_data['enable_sms_register_login'] = isset($_POST['enable_sms_register_login']);
    $login_data['force_to_add_mobile'] = isset($_POST['force_to_add_mobile']);

    $login_data['reset_pass_method'] = in_array($_POST['reset_pass_method'], ['both', 'mobile', 'email'])
        ? sanitize_text_field($_POST['reset_pass_method'])
        : 'both';

    update_option('wpap_login', $login_data);
    wp_send_json('success');
});

add_action('wp_ajax_wpap_google', function () {
    if (!isset($_POST['google_nonce']) || !wp_verify_nonce($_POST['google_nonce'], 'google')) {
        wp_send_json('error');
    }

    $login_data['enable_recaptcha'] = isset($_POST['enable_recaptcha']);
    $login_data['recaptcha_site_key'] = sanitize_text_field($_POST['recaptcha_site_key']);
    $login_data['recaptcha_secret_key'] = sanitize_text_field($_POST['recaptcha_secret_key']);
    update_option('wpap_google_options', $login_data);
    wp_send_json('success');
});

add_action('wp_ajax_wpap_account_menu', function () {
    if (!isset($_POST['account_menu_nonce']) || !wp_verify_nonce($_POST['account_menu_nonce'], 'account_menu_nonce')) {
        wp_send_json('error');
    }

    $login_data['non_logged_in_btn'] = sanitize_text_field($_POST['non_logged_in_btn']);
    $login_data['logged_in_btn'] = sanitize_text_field($_POST['logged_in_btn']);
    $login_data['logged_in_btn_icon'] = sanitize_text_field($_POST['logged_in_btn_icon']);
    $login_data['logged_in_btn_text'] = sanitize_text_field($_POST['logged_in_btn_text']);
    $login_data['menus'] = array_map('sanitize_text_field', (array)$_POST['menus']);
    update_option('wpap_account_menu', $login_data);
    wp_send_json('success');
});

add_action('wp_ajax_wpap_panel', function () {
    if (!isset($_POST['panel_nonce']) || !wp_verify_nonce($_POST['panel_nonce'], 'panel_nonce')) {
        wp_send_json('error');
    }

    $panel_data['fullscreen_compatibility'] = isset($_POST['fullscreen_compatibility']);
    $panel_data['display_top_sidebar'] = isset($_POST['display_top_sidebar']);
    $panel_data['display_sidebar_links'] = isset($_POST['display_sidebar_links']);
    $panel_data['logo_url'] = sanitize_text_field($_POST['logo_url']);
    $panel_data['upload_avatar'] = isset($_POST['enable_upload_avatar']);

    $panel_data['avatar_size'] = (is_numeric($_POST['avatar_size']) and $_POST['avatar_size'] >= 100)
        ? intval($_POST['avatar_size'])
        : 100;

    $panel_data['notifications_per_page'] = intval($_POST['notifications_per_page']);
    $panel_data['comments_per_page'] = intval($_POST['comments_per_page']);

    update_option('wpap_panel', $panel_data);
    wp_send_json('success');
});

add_action('wp_ajax_wpap_dashboard', function () {
    if (!isset($_POST['dash_nonce']) || !wp_verify_nonce($_POST['dash_nonce'], 'dash_nonce')) {
        wp_send_json('error');
    }

    $dash_data = [
        'dash_widgets' => array_map(
            'sanitize_text_field',
            empty($_POST['dash_widgets']) ? [] : $_POST['dash_widgets']
        ),
    ];

    update_option('wpap_dash', $dash_data);
    wp_send_json('success');
});

add_action('wp_ajax_wpap_dash_boxes', function () {
    if (empty($_POST['boxes'])
        || empty($_POST['dash_box_nonce'])
        || !wp_verify_nonce($_POST['dash_box_nonce'], 'dash_box_nonce')
    ) {
        wp_send_json('error');
    }

    $allowed_names = [
        'notifications',
        'comments',
        'private_msg',
        'user_registration_date',
        'completed_orders',
        'wallet_amount',
        'total_spent',
        'total_items_bought',
        'custom_box',
    ];

    $boxes = [];
    foreach ($_POST['boxes'] as $box) {
        if (!in_array($box['name'] ?? '', $allowed_names)) {
            continue;
        }

        $is_default = in_array($box['name'], $allowed_names) && $box['name'] !== 'custom_box';

        $boxes[] = [
            'name' => sanitize_text_field($box['name']),
            'icon' => empty($box['icon']) ? 'ri-star-line' : sanitize_text_field($box['icon']),
            'icon_color' => empty($box['icon_color']) ? '#ffffff' : sanitize_text_field($box['icon_color']),
            'icon_bg' => empty($box['icon_bg']) ? '#0078ff' : sanitize_text_field($box['icon_bg']),
            'title' => empty($box['icon_bg']) ? __('عنوان', 'arvand-panel') : sanitize_text_field($box['title']),

            'content' => $is_default
                ? __('محتوای ویژۀ افزونه', 'arvand-panel')
                : stripslashes(sanitize_text_field($box['content'] ?? '')),

            'link' => $is_default ? '' : sanitize_url($box['link'] ?? ''),
            'box_type' => $is_default ? 'default' : 'custom',
            'content_type' => $box['content_type'] === 'text' ? 'text' : 'shortcode',
            'display' => $box['display'] === 'show' ? 'show' : 'hide',
        ];
    }

    update_option('wpap_dash_box', $boxes);
    wp_send_json('success');
});

add_action('wp_ajax_wpap_ticket', function () {
    if (!isset($_POST['ticket_nonce']) || !wp_verify_nonce($_POST['ticket_nonce'], 'ticket_nonce')) {
        wp_send_json('error');
    }

    $ticket_attachment_size = intval($_POST['ticket_attachment_size']) >= 100 ? $_POST['ticket_attachment_size'] : 1000;

    if (isset($_POST['ticket_status_name'], $_POST['ticket_status_color'], $_POST['ticket_status_text_color'])) {
        $ticket_data['ticket_status'] = [
            'name' => wpap_sanitize_array_fields($_POST['ticket_status_name'], false),
            'color' => wpap_sanitize_array_fields($_POST['ticket_status_color'], false),
            'text_color' => wpap_sanitize_array_fields($_POST['ticket_status_text_color'], false),
        ];
    }

    $ticket_data['ticket_attachment_size'] = $ticket_attachment_size;
    $ticket_data['tickets_per_page'] = intval($_POST['tickets_per_page']);
    $ticket_data['ticket_replies_per_page'] = intval($_POST['ticket_replies_per_page']);
    $ticket_data['enable_ticket_sms'] = isset($_POST['enable_ticket_sms']);
    $ticket_data['ticket_sms_text'] = sanitize_textarea_field($_POST['ticket_sms_text']);
    $ticket_data['new_ticket_sms_text'] = sanitize_textarea_field($_POST['new_ticket_sms_text']);
    update_option('wpap_ticket', $ticket_data);
    wp_send_json('success');
});

add_action('wp_ajax_wpap_ticket_department', function () {
    if (!isset($_POST['ticket_department_nonce']) || !wp_verify_nonce($_POST['ticket_department_nonce'], 'ticket_department_nonce')) {
        wp_send_json('error');
    }

    $deps = [];

    foreach ($_POST['departments'] as $dep) {
        if (!empty($dep)) {
            $deps[] = sanitize_text_field($dep);
        }
    }

    $ticket_data['departments'] = empty($deps) ? [__('Admin', 'arvand-panel')] : $deps;
    $update = update_option('wpap_ticket_department', $ticket_data);

    if ($update) {
        $users = get_users(['meta_key' => 'wpap_user_ticket_department']);

        foreach ($users as $user) {
            $user_dep = unserialize(get_user_meta($user->ID, 'wpap_user_ticket_department', true));
            $new_user_department = array_intersect($user_dep, $ticket_data['departments']);
            update_user_meta($user->ID, 'wpap_user_ticket_department', maybe_serialize($new_user_department));
        }

        $posts = get_posts([
            'post_type' => 'wpap_ticket',
            'meta_key' => 'wpap_ticket_department',
            'numberposts' => -1,
        ]);

        foreach ($posts as $post) {
            $ticket_department = get_post_meta($post->ID, 'wpap_ticket_department', true);

            if (!in_array($ticket_department, $ticket_data['departments'])) {
                delete_post_meta($post->ID, 'wpap_ticket_department');
            }
        }
    }

    wp_send_json('success');
});

add_action('wp_ajax_wpap_wallet', function () {
    if (!isset($_POST['wallet_nonce']) || !wp_verify_nonce(wp_unslash($_POST['wallet_nonce']), 'wallet')) {
        wp_send_json('error');
    }

    update_option('wpap_wallet_options', [
        'enabled' => isset($_POST['enabled']),
        'min_amount' => floatval($_POST['min_amount'] ?? 10000),
    ]);

    wp_send_json('success');
});

add_action('wp_ajax_wpap_add_supporter', function () {
    if (!isset($_POST['ticket_department_nonce']) || !wp_verify_nonce($_POST['ticket_department_nonce'], 'ticket_department_nonce')) {
        wp_send_json('error');
    }

    if (empty($_POST['responsible'])) {
        wp_send_json(['status' => 'error', 'msg' => __('Username must not be empty.', 'arvand-panel')]);
    }

    if (!username_exists($_POST['responsible'])) {
        wp_send_json(['status' => 'error', 'msg' => __('This user does not exist.', 'arvand-panel')]);
    }

    $user_login = sanitize_text_field($_POST['responsible']);

    if (!isset($_POST['responsible_for'])) {
        wp_send_json(['status' => 'error', 'msg' => __('Choose at least one department.', 'arvand-panel')]);
    }

    $user = get_user_by('login', $user_login);
    $td = wpap_ticket_department_options();
    $departments = array_intersect($_POST['responsible_for'], $td['departments']);
    update_user_meta($user->ID, 'wpap_user_ticket_department', maybe_serialize($departments));
    wp_send_json('success');
});

add_action('wp_ajax_wpap_edit_supporter', function () {
    if (!isset($_POST['ticket_department_nonce']) || !wp_verify_nonce($_POST['ticket_department_nonce'], 'ticket_department_nonce')) {
        wp_send_json('error');
    }

    $user_login = sanitize_user($_POST['responsible']);

    if (username_exists($user_login) and isset($_POST['responsible_for'])) {
        $user = get_user_by('login', $user_login);
        $td = wpap_ticket_department_options();
        $departments = array_intersect($_POST['responsible_for'], $td['departments']);
        update_user_meta($user->ID, 'wpap_user_ticket_department', maybe_serialize($departments));
        wp_send_json('success');
    }

    wp_send_json('error');
});

add_action('wp_ajax_wpap_shortcode', function () {
    if (!isset($_POST['shortcode_nonce']) or !wp_verify_nonce($_POST['shortcode_nonce'], 'shortcode_nonce')) {
        wp_send_json('error');
    }

    $dash_data = [
        'before_sidebar_nav' => stripslashes(wp_kses($_POST['before_sidebar_nav'], 'post')),
        'after_sidebar_nav' => stripslashes(wp_kses($_POST['after_sidebar_nav'], 'post')),
        'panel_header' => stripslashes(wp_kses($_POST['panel_header'], 'post')),
        'after_logo' => stripslashes(wp_kses($_POST['after_logo'], 'post')),
        'dash_top_shortcode' => stripslashes(wp_kses($_POST['dash_top_shortcode'], 'post')),
        'before_dash_info_boxes' => stripslashes(wp_kses($_POST['before_dash_info_boxes'], 'post')),
        'after_dash_info_boxes' => stripslashes(wp_kses($_POST['after_dash_info_boxes'], 'post')),
        'dash_bottom_shortcode' => stripslashes(wp_kses($_POST['dash_bottom_shortcode'], 'post'))
    ];

    update_option('wpap_shortcode', $dash_data);
    wp_send_json('success');
});

add_action('wp_ajax_wpap_email', function () {
    if (!isset($_POST['email_nonce']) || !wp_verify_nonce($_POST['email_nonce'], 'email_nonce')) {
        wp_send_json('error');
    }

    $data = [
        'reg_email_subject' => sanitize_text_field($_POST['reg_email_subject']),
        'reg_email_content' => wp_kses($_POST['reg_email_content'], 'post'),
        'activation_email_subject' => sanitize_text_field($_POST['activation_email_subject']),
        'activation_email' => wp_kses($_POST['activation_email'], 'post'),
        'admin_approval_email_subject' => sanitize_text_field($_POST['admin_approval_email_subject']),
        'admin_approval_email' => wp_kses($_POST['admin_approval_email'], 'post'),
        'enable_ticket_email' => isset($_POST['enable_ticket_email']),
        'ticket_email_subject' => wp_strip_all_tags($_POST['ticket_email_subject']),
        'ticket_email_content' => wp_kses($_POST['ticket_email_content'], 'post'),
        'new_ticket_email_subject' => wp_strip_all_tags($_POST['new_ticket_email_subject']),
        'new_ticket_email_content' => wp_kses($_POST['new_ticket_email_content'], 'post')
    ];

    update_option('wpap_email', $data);
    wp_send_json('success');
});

add_action('wp_ajax_wpap_pages', function () {
    if (!isset($_POST['pages_nonce']) || !wp_verify_nonce($_POST['pages_nonce'], 'pages_nonce')) {
        wp_send_json('error');
    }

    $data = [
        'register_page_id' => intval($_POST['register_page'] ?? -1),
        'login_page_id' => intval($_POST['login_page'] ?? -1),
        'sms_register_login_page_id' => intval($_POST['sms_register_login_page'] ?? -1),
        'after_register_page_id' => intval($_POST['after_register_page'] ?? -1),
        'after_login_page_id' => intval($_POST['after_login'] ?? -1),
        'after_sms_register_login_page_id' => intval($_POST['after_sms_register_login_page'] ?? -1),
        'lost_pass_page_id' => intval($_POST['lost_pass_page'] ?? -1),
        'reset_pass_page_id' => intval($_POST['reset_pass_page'] ?? -1),
        'after_logout_page_id' => intval($_POST['after_logout'] ?? -1),
        'panel_page_id' => intval($_POST['panel_page'] ?? -1)
    ];

    update_option('wpap_plugin_pages', $data);
    wp_send_json('success');
});

add_action('wp_ajax_wpap_styles', function () {
    if (!isset($_POST['styles_nonce']) && !wp_verify_nonce('styles_nonce', 'styles_nonce')) {
        wp_send_json('error');
    }

    $data = [
        'panel_z_index' => absint($_POST['panel_z_index'] ?? 100),
        'panel_logo_width' => absint($_POST['panel_logo_width'] ?? 0),
        'panel_logo_height' => absint($_POST['panel_logo_height'] ?? 0),
        'panel_logo_align' => in_array($_POST['panel_logo_align'] ?? 'right', ['right', 'center', 'left'])
            ? $_POST['panel_logo_align']
            : 'right',
    ];

    update_option('wpap_styles', $data);
    wp_send_json('success');
});

add_action('wp_ajax_wpap_colors', function () {
    if (!isset($_POST['colors_nonce']) && !wp_verify_nonce('colors_nonce', 'colors')) {
        wp_send_json('error');
    }

    $colors = [
        'color_1' => '#f2f8ff',
        'color_2' => '#0062f5',
        'bg_color_1' => '#ffffff',
        'bg_color_2' => '#f7f7f7',
        'text_color_1' => '#303030',
        'text_color_2' => '#707070',
        'text_color_3' => '#ffffff',
        'border_color_1' => '#e6e6e6',
        'border_color_2' => '#ffffff',
    ];

    $data = [];

    foreach ($colors as $name => $value) {
        $data["panel_$name"] = sanitize_hex_color($_POST["panel_$name"] ?? $value);
    }
    foreach ($colors as $name => $value) {
        $data["panel_dark_$name"] = sanitize_hex_color($_POST["panel_dark_$name"] ?? $value);
    }
    foreach ($colors as $name => $value) {
        $data["auth_$name"] = sanitize_hex_color($_POST["auth_$name"] ?? $value);
    }
    foreach ($colors as $name => $value) {
        $data["am_$name"] = sanitize_hex_color($_POST["am_$name"] ?? $value);
    }

    update_option('wpap_colors', $data);
    wp_send_json('success');
});

add_action('wp_ajax_wpap_roles', function () {
    if (!isset($_POST['roles_nonce']) || !wp_verify_nonce($_POST['roles_nonce'], 'roles_nonce')) {
        wp_send_json('error');
    }

    $data = [];
    $replace = str_replace(' ', '_', $_POST['new_role']);
    $role = 'arvand_panel_' . strtolower(sanitize_text_field($replace));
    $roles = get_editable_roles();

    if (in_array($_POST['new_role_after_delete'], array_keys($roles))) {
        $data['new_role'] = sanitize_text_field($_POST['new_role_after_delete']);
    }

    $update = update_option('wpap_roles', $data);

    if (!empty($_POST['new_role']) && !empty($_POST['role_display_name'])) {
        $add = add_role($role, sanitize_text_field($_POST['new_role']));
    }

    if ((isset($update) && $update) || (isset($add) && $add)) {
        wp_send_json([
            'status' => 'success',
            'role_name' => sanitize_text_field($_POST['new_role']),
            'role' => 'arvand_panel_' . sanitize_text_field($_POST['new_role']),
            'role_nonce' => wp_create_nonce('del_role'),
        ]);
    }

    wp_send_json('error');
});

add_action('wp_ajax_wpap_delete_role', function () {
    if (empty($_POST['role']) || empty($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'del_role')) {
        wp_send_json('error');
    }

    if ('arvand_panel' === substr($_POST['role'], 0, 12)) {
        $role = sanitize_text_field($_POST['role']);
        $new_role = sanitize_text_field($_POST['new_role']);

        $users = get_users(['role' => $role]);

        foreach ($users as $user) {
            $roles = new WP_User($user->ID);
            $roles->set_role($new_role);
        }

        remove_role($role);
        wp_send_json('success');
    }

    wp_send_json('error');
});