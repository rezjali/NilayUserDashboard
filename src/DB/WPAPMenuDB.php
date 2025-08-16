<?php

namespace Arvand\ArvandPanel\DB;

defined('ABSPATH') || exit;

class WPAPMenuDB
{
    private $db;
    private $table;

    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->table = $this->db->prefix . 'wpap_menus';
    }

    public function createTable(): void
    {
        $charset_collate = $this->db->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS `$this->table` (`menu_id` INT NOT NULL AUTO_INCREMENT, `menu_name` VARCHAR(50) NOT NULL UNIQUE, `menu_order` INT DEFAULT 0, `menu_icon` TEXT DEFAULT NULL, `menu_title` VARCHAR(50) NOT NULL, `menu_content` LONGTEXT DEFAULT NULL, `menu_access` TEXT NOT NULL, `menu_type` VARCHAR(20) NOT NULL, `menu_display` VARCHAR(10) NOT NULL, `menu_parent` INT DEFAULT 0, `menu_post_id` INT DEFAULT 0, PRIMARY KEY  (menu_id)) $charset_collate;";
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    public function exists($value, string $column = 'route', string $format = '%s'): bool
    {
        return (bool) $this->db->get_row(
            $this->db->prepare("SELECT `menu_id` FROM `$this->table` WHERE `$column` = $format", $value)
        );
    }

    public function insert(array $data): int
    {
        $res = $this->db->insert($this->table, $data, ['%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d']);
        return $res ? $this->db->insert_id : 0;
    }

    public function insertSubmenus(int $parent_id, array $menu_ids): void
    {
        $submenu_ids = implode(',', array_map('absint', $menu_ids));
        $not_in = empty($menu_ids) ? '' : "AND `menu_id` NOT IN($submenu_ids)";

        $this->db->query(
            $this->db->prepare("UPDATE `$this->table` SET `menu_parent` = 0 WHERE `menu_parent` = %d $not_in;", $parent_id)
        );

        $this->db->query(
            $this->db->prepare("UPDATE `$this->table` SET `menu_parent` = %d WHERE `menu_id` IN($submenu_ids);", $parent_id)
        );
    }

    public function getByID(int $id, array $fields = []): ?object
    {
        $fields = empty($fields) ? '*' : implode(',', $fields);
        return $this->db->get_row($this->db->prepare("SELECT $fields FROM `$this->table` WHERE `menu_id` = %d", $id));
    }

    public function getByName(string $name, array $fields = []): ?object
    {
        $fields = empty($fields) ? '*' : implode(',', $fields);
        return $this->db->get_row($this->db->prepare("SELECT $fields FROM `$this->table` WHERE `menu_name` = %s", $name));
    }

    public function getParents(): array
    {
        return $this->db->get_results("SELECT * FROM `$this->table` WHERE `menu_type` = 'parent' ORDER BY `menu_order` ASC");
    }

    public function getMenus(int $parent_id, bool $display_hide_menus = false): array
    {
        $display_where = '';

        if (!$display_hide_menus) {
            $display_where = "AND `menu_display` = 'show'";
        }

        return $this->db->get_results($this->db->prepare(
            "SELECT * FROM `$this->table` WHERE `menu_parent` = %d $display_where ORDER BY `menu_order` ASC",
            $parent_id
        ));
    }

    public function getNonParentMenus(int $parent_id = 0): array
    {
        return $this->db->get_results($this->db->prepare(
            "SELECT * FROM `$this->table` WHERE `menu_parent` IN(0, %d) AND `menu_id` != %d AND `menu_type` != 'parent' ORDER BY `menu_order`",
            $parent_id,
            $parent_id
        ));
    }

    public function getAccountMenus(array $names = [])
    {
        $names = implode("','", $names);
        $in = !empty($names) ? "AND `menu_name` IN ('$names')" : '';
        return $this->db->get_results("SELECT `menu_name`, `menu_icon`, `menu_title` FROM `$this->table` WHERE `menu_type` != 'parent' AND `menu_display` != 'hide' $in");
    }

    public function getDisplay($id): ?object
    {
        return $this->db->get_row(
            $this->db->prepare("SELECT `menu_display` FROM `$this->table` WHERE `menu_id` = %d;", $id)
        );
    }

    public function delete(int $id): bool
    {
        return (bool) $this->db->delete($this->table, ['menu_id' => $id], ['%d']);
    }
}
