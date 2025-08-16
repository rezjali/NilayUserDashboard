<?php
/**
 * Plugin Name: Arvand Panel
 * Plugin URI: https://www.zhaket.com/store/web/arvand-wp
 * Description: A user account area plugin for wordpress cms.
 * Author: اروند توسعه
 * Author URI: https://www.zhaket.com/store/web/arvand-wp
 * Version: 6.0.0
 * Text Domain: arvand-panel
 * Domain Path: /languages
 * Requires at least: 6.3
 * Requires PHP: 7.4
 */

defined('ABSPATH') || die;

// paths
define('WPAP_DIR_PATH', plugin_dir_path(__FILE__));
define('WPAP_INC_PATH', plugin_dir_path(__FILE__) . 'includes/');
define('WPAP_SRC_PATH', plugin_dir_path(__FILE__) . 'src/');
define('WPAP_ADMIN_TEMPLATES_PATH', plugin_dir_path(__FILE__) . 'templates/admin/');
define('WPAP_TEMPLATES_PATH', plugin_dir_path(__FILE__) . 'templates/');

// urls
define('WPAP_DIR_URL', plugin_dir_url(__FILE__));
define('WPAP_ASSETS_URL', plugin_dir_url(__FILE__) . 'assets/');

const WPAP_DEMO = 'demo';

require WPAP_DIR_PATH . 'vendor/autoload.php';

// --------- TEMP - DB UPGRADE --------- //
add_action('init', function () {
    new \Arvand\ArvandPanel\DatabaseUpgrade\WPAPMenuDBUpgrade;
});
// --------- TEMP - DB UPGRADE --------- //