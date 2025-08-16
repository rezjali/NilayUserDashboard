<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!apply_filters('woocommerce_order_item_visible', true, $item)) {
    return;
}

$is_visible = $product && $product->is_visible();

$product_permalink = apply_filters(
    'woocommerce_order_item_permalink',
    $is_visible ? $product->get_permalink($item) : '',
    $item,
    $order
);
?>

<div class="wpap-list-item <?php echo esc_attr(apply_filters('woocommerce_order_item_class', 'woocommerce-table__line-item order_item', $item, $order)); ?>">
    <div class="wpap-product-image">
        <?php
        echo wp_kses_post(
            $product_permalink
                ? sprintf('<a href="%s" target="_blank">%s</a>', $product_permalink, $product->get_image('post_thumbnail'))
                : ($product ? $product->get_image('post_thumbnail') : wc_placeholder_img()),
        );
        ?>
    </div>

    <div class="wpap-product-name">
        <?php
        echo wp_kses_post(
            apply_filters('woocommerce_order_item_name',
                $product_permalink
                    ? sprintf('<a href="%s">%s</a>', $product_permalink, $item->get_name())
                    : $item->get_name(),
                $item,
                $is_visible
            )
        );

        $qty = $item->get_quantity();
        $refunded_qty = $order->get_qty_refunded_for_item($item_id);

        if ($refunded_qty) {
            $qty_display = '<del>' . esc_html($qty) . '</del> <ins>' . esc_html($qty - ($refunded_qty * -1)) . '</ins>';
        } else {
            $qty_display = esc_html($qty);
        }

        echo apply_filters(
            'woocommerce_order_item_quantity_html',
            ' <strong class="wpap-product-quantity">' . sprintf('&times;&nbsp;%s', $qty_display) . '</strong>',
            $item
        );

        do_action('woocommerce_order_item_meta_start', $item_id, $item, $order, false);

        wc_display_item_meta($item);

        do_action('woocommerce_order_item_meta_end', $item_id, $item, $order, false);
        ?>

        <div class="wpap-product-total">
            <?php echo $order->get_formatted_line_subtotal($item); ?>
        </div>
    </div>

    <?php if ($product_permalink): ?>
        <a class="wpap-view-product wpap-btn-1" href="<?php echo esc_url($product_permalink); ?>" target="_blank">
            <?php esc_html_e('مشاهده محصول', 'arvand-panel'); ?>
        </a>
    <?php endif; ?>
</div>

<?php if ($show_purchase_note && $purchase_note) : ?>
    <div class="wpap-list-item wpap-product-purchase-note">
        <?php echo wpautop(do_shortcode(wp_kses_post($purchase_note))); ?>
    </div>
<?php endif; ?>
