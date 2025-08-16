<?php
defined('ABSPATH') || exit;

if (!class_exists('Woocommerce')) {
    return;
}

$completed_orders = wc_get_orders([
    'customer_id' => get_current_user_id(),
    'status' => 'completed',
    'limit' => -1,
    'return' => 'ids'
]);
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
        <?php printf(esc_html__('%d سفارش', 'arvand-panel'), count($completed_orders)); ?>
    </span>
</div>

