<?php
defined('ABSPATH') || exit;

// Handle wallet top-up by creating a Woocommerce order
add_action('init', function () {
    if (!class_exists('Woocommerce')
        || !is_user_logged_in()
        || !isset($_POST['wpap_wallet_topup'])
    ) {
        return;
    }

    if (wpap_is_demo()) {
        wp_redirect(add_query_arg('error', 'demo', wp_get_referer()));
        exit;
    }

    $amount = floatval($_POST['wpap_wallet_amount']);
    if ($amount <= 0) {
        wp_redirect(add_query_arg('error', 'empty_amount', wp_get_referer()));
        exit;
    }

    if ($amount < floatval(wpap_wallet_options()['min_amount'])) {
        wp_redirect(add_query_arg('error', 'min_amount', wp_get_referer()));
        exit;
    }

    $user_id = get_current_user_id();
    $order = wc_create_order(['customer_id' => $user_id]);
    $order->set_created_via('wpap_wallet_topup');
    $order->set_status('pending');
    $order->add_order_note(__('شارژ کیف پول - در انتظار پرداخت', 'arvand-panel'));
    $order->update_meta_data('wpap_wallet_topup', 'yes');
    $order->update_meta_data('wpap_wallet_amount', $amount);

    $product = new WC_Product();
    $product->set_name(__('شارژ کیف پول', 'arvand-panel'));
    $product->set_price($amount);
    $product->set_regular_price($amount);
    $product->set_virtual(true);
    $product->set_manage_stock(false);

    $order->add_product($product);

    $order->set_total($amount);
    $order->save();

    wp_redirect($order->get_checkout_payment_url());
    exit;
});

add_action('wp_ajax_wpap_add_to_list', function () {
    if (wpap_is_demo()) {
        wp_send_json_error();
    }

    $product_id = intval($_POST['product_id']);
    $user_id = get_current_user_id();
    $list = get_user_meta($user_id, 'wpap_bookmarked', true);
    $list = is_array($list) ? $list : [];

    if (in_array($product_id, $list)) {
        $list = array_diff($list, [$product_id]);
        $status = 'removed';
    } else {
        $list[] = $product_id;
        $status = 'added';
    }

    update_user_meta($user_id, 'wpap_bookmarked', array_values($list));
    wp_send_json(['status' => $status]);
});