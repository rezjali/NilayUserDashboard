<?php
defined( 'ABSPATH' ) || exit;

if (!class_exists('woocommerce')) {
    wpap_print_notice(
        __('Woocommerce plugin is not active.', 'arvand-panel'),
        'info',
        false,
    );

    return;
}

wc_print_notices();

$type = '';
if (!empty($address_type) && in_array($address_type, ['billing', 'shipping'])) {
    $type = sanitize_key($address_type);
}

woocommerce_account_edit_address($type);