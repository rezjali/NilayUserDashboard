<?php

namespace Arvand\ArvandPanel\Admin\Handlers;

defined('ABSPATH') || exit;

use Arvand\ArvandPanel\DB\WPAPMenuDB;
use Arvand\ArvandPanel\WPAPPluginActivation;

class WPAPMenuHandler
{
    public static function newMenu(): array
    {
        if (!isset($_POST['add_new_menu'])
            || !isset($_POST['new_menu_nonce'])
            || !wp_verify_nonce($_POST['new_menu_nonce'], 'new_menu')
        ) {
            return [];
        }

        if (empty($_POST['route'])) {
            return ['ok' => false, 'msg' => __('لطفاً نامک منو را وارد کنید.', 'arvand-panel')];
        }

        $menu_db = new WPAPMenuDB;

        if ($menu_db->exists($_POST['route'])) {
            return ['ok' => false, 'msg' => __('نامک منو از قبل موجود است.', 'arvand-panel')];
        }

        $icon = [
            'classes' => empty($_POST['menu_icon']) ? 'ri-menu-line' : sanitize_text_field($_POST['menu_icon']),
            'image_id' => isset($_POST['icon_image_id']) ? (int)$_POST['icon_image_id'] : -1,
            'color_type' => (isset($_POST['icon_color_type']) && 1 == $_POST['icon_color_type']) ? 1 : 0,
            'color' => isset($_POST['icon_color']) ? sanitize_hex_color($_POST['icon_color']) : '#ffffff'
        ];

        $menu_type = 'shortcode';
        $content = '';

        if (isset($_POST['menu_type']) && in_array($_POST['menu_type'], ['shortcode', 'text', 'link', 'page', 'parent'])) {
            $menu_type = $_POST['menu_type'];
            $content = $_POST['menu_content_' . $menu_type] ?? '';
        }

        $content_array = [
            'shortcode' => sanitize_textarea_field($content),
            'link' => sanitize_url($content),
            'text' => wp_kses_post($content),
            'html' => wp_kses_post($content),
        ];

        $parent_id = isset($_POST['menu_parent']) ? (int)$_POST['menu_parent'] : 0;

        if ('parent' === $menu_type || !$menu_db->exists($parent_id, 'menu_id', '%d')) {
            $parent_id = 0;
        }

        global $wpdb;
        $table = $wpdb->prefix . 'wpap_menus';

        $insert = $wpdb->insert(
            $table,
            [
                'menu_name' => str_replace(' ', '_', sanitize_title($_POST['route'])),
                'route' => str_replace(' ', '-', sanitize_title($_POST['route'])),
                'menu_icon' => json_encode($icon),
                'menu_title' => empty($_POST['menu_title']) ? __('Menu Title', 'arvand-panel') : sanitize_text_field($_POST['menu_title']),
                'menu_content' => $content_array[$menu_type] ?? '',
                'menu_access' => maybe_serialize($_POST['menu_access'] ?? []),
                'menu_type' => $menu_type,
                'menu_display' => (isset($_POST['menu_display']) && 'show' === $_POST['menu_display']) ? 'show' : 'hide',
                'menu_post_id' => isset($_POST['menu_post_id']) ? absint($_POST['menu_post_id']) : -1,
                'menu_parent' => $parent_id
            ],
            ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d']
        );

        $wpdb->update($table,
            ['menu_name' => "menu_$wpdb->insert_id"], ['menu_id' => $wpdb->insert_id], '%s', '%d'
        );

        if (false === $insert) {
            return ['ok' => false, 'msg' => __('متأسفانه مشکلی در ذخیره منو پیش آمده.', 'arvand-panel')];
        }

        if ('parent' === $menu_type && !empty($_POST['sub_menus'])) {
            $menu_db->insertSubmenus($wpdb->insert_id, (array)$_POST['sub_menus']);
        }

        update_option('wpap_menus_cache', '');

        return ['ok' => true, 'msg' => __('منو جدید با موفقیت ایجاد شد.', 'arvand-panel')];
    }

    public static function sortMenus(): array
    {
        if (
            !isset($_POST['menu_id'])
            || !isset($_POST['sort_menus_nonce'])
            || !wp_verify_nonce($_POST['sort_menus_nonce'], 'sort_menus')
        ) {
            return [];
        }

        $menu_ids = array_map('absint', $_POST['menu_id']);

        global $wpdb;

        foreach ($menu_ids as $order => $id) {
            $wpdb->update($wpdb->prefix . 'wpap_menus', ['menu_order' => $order], ['menu_id' => $id], '%d', '%d');
        }

        update_option('wpap_menus_cache', '');

        return ['ok' => true, 'msg' => __('مرتب سازی منوها با موفقیت انجام شد.', 'arvand-panel')];
    }

    public static function editMenu(int $menu_id): array
    {
        if (
            !isset($_POST['edit_menu'])
            || empty($_POST['edit_menu_nonce'])
            || !wp_verify_nonce($_POST['edit_menu_nonce'], 'edit_menu')
        ) {
            return [];
        }

        $menu_db = new WPAPMenuDB;

        $menu = $menu_db->getByID($menu_id);
        if (!$menu) {
            return [];
        }

        if (empty($_POST['route'])) {
            return ['ok' => false, 'msg' => __('لطفاً نامک منو را وارد کنید.', 'arvand-panel')];
        }

        if ($menu_db->exists($_POST['route']) && $_POST['route'] !== $menu->route) {
            return ['ok' => false, 'msg' => __('نامک منو از قبل موجود است.', 'arvand-panel')];
        }

        $menu_type = 'default';
        $content = '';
        $post_id = 0;

        if ('default' !== $menu->menu_type) {
            $menu_type = 'shortcode';

            if (isset($_POST['menu_type']) && in_array($_POST['menu_type'], ['shortcode', 'text', 'link', 'page', 'parent'])) {
                $menu_type = $_POST['menu_type'];
                $content = $_POST['menu_content_' . $menu_type] ?? '';
            }

            $content_array = [
                'shortcode' => sanitize_textarea_field($content),
                'link' => sanitize_url($content),
                'text' => wp_kses_post($content),
            ];

            $content = $content_array[$menu_type] ?? '';
            $post_id = isset($_POST['menu_post_id']) ? absint($_POST['menu_post_id']) : -1;
        }

        $icon = [
            'classes' => empty($_POST['menu_icon']) ? 'bx bx-menu' : sanitize_text_field($_POST['menu_icon']),
            'image_id' => isset($_POST['icon_image_id']) ? (int)$_POST['icon_image_id'] : -1,
            'color_type' => (isset($_POST['icon_color_type']) && 1 == $_POST['icon_color_type']) ? 1 : 0,
            'color' => isset($_POST['icon_color']) ? sanitize_hex_color($_POST['icon_color']) : 'inherit'
        ];

        $display = (isset($_POST['menu_display']) && 'show' === $_POST['menu_display']) ? 'show' : 'hide';
        $parent_id = isset($_POST['menu_parent']) ? (int)$_POST['menu_parent'] : 0;

        if ('parent' === $menu_type || !$menu_db->exists($parent_id, 'menu_id', '%d')) {
            $parent_id = 0;
        }

        global $wpdb;
        $table = $wpdb->prefix . 'wpap_menus';

        $update = $wpdb->update(
            $table,
            [
                'route' => str_replace(' ', '-', sanitize_title($_POST['route'])),
                'menu_icon' => json_encode($icon),
                'menu_title' => empty($_POST['menu_title']) ? __('Menu Title', 'arvand-panel') : sanitize_text_field($_POST['menu_title']),
                'menu_content' => $content,
                'menu_access' => maybe_serialize($_POST['menu_access'] ?? []),
                'menu_type' => $menu_type,
                'menu_display' => 'dash' === $menu->menu_name ? 'show' : $display,
                'menu_post_id' => $post_id,
                'menu_parent' => $parent_id
            ],
            ['menu_id' => $menu->menu_id],
            ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d'],
            '%d'
        );

        if (false === $update) {
            return ['ok' => false, 'msg' => __('متأسفانه مشکلی در ذخیره منو پیش آمده.', 'arvand-panel')];
        }

        if ('parent' === $menu_type) {
            $sub_menus = empty($_POST['sub_menus']) ? [] : (array)$_POST['sub_menus'];
            $menu_db->insertSubmenus((int)$menu->menu_id, $sub_menus);
        }

        if ('parent' === $menu->menu_type && 'parent' !== $menu_type) {
            $wpdb->update($table, ['menu_parent' => 0], ['menu_parent' => $menu->menu_id], null, '%d');
        }

        update_option('wpap_menus_cache', '');

        return ['ok' => true, 'msg' => __('منو با موفقیت ویرایش شد.', 'arvand-panel')];
    }

    public static function resetMenus(): array
    {
        if (
            !isset($_POST['reset_menus'])
            || !isset($_POST['reset_menus'])
            || !wp_verify_nonce(wp_unslash($_POST['reset_menus_nonce']), 'reset_menus')
        ) {
            return [];
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'wpap_menus';
        $wpdb->query("TRUNCATE TABLE $table_name");

        $activation = new WPAPPluginActivation;
        $activation->generateMenus();

        update_option('wpap_menus_cache', '');

        return ['ok' => true, 'msg' => __('منوی پنل کاربری به حالت پیشفرض بازگشت.', 'arvand-panel')];
    }
}
