<?php

namespace Arvand\ArvandPanel;

defined('ABSPATH') || exit;

use Arvand\ArvandPanel\DB\WPAPMenuDB;
use Arvand\ArvandPanel\Form\WPAPFieldSettings;

class WPAPPluginActivation
{
    public function activation(): void
    {
        if (!current_user_can('activate_plugins')) {
            return;
        }

        (new WPAPMenuDB)->createTable();
        $this->createSMSVerificationTable();

        if (!get_option('wpap_plugin_is_installed')) {
            $this->generateMenus();
            $this->generatePages();
            $this->generateRequiredRegisterFields();

            add_option('wpap_plugin_is_installed', 'yes');
        }
    }

    private function createSMSVerificationTable(): void
    {
        global $wpdb;
        $table = $wpdb->prefix . 'wpap_sms_verification';
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS $table (id INT NOT NULL AUTO_INCREMENT, user_id INT NOT NULL, mobile VARCHAR(12) NOT NULL, code INT NOT NULL, status VARCHAR(20) NOT NULL, PRIMARY KEY  (id)) $charset_collate;";
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    private function generatePages(): void
    {
        global $wpdb;
        $data = wpap_pages_options();

        if (!$wpdb->get_row("SELECT `post_name` FROM `$wpdb->posts` WHERE `post_name` = 'wpap-panel'")) {
            $page = array(
                'post_title' => __('پنل کاربری', 'arvand-panel'),
                'post_name' => 'wpap-panel',
                'post_content' => '[wpap_user_panel]',
                'post_status' => 'publish',
                'post_author' => get_current_user_id(),
                'post_type' => 'page',
            );

            $insert = wp_insert_post($page);
            $data['panel_page_id'] = $insert;
            $data['after_register_page_id'] = $insert;
            $data['after_login_page_id'] = $insert;
            $data['after_sms_register_login_page_id'] = $insert;
        }

        if (!$wpdb->get_row("SELECT `post_name` FROM `$wpdb->posts` WHERE `post_name` = 'wpap-register'")) {
            $page = array(
                'post_title' => __('ثبت نام', 'arvand-panel'),
                'post_name' => 'wpap-register',
                'post_content' => '[wpap_register_form]',
                'post_status' => 'publish',
                'post_author' => get_current_user_id(),
                'post_type' => 'page',
            );

            $data['register_page_id'] = wp_insert_post($page);
        }

        if (!$wpdb->get_row("SELECT `post_name` FROM `$wpdb->posts` WHERE `post_name` = 'wpap-login'")) {
            $page = array(
                'post_title' => __('ورود', 'arvand-panel'),
                'post_name' => 'wpap-login',
                'post_content' => '[wpap_login_form]',
                'post_status' => 'publish',
                'post_author' => get_current_user_id(),
                'post_type' => 'page',
            );

            $insert = wp_insert_post($page);
            $data['login_page_id'] = $insert;
            $data['after_logout_page_id'] = $insert;
        }

        if (!$wpdb->get_row("SELECT `post_name` FROM `$wpdb->posts` WHERE `post_name` = 'wpap-register-login'")) {
            $page = array(
                'post_title' => __('ورود با شماره همراه', 'arvand-panel'),
                'post_name' => 'wpap-register-login',
                'post_content' => '[wpap_sms_register_login]',
                'post_status' => 'publish',
                'post_author' => get_current_user_id(),
                'post_type' => 'page',
            );

            $insert = wp_insert_post($page);
            $data['sms_register_login_page_id'] = $insert;
        }

        if (!$wpdb->get_row("SELECT `post_name` FROM `$wpdb->posts` WHERE `post_name` = 'wpap-lost-password'")) {
            $page = array(
                'post_title' => __('فراموشی رمز', 'arvand-panel'),
                'post_name' => 'wpap-lost-password',
                'post_content' => '[wpap_lost_password_form]',
                'post_status' => 'publish',
                'post_author' => get_current_user_id(),
                'post_type' => 'page',
            );

            $data['lost_pass_page_id'] = wp_insert_post($page);
        }

        if (!$wpdb->get_row("SELECT `post_name` FROM `$wpdb->posts` WHERE `post_name` = 'wpap-reset-password'")) {
            $page = array(
                'post_title' => __('رمز جدید', 'arvand-panel'),
                'post_name' => 'wpap-reset-password',
                'post_content' => '[wpap_reset_password_form]',
                'post_status' => 'publish',
                'post_author' => get_current_user_id(),
                'post_type' => 'page',
            );

            $data['reset_pass_page_id'] = wp_insert_post($page);
        }

        update_option('wpap_plugin_pages', $data);
    }

    public function generateRequiredRegisterFields(): void
    {
        $fields = get_option('wpap_register_fields');
        $fields = $fields ? json_decode($fields, true) : [];
        $id = get_option('wpap_register_fields_last_id') ?: 0;

        foreach (['user_login', 'user_email', 'user_pass'] as $field_name) {
            if (!WPAPFieldSettings::get($field_name)) {
                $field_class = 'Arvand\ArvandPanel\Form\Fields\wpap_field_' . $field_name;
                $settings = call_user_func([$field_class, 'defaultSettings']);
                $settings['id'] = ++$id;
                $fields[$field_name] = $settings;
            }
        }

        update_option('wpap_register_fields', json_encode($fields));
        update_option('wpap_register_fields_last_id', $id);
    }

    public function generateMenus(): void
    {
        $menuDB = new WPAPMenuDB;
        $role_names = wp_roles()->get_names();
        $access = maybe_serialize(array_keys($role_names));

        $icon = [
            'image_id' => -1,
            'color_type' => 0,
            'color' => ''
        ];

        $menuDB->insert([
            'menu_name' => 'dash',
            'route' => 'dash',
            'menu_order' => 0,
            'menu_icon' => json_encode(['classes' => 'ri-dashboard-line'] + $icon),
            'menu_title' => __('Dashboard', 'arvand-panel'),
            'menu_content' => null,
            'menu_access' => $access,
            'menu_type' => 'default',
            'menu_display' => 'show',
            'menu_parent' => 0,
            'menu_post_id' => 0
        ]);

        $menuDB->insert([
            'menu_name' => 'home',
            'route' => 'home',
            'menu_order' => 1,
            'menu_icon' => json_encode(['classes' => 'ri-home-9-line'] + $icon),
            'menu_title' => __('Home', 'arvand-panel'),
            'menu_content' => null,
            'menu_access' => $access,
            'menu_type' => 'default',
            'menu_display' => 'hide',
            'menu_parent' => 0,
            'menu_post_id' => 0
        ]);

        $menu_id_settings = $menuDB->insert([
            'menu_name' => 'settings',
            'route' => 'settings',
            'menu_order' => 2,
            'menu_icon' => json_encode(['classes' => 'ri-settings-line'] + $icon),
            'menu_title' => __('تنظیمات', 'arvand-panel'),
            'menu_content' => null,
            'menu_access' => $access,
            'menu_type' => 'parent',
            'menu_display' => 'show',
            'menu_parent' => 0,
            'menu_post_id' => 0
        ]);

        $menuDB->insert([
            'menu_name' => 'user_edit',
            'route' => 'user-edit',
            'menu_order' => 3,
            'menu_icon' => json_encode(['classes' => 'ri-user-settings-line'] + $icon),
            'menu_title' => __('Edit Profile', 'arvand-panel'),
            'menu_content' => null,
            'menu_access' => $access,
            'menu_type' => 'default',
            'menu_display' => 'show',
            'menu_parent' => $menu_id_settings,
            'menu_post_id' => 0
        ]);

        $menuDB->insert([
            'menu_name' => 'change_email',
            'route' => 'change-email',
            'menu_order' => 4,
            'menu_icon' => json_encode(['classes' => 'ri-mail-settings-line'] + $icon),
            'menu_title' => __('ثبت/تغییر ایمیل', 'arvand-panel'),
            'menu_content' => null,
            'menu_access' => $access,
            'menu_type' => 'default',
            'menu_display' => 'show',
            'menu_parent' => $menu_id_settings,
            'menu_post_id' => 0
        ]);

        $menuDB->insert([
            'menu_name' => 'change_password',
            'route' => 'change-password',
            'menu_order' => 5,
            'menu_icon' => json_encode(['classes' => 'ri-key-2-line'] + $icon),
            'menu_title' => __('Change Password', 'arvand-panel'),
            'menu_content' => null,
            'menu_access' => $access,
            'menu_type' => 'default',
            'menu_display' => 'show',
            'menu_parent' => $menu_id_settings,
            'menu_post_id' => 0
        ]);

        $menuDB->insert([
            'menu_name' => 'mobile',
            'route' => 'mobile',
            'menu_order' => 6,
            'menu_icon' => json_encode(['classes' => 'ri-smartphone-line'] + $icon),
            'menu_title' => __('ثبت/تغییر موبایل', 'arvand-panel'),
            'menu_content' => null,
            'menu_access' => $access,
            'menu_type' => 'default',
            'menu_display' => 'show',
            'menu_parent' => $menu_id_settings,
            'menu_post_id' => 0
        ]);

        $menuDB->insert([
            'menu_name' => 'notifications',
            'route' => 'notifications',
            'menu_order' => 7,
            'menu_icon' => json_encode(['classes' => 'ri-notification-3-line'] + $icon),
            'menu_title' => __('Notifications', 'arvand-panel'),
            'menu_content' => null,
            'menu_access' => $access,
            'menu_type' => 'default',
            'menu_display' => 'show',
            'menu_parent' => 0,
            'menu_post_id' => 0
        ]);

        $menu_id_support = $menuDB->insert([
            'menu_name' => 'support',
            'route' => 'support',
            'menu_order' => 8,
            'menu_icon' => json_encode(['classes' => 'ri-customer-service-2-line'] + $icon),
            'menu_title' => __('پشتیبانی', 'arvand-panel'),
            'menu_content' => null,
            'menu_access' => $access,
            'menu_type' => 'parent',
            'menu_display' => 'show',
            'menu_parent' => 0,
            'menu_post_id' => 0
        ]);

        $menuDB->insert([
            'menu_name' => 'new_ticket',
            'route' => 'new-ticket',
            'menu_order' => 9,
            'menu_icon' => json_encode(['classes' => 'ri-coupon-line'] + $icon),
            'menu_title' => __('New Ticket', 'arvand-panel'),
            'menu_content' => null,
            'menu_access' => $access,
            'menu_type' => 'default',
            'menu_display' => 'show',
            'menu_parent' => $menu_id_support,
            'menu_post_id' => 0
        ]);

        $menuDB->insert([
            'menu_name' => 'tickets',
            'route' => 'tickets',
            'menu_order' => 10,
            'menu_icon' => json_encode(['classes' => 'ri-question-answer-line'] + $icon),
            'menu_title' => __('Tickets', 'arvand-panel'),
            'menu_content' => null,
            'menu_access' => $access,
            'menu_type' => 'default',
            'menu_display' => 'show',
            'menu_parent' => $menu_id_support,
            'menu_post_id' => 0
        ]);

        $menuDB->insert([
            'menu_name' => 'comments',
            'route' => 'comments',
            'menu_order' => 11,
            'menu_icon' => json_encode(['classes' => 'ri-chat-3-line'] + $icon),
            'menu_title' => __('Comments', 'arvand-panel'),
            'menu_content' => null,
            'menu_access' => $access,
            'menu_type' => 'default',
            'menu_display' => 'show',
            'menu_parent' => $menu_id_support,
            'menu_post_id' => 0
        ]);

        $menuDB->insert([
            'menu_name' => 'private_msg',
            'route' => 'private-msg',
            'menu_order' => 12,
            'menu_icon' => json_encode(['classes' => 'ri-mail-forbid-line'] + $icon),
            'menu_title' => __('Private Message', 'arvand-panel'),
            'menu_content' => null,
            'menu_access' => $access,
            'menu_type' => 'default',
            'menu_display' => 'show',
            'menu_parent' => 0,
            'menu_post_id' => 0
        ]);

        $menu_id_wallet = $menuDB->insert([
            'menu_name' => 'wallet',
            'route' => 'wallet',
            'menu_order' => 13,
            'menu_icon' => json_encode(['classes' => 'ri-wallet-line'] + $icon),
            'menu_title' => __('کیف پول', 'arvand-panel'),
            'menu_content' => null,
            'menu_access' => $access,
            'menu_type' => 'parent',
            'menu_display' => 'show',
            'menu_parent' => 0,
            'menu_post_id' => 0
        ]);

        $menuDB->insert([
            'menu_name' => 'wallet_topup',
            'route' => 'wallet-top-up',
            'menu_order' => 14,
            'menu_icon' => json_encode(['classes' => 'ri-bank-card-line'] + $icon),
            'menu_title' => __('شارژ کیف پول', 'arvand-panel'),
            'menu_content' => null,
            'menu_access' => $access,
            'menu_type' => 'default',
            'menu_display' => 'show',
            'menu_parent' => $menu_id_wallet,
            'menu_post_id' => 0
        ]);

        $menuDB->insert([
            'menu_name' => 'wallet_transactions',
            'route' => 'wallet-transactions',
            'menu_order' => 15,
            'menu_icon' => json_encode(['classes' => 'ri-history-line'] + $icon),
            'menu_title' => __('تراکنش ها', 'arvand-panel'),
            'menu_content' => null,
            'menu_access' => $access,
            'menu_type' => 'default',
            'menu_display' => 'show',
            'menu_parent' => $menu_id_wallet,
            'menu_post_id' => 0
        ]);

        $menuDB->insert([
            'menu_name' => 'wc_orders',
            'route' => 'orders',
            'menu_order' => 16,
            'menu_icon' => json_encode(['classes' => 'ri-file-list-3-line'] + $icon),
            'menu_title' => __('سفارش ها', 'arvand-panel'),
            'menu_content' => null,
            'menu_access' => $access,
            'menu_type' => 'default',
            'menu_display' => 'show',
            'menu_parent' => 0,
            'menu_post_id' => 0
        ]);

        $menuDB->insert([
            'menu_name' => 'wc_downloads',
            'route' => 'downloads',
            'menu_order' => 17,
            'menu_icon' => json_encode(['classes' => 'ri-download-2-line'] + $icon),
            'menu_title' => __('دانلود ها', 'arvand-panel'),
            'menu_content' => null,
            'menu_access' => $access,
            'menu_type' => 'default',
            'menu_display' => 'show',
            'menu_parent' => 0,
            'menu_post_id' => 0
        ]);

        $menuDB->insert([
            'menu_name' => 'bookmarked',
            'route' => 'bookmarked',
            'menu_order' => 18,
            'menu_icon' => json_encode(['classes' => 'ri-heart-line'] + $icon),
            'menu_title' => __('لیست علاقه مندی', 'arvand-panel'),
            'menu_content' => null,
            'menu_access' => $access,
            'menu_type' => 'default',
            'menu_display' => 'show',
            'menu_parent' => 0,
            'menu_post_id' => 0
        ]);

        $menuDB->insert([
            'menu_name' => 'visited_products',
            'route' => 'visited-products',
            'menu_order' => 19,
            'menu_icon' => json_encode(['classes' => 'ri-eye-2-line'] + $icon),
            'menu_title' => __('بازدیدهای اخیر', 'arvand-panel'),
            'menu_content' => null,
            'menu_access' => $access,
            'menu_type' => 'default',
            'menu_display' => 'show',
            'menu_parent' => 0,
            'menu_post_id' => 0
        ]);

        $menuDB->insert([
            'menu_name' => 'wc_addresses',
            'route' => 'addresses',
            'menu_order' => 20,
            'menu_icon' => json_encode(['classes' => 'ri-map-pin-line'] + $icon),
            'menu_title' => __('آدرس ها', 'arvand-panel'),
            'menu_content' => null,
            'menu_access' => $access,
            'menu_type' => 'default',
            'menu_display' => 'show',
            'menu_parent' => 0,
            'menu_post_id' => 0
        ]);

        $menuDB->insert([
            'menu_name' => 'wc_edit_account',
            'route' => 'edit-account',
            'menu_order' => 21,
            'menu_icon' => json_encode(['classes' => 'ri-account-circle-2-line'] + $icon),
            'menu_title' => __('ویرایش حساب', 'arvand-panel'),
            'menu_content' => null,
            'menu_access' => $access,
            'menu_type' => 'default',
            'menu_display' => 'show',
            'menu_parent' => 0,
            'menu_post_id' => 0
        ]);

        $menuDB->insert([
            'menu_name' => 'logout',
            'route' => 'logout',
            'menu_order' => 22,
            'menu_icon' => json_encode(['classes' => 'ri-logout-circle-line'] + $icon),
            'menu_title' => __('Logout', 'arvand-panel'),
            'menu_content' => null,
            'menu_access' => $access,
            'menu_type' => 'default',
            'menu_display' => 'hide',
            'menu_parent' => 0,
            'menu_post_id' => 0
        ]);

        delete_option('wpap_menus_cache');
    }
}