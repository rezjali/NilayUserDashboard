<?php
defined('ABSPATH') || exit;

if (!class_exists('Woocommerce')) {
    return;
}

$completed_orders = wc_get_orders([
    'customer_id' => get_current_user_id(),
    'status' => 'completed',
    'limit' => -1
]);

$total_items_bought = 0;
foreach ($completed_orders as $order) {
    foreach ($order->get_items() as $item) {
        $total_items_bought += $item->get_quantity();
    }
}
?>

<div class="wpap-dash-info-box">
    <?php
    printf(
        '<i style="background-color: %s; color: %s;" class="%s"></i>',
        esc_attr($box['icon_bg']),
        esc_attr($box['icon_color']),
        esc_attr($box['icon'])
    );
    ?>

    <h3><?php esc_html_e($box['title']); ?></h3>

    <span>
        <?php printf(esc_html__('%d محصول', 'arvand-panel'), $total_items_bought); ?>
    </span>
</div>

