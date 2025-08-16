<?php
defined('ABSPATH') || exit;

if (!class_exists('Woocommerce') || !wpap_is_valid_section('wallet_topup')) {
    return;
}
?>

<a href="<?php echo esc_url(wpap_get_page_url_by_name('wallet_topup')); ?>">
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
            <?php echo wc_price(wpap_wallet_get_balance(get_current_user_id())); ?>
        </span>
    </div>
</a>