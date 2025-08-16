<?php
defined('ABSPATH') || exit;

if (!class_exists('woocommerce')) {
    wpap_print_notice(__('Woocommerce plugin is not active.', 'arvand-panel'), 'info', false);
    return;
}

$user_id = get_current_user_id();
$visited_ids = get_user_meta($user_id, 'wpap_visited', true);
$visited_ids = is_array($visited_ids) ? $visited_ids : [];

if (isset($_GET['remove-visited'])
    && isset($_GET['remove-visited-nonce'])
    && wp_verify_nonce(wp_unslash($_GET['remove-visited-nonce']), 'remove_visited')
    && in_array($_GET['remove-visited'], $visited_ids)
) {
    if (wpap_is_demo()) {
        wpap_print_notice(__('کاربر دمو (demo) قادر به تغییر نیست.', 'arvand-panel'),
            'error',
            true,
            '0 0 30px 0'
        );
    } else {
        $visited_ids = array_values(array_diff($visited_ids, [$_GET['remove-visited']]));
        update_user_meta($user_id, 'wpap_visited', $visited_ids);
    }
}

$products = [];
$products_count = 0;

if (!empty($visited_ids)) {
    $limit = 20;

    $products = wc_get_products([
        'include' => $visited_ids,
        'limit' => $limit,
        'status' => 'publish',
    ]);

    // Sort products to match original $visited_ids order
    usort($products, function($a, $b) use ($visited_ids) {
        return array_search($b->get_id(), $visited_ids) - array_search($a->get_id(), $visited_ids);
    });
}
?>

<div id="wpap-visited-products">
    <h2 class="wpap-section-title wpap-mb-30">
        <?php esc_html_e('بازدیدهای اخیر', 'arvand-panel'); ?>
    </h2>

    <?php if (!empty($products)): ?>
        <div id="wpap-visited-products-wrap">
            <?php foreach ($products as $product): ?>
                <?php
                /**
                 * @var WC_Product $product
                 */
                ?>

                <article class="wpap-product">
                    <div class="wpap-product-image">
                        <a href="<?php echo esc_url($product->get_permalink()); ?>" target="_blank">
                            <?php echo wp_kses_post($product->get_image()); ?>
                        </a>
                    </div>

                    <div class="wpap-product-content">
                        <?php echo esc_html($product->get_name()); ?>
                    </div>

                    <footer>
                        <div class="wpap-product-price">
                            <?php echo wp_kses_post($product->get_price_html()); ?>
                        </div>

                        <a href="<?php echo esc_url(add_query_arg(['remove-visited-nonce' => wp_create_nonce('remove_visited'), 'remove-visited' => $product->get_id()])); ?>">
                            <i class="ri-delete-bin-7-line"></i>
                        </a>
                    </footer>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="wpap-notfound">
            <p><?php esc_html_e('موردی یافت نشد.', 'arvand-panel'); ?></p>
        </div>
    <?php endif; ?>
</div>