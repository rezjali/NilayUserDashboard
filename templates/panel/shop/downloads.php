<?php
defined( 'ABSPATH' ) || exit;

if (!class_exists('woocommerce')) {
    wpap_print_notice(__('Woocommerce plugin is not active.', 'arvand-panel'), 'info', false);
} else {
    woocommerce_account_downloads();
}