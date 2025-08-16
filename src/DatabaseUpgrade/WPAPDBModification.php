<?php

namespace Arvand\ArvandPanel\DatabaseUpgrade;

defined('ABSPATH') || exit;

class WPAPDBModification
{
    public static function tempRenameCols(bool $old = false): void
    {
        global $wpdb;
        $table = $wpdb->prefix . 'wpap_menus';

        if ($old) {
            // old cols
            $wpdb->query("ALTER TABLE $table CHANGE `id` `menu_id` INT NOT NULL AUTO_INCREMENT;");
            $wpdb->query("ALTER TABLE $table CHANGE `slug` `menu_name` VARCHAR(50) NOT NULL UNIQUE;");
            $wpdb->query("ALTER TABLE $table CHANGE `order` `menu_order` INT NOT NULL");
            $wpdb->query("ALTER TABLE $table CHANGE `icon` `menu_icon` TEXT NOT NULL;");
            $wpdb->query("ALTER TABLE $table CHANGE `title` `menu_title` VARCHAR(50) NOT NULL;");
            $wpdb->query("ALTER TABLE $table CHANGE `content` `menu_content` TEXT NOT NULL;");
            $wpdb->query("ALTER TABLE $table CHANGE `access` `menu_access` TEXT NOT NULL;");
            $wpdb->query("ALTER TABLE $table CHANGE `type` `menu_type` VARCHAR(20) NOT NULL;");
            $wpdb->query("ALTER TABLE $table CHANGE `display` `menu_display` VARCHAR(10) NOT NULL;");
            $wpdb->query("ALTER TABLE $table CHANGE `parent` `menu_parent` INT DEFAULT 0;");
            $wpdb->query("ALTER TABLE $table CHANGE `post_id` `menu_post_id` INT DEFAULT 0;");

            $wpdb->update($table, ['menu_display' => 'show'], ['menu_display' => 1]);
            $wpdb->update($table, ['menu_display' => 'hide'], ['menu_display' => 0]);
        } else {
            $wpdb->update($table, ['menu_display' => 1], ['menu_display' => 'show']);
            $wpdb->update($table, ['menu_display' => 0], ['menu_display' => 'hide']);

            // new cols
            $wpdb->query("ALTER TABLE $table CHANGE `menu_id` `id` INT NOT NULL AUTO_INCREMENT;");
            $wpdb->query("ALTER TABLE $table CHANGE `menu_name` `slug` VARCHAR(50) NOT NULL UNIQUE;");
            $wpdb->query("ALTER TABLE $table CHANGE `menu_icon` `icon` TEXT DEFAULT NULL;");
            $wpdb->query("ALTER TABLE $table CHANGE `menu_order` `order` INT DEFAULT 0");
            $wpdb->query("ALTER TABLE $table CHANGE `menu_title` `title` VARCHAR(50) NOT NULL;");
            $wpdb->query("ALTER TABLE $table CHANGE `menu_content` `content` LONGTEXT DEFAULT NULL;");
            $wpdb->query("ALTER TABLE $table CHANGE `menu_access` `access` TEXT NOT NULL;");
            $wpdb->query("ALTER TABLE $table CHANGE `menu_type` `type` VARCHAR(20) NOT NULL;");
            $wpdb->query("ALTER TABLE $table CHANGE `menu_display` `display` TINYINT(1) NOT NULL DEFAULT 1;");
            $wpdb->query("ALTER TABLE $table CHANGE `menu_parent` `parent` INT DEFAULT 0;");
            $wpdb->query("ALTER TABLE $table CHANGE `menu_post_id` `post_id` INT DEFAULT 0;");
        }
    }

    public static function tempChangeDefaultMenusContent(): void
    {
        global $wpdb;
        $table = $wpdb->prefix . 'wpap_menus';
        $wpdb->update($table, ['content' => null], ['slug' => 'dash', 'type' => 'default']);
        $wpdb->update($table, ['content' => null], ['slug' => 'home', 'type' => 'default']);
        $wpdb->update($table, ['content' => null], ['slug' => 'user-edit', 'type' => 'default']);
        $wpdb->update($table, ['content' => null], ['slug' => 'change-email', 'type' => 'default']);
        $wpdb->update($table, ['content' => null], ['slug' => 'change-password', 'type' => 'default']);
        $wpdb->update($table, ['content' => null], ['slug' => 'mobile', 'type' => 'default']);
        $wpdb->update($table, ['content' => null], ['slug' => 'dash', 'type' => 'default']);
        $wpdb->update($table, ['content' => null], ['slug' => 'notifications', 'type' => 'default']);
        $wpdb->update($table, ['content' => null], ['slug' => 'comments', 'type' => 'default']);
        $wpdb->update($table, ['content' => null], ['slug' => 'new-ticket', 'type' => 'default']);
        $wpdb->update($table, ['content' => null], ['slug' => 'tickets', 'type' => 'default']);
        $wpdb->update($table, ['content' => null], ['slug' => 'private-msg', 'type' => 'default']);
        $wpdb->update($table, ['content' => null], ['slug' => 'logout', 'type' => 'default']);
    }
}