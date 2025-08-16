<?php
defined('ABSPATH') || exit;

if (!class_exists('Woocommerce')) {
    return;
}

$total_spent = wc_get_customer_total_spent(get_current_user_id());
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
        <?php echo wc_price($total_spent); ?>
    </span>
</div>