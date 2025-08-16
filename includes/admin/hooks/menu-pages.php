<?php
defined('ABSPATH') || exit;

use Arvand\ArvandPanel\Admin\Handlers\WPAPMenuHandler;
use Arvand\ArvandPanel\DB\WPAPMenuDB;
use Arvand\ArvandPanel\WPAPMessage;
use Arvand\ArvandPanel\WPAPTicket;

add_action('admin_menu', function () {
    add_menu_page(
        __('Arvand Panel', 'arvand-panel'),
        __('Arvand Panel', 'arvand-panel'),
        'manage_options',
        'arvand-panel',
        false,
        WPAP_ASSETS_URL . 'admin/images/icon.png'
    );

    add_submenu_page(
        'arvand-panel',
        __('Settings', 'arvand-panel'),
        __('Settings', 'arvand-panel'),
        'manage_options',
        'arvand-panel',
        function () {
            require WPAP_ADMIN_TEMPLATES_PATH . 'settings/index.php';
        }
    );

    add_submenu_page(
        'arvand-panel',
        __('Notifications', 'arvand-panel'),
        __('Notifications', 'arvand-panel'),
        'manage_options',
        'edit.php?post_type=wpap_notifications',
        false
    );

    add_submenu_page(
        'arvand-panel',
        __('Tickets', 'arvand-panel'),
        __('Tickets', 'arvand-panel'),
        'manage_options',
        'wpap-tickets',
        'wpap_admin_menu_ticket'
    );

    add_submenu_page(
        'arvand-panel',
        __('Register Fields', 'arvand-panel'),
        __('Register Fields', 'arvand-panel'),
        'manage_options',
        'wpap-register-fields',
        function () {
            require WPAP_ADMIN_TEMPLATES_PATH . 'form-builder.php';
        }
    );

    add_submenu_page(
        'arvand-panel',
        __('Panel menus', 'arvand-panel'),
        __('Panel menus', 'arvand-panel'),
        'manage_options',
        'wpap-panel-menus',
        'wpap_admin_menu_panel_menus'
    );

    $unread_message_count = WPAPMessage::adminUnseenCount();
    $unread_message_count = $unread_message_count
        ? '<span class="awaiting-mod" style="margin: 0 5px">' . esc_html($unread_message_count) . '</span>'
        : '';

    add_submenu_page(
        'arvand-panel',
        __('Private Message', 'arvand-panel'),
        __('Private Message', 'arvand-panel') . $unread_message_count,
        'manage_options',
        'wpap-private-message',
        'wpap_admin_menu_private_message'
    );

    add_submenu_page(
        'arvand-panel',
        __('Plugin Info', 'arvand-panel'),
        __('Plugin Info', 'arvand-panel'),
        'manage_options',
        'wpap-info',
        function () {
            require WPAP_ADMIN_TEMPLATES_PATH . 'info.php';
        }
    );
});

function wpap_admin_menu_ticket(): void
{
    $remove_args = ['ticket', 'page-num'];
    $section = isset($_GET['section']) ? sanitize_text_field($_GET['section']) : '';
    $ticket = isset($_GET['ticket']) ? absint($_GET['ticket']) : null;
    $post = ($ticket && $post = get_post($ticket)) ? $post : null;
    $is_valid_post = ($post && 'wpap_ticket' === $post->post_type);
    $file = 'list';

    if ($section === 'new') {
        $file = 'new';
    }

    if ('single' === $section && $is_valid_post) {
        $user_dep = WPAPTicket::userDepartment(get_current_user_id());
        $department = get_post_meta($post->ID, 'wpap_ticket_department', 1);
        $file = 'single';

        if (!empty($user_dep) && !in_array($department, $user_dep)) {
            $file = 'list';
        }
    }

    if ('edit' === $section && $is_valid_post) {
        $file = 'edit';
    }
    ?>
    <div class="wpap-wrap wrap">
        <?php require WPAP_ADMIN_TEMPLATES_PATH . 'ticket/' . $file . '.php'; ?>
    </div>
    <?php
}

function wpap_admin_menu_panel_menus(): void
{
    $section = empty($_GET['section']) ? 'list' : sanitize_title($_GET['section']);
    $section = in_array($section, ['new', 'edit', 'list']) ? $section : 'list';
    $menu_db = new WPAPMenuDB;

    if ('edit' === $section) {
        if (isset($_GET['menu'])) {
            $menu_id = (int)$_GET['menu'];
            $response = WPAPMenuHandler::editMenu($menu_id);

            if (!$menu = $menu_db->getByID($menu_id)) {
                $section = 'list';
            }
        } else {
            $section = 'list';
        }
    }

    require WPAP_ADMIN_TEMPLATES_PATH . 'menu/index.php';
}

function wpap_admin_menu_private_message(): void
{
    $sections = ['new', 'user-messages', 'single', 'edit'];
    $remove_args = ['page-num', 'del-all-msg', 'del-all-user-msg', 'del-msg', 'del-msg-nonce'];
    $file = 'messages';

    if (isset($_GET['section']) && in_array($_GET['section'], $sections)) {
        $section = sanitize_text_field($_GET['section']);

        if ('new' === $section) {
            $file = 'new';
        }

        if ('user-messages' === $section) {
            $file = 'user-messages';
        }

        if (in_array($section, ['single', 'edit']) && !empty($_GET['msg'])) {
            $post = get_post((int)$_GET['msg']);

            if ($post && 'wpap_private_message' === $post->post_type) {
                $recipient = get_post_meta($post->ID, 'wpap_private_msg_recipient', 1);
                $user = get_user_by('id', $recipient);
                $file = $user ? $section : 'messages';
            }
        }
    }
    ?>
    <div id="wpap-private-message" class="wpap-wrap wrap">
        <?php require WPAP_ADMIN_TEMPLATES_PATH . "message/$file.php"; ?>
    </div>
    <?php
}