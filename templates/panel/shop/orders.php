<?php
defined( 'ABSPATH' ) || exit;

if (!class_exists('woocommerce')) {
    wpap_print_notice(__('افزونه ووکامرس فعال نیست.', 'arvand-panel'), 'info', false);
    return;
}

if (!empty($id)) {
    $order = wc_get_order(absint($id));

    if (!$order || !current_user_can('view_order', $order->get_id())) {
        wpap_print_notice(__('سفارش معتبر نیست.', 'arvand-panel'), 'error');
        return;
    }

    woocommerce_account_view_order($order->get_id());
} else {
    woocommerce_account_orders(absint($_GET['orders-page'] ?? 1));
}