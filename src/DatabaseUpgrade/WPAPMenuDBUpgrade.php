<?php

namespace Arvand\ArvandPanel\DatabaseUpgrade;

defined('ABSPATH') || exit;

use Arvand\ArvandPanel\DB\WPAPMenuDB;

class WPAPMenuDBUpgrade
{
    private $db;
    private $table;

    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->table = $wpdb->prefix . 'wpap_menus';
        $this->addRouteCol();
        $this->deleteOldWCMenus();
        $this->setRouteValueSameMenuNameValue();
        $this->generateNewWCMenus();
        $this->renameMenuNameValues();
        update_option('wpap_menus_cache', false);
    }

    private function addRouteCol(): void
    {
        $row = $this->db->get_results("SHOW COLUMNS FROM `$this->table` LIKE 'route'");
        if (empty($row)) {
            // Add the new column AFTER `menu_name`
            $this->db->query("ALTER TABLE `$this->table` ADD COLUMN `route` VARCHAR(191) NULL UNIQUE AFTER `menu_name`;");
        }
    }

    private function deleteOldWCMenus(): void
    {
        $this->db->query(
            "DELETE FROM `$this->table` WHERE menu_type != 'default' AND `menu_name` IN ('wc_orders', 'section-orders', 'wc_downloads', 'section-downloads', 'wc_addresses', 'section-address');"
        );
    }

    private function setRouteValueSameMenuNameValue(): void
    {
        $this->db->query("UPDATE `$this->table` SET route = REPLACE(menu_name, '_', '-') WHERE route IS NULL;");
    }

    private function generateNewWCMenus(): void
    {
        $role_names = wp_roles()->get_names();
        $access = maybe_serialize(array_keys($role_names));
        $icon = [
            'image_id' => -1,
            'color_type' => 0,
            'color' => ''
        ];

        $menuDB = new WPAPMenuDB;

        if (!$this->db->get_var("SELECT COUNT(*) FROM $this->table WHERE menu_name = 'wallet' AND menu_type = 'default';")) {
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
        }

        if (!$this->db->get_var("SELECT COUNT(*) FROM $this->table WHERE menu_name = 'wallet_topup' AND menu_type = 'default';")) {
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
        }

        if (!$this->db->get_var("SELECT COUNT(*) FROM $this->table WHERE menu_name = 'wallet_transactions' AND menu_type = 'default';")) {
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
        }

        if (!$this->db->get_var("SELECT COUNT(*) FROM $this->table WHERE menu_name = 'wc_orders' AND menu_type = 'default';")) {
            $menuDB->insert([
                'menu_name' => 'wc_orders',
                'route' => 'orders',
                'menu_order' => 16,
                'menu_icon' => json_encode(['classes' => 'bi bi-receipt-cutoff'] + $icon),
                'menu_title' => __('سفارش ها', 'arvand-panel'),
                'menu_content' => null,
                'menu_access' => $access,
                'menu_type' => 'default',
                'menu_display' => 'show',
                'menu_parent' => 0,
                'menu_post_id' => 0
            ]);
        }

        if (!$this->db->get_var("SELECT COUNT(*) FROM $this->table WHERE menu_name = 'wc_downloads' AND menu_type = 'default';")) {
            $menuDB->insert([
                'menu_name' => 'wc_downloads',
                'route' => 'downloads',
                'menu_order' => 17,
                'menu_icon' => json_encode(['classes' => 'bi bi-file-earmark-arrow-down'] + $icon),
                'menu_title' => __('دانلود ها', 'arvand-panel'),
                'menu_content' => null,
                'menu_access' => $access,
                'menu_type' => 'default',
                'menu_display' => 'show',
                'menu_parent' => 0,
                'menu_post_id' => 0
            ]);
        }

        if (!$this->db->get_var("SELECT COUNT(*) FROM $this->table WHERE menu_name = 'bookmarked' AND menu_type = 'default';")) {
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
        }

        if (!$this->db->get_var("SELECT COUNT(*) FROM $this->table WHERE menu_name = 'visited_products' AND menu_type = 'default';")) {
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
        }

        if (!$this->db->get_var("SELECT COUNT(*) FROM $this->table WHERE menu_name = 'wc_addresses' AND menu_type = 'default';")) {
            $menuDB->insert([
                'menu_name' => 'wc_addresses',
                'route' => 'addresses',
                'menu_order' => 10,
                'menu_icon' => json_encode(['classes' => 'bi bi-geo-alt'] + $icon),
                'menu_title' => __('آدرس ها', 'arvand-panel'),
                'menu_content' => null,
                'menu_access' => $access,
                'menu_type' => 'default',
                'menu_display' => 'show',
                'menu_parent' => 0,
                'menu_post_id' => 0
            ]);
        }

        if (!$this->db->get_var("SELECT COUNT(*) FROM $this->table WHERE menu_name = 'wc_edit_account' AND menu_type = 'default';")) {
            $menuDB->insert([
                'menu_name' => 'wc_edit_account',
                'route' => 'edit-account',
                'menu_order' => 21,
                'menu_icon' => json_encode(['classes' => 'bi bi-person'] + $icon),
                'menu_title' => __('ویرایش حساب', 'arvand-panel'),
                'menu_content' => null,
                'menu_access' => $access,
                'menu_type' => 'default',
                'menu_display' => 'show',
                'menu_parent' => 0,
                'menu_post_id' => 0
            ]);
        }
    }

    private function renameMenuNameValues(): void
    {
        $menus = $this->db->get_results("SELECT menu_id, menu_name, menu_type FROM `$this->table`");
        foreach ($menus as $menu) {
            if ($menu->menu_type === 'default') {
                $name = sanitize_text_field(str_replace('-', '_', $menu->menu_name));
            } else {
                $name = 'menu_' . $menu->menu_id;
            }

            if ($menu->menu_name === $name) continue;

            $this->db->update($this->table, ['menu_name' => $name], ['menu_id' => $menu->menu_id], ['%s'], ['%d']);
        }
    }
}