<?php
defined('ABSPATH') || exit;

if (!in_array('products', $dash['dash_widgets'])) {
    return;
}

global $current_user;

$orders = wc_get_orders([
    'customer' => $current_user->ID,
    'status' => 'completed',
    'limit' => 20,
]);

$product_ids = [];
foreach ($orders as $order) {
    foreach ($order->get_items() as $item) {
        $product_ids[] = $item->get_product_id();
    }
}

$products = empty($product_ids) ? [] : wc_get_products([
    'post_type' => 'product',
    'include' => $product_ids,
    'limit' => 5,
    'meta_key' => 'total_sales',
    'orderby' => 'meta_value_num',
    'order' => 'DESC',
    'post_status' => 'publish',
    'ignore_sticky_posts' => true,
]);
?>

<div id="wpap-dash-products" class="wpap-list">
    <header>
        <h2><?php esc_html_e('خریدهای پرتکرار', 'arvand-panel'); ?></h2>

        <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="wpap-list-show-all"
           target="_blank">
            <?php esc_html_e('مشاهده محصولات', 'arvand-panel'); ?>
        </a>
    </header>

    <div class="wpap-list-wrap">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <?php
                /**
                 * @var $product WC_Product
                 */
                $is_visible = $product && $product->is_visible();
                $product_permalink = $is_visible ? $product->get_permalink() : '';
                ?>

                <div class="wpap-list-item">
                    <div class="wpap-dash-products-image">
                        <?php echo wp_kses_post($product->get_image('post_thumbnail')); ?>
                    </div>

                    <div class="wpap-list-item-title">
                        <?php
                        echo $product_permalink ? sprintf(
                            '<a href="%s" target="_blank"><h2>%s</h2></a><div class="wpap-dash-products-price">%s</div>',
                            esc_url($product_permalink),
                            esc_html($product->get_name()),
                            $product->get_price_html()
                        ) : sprintf('<h2>%s</h2><div class="wpap-dash-products-price">%s</div>',
                            esc_html($product->get_name()),
                            $product->get_price_html()
                        );
                        ?>
                    </div>

                    <?php if ($product_permalink): ?>
                        <a href="<?php echo esc_url($product_permalink); ?>" class="wpap-list-item-view wpap-btn-1" target="_blank">
                            <?php esc_html_e('خرید مجدد', 'arvand-panel'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="wpap-list-item">
                <?php esc_html_e('محصولی وجود ندارد.', 'arvand-panel'); ?>
            </div>
        <?php endif; ?>
    </div>
</div>
