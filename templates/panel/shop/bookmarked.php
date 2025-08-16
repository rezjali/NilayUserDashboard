<?php
defined('ABSPATH') || exit;

if (!class_exists('woocommerce')) {
    wpap_print_notice(__('Woocommerce plugin is not active.', 'arvand-panel'), 'info', false);
    return;
}

$user_id = get_current_user_id();
$list_ids = get_user_meta($user_id, 'wpap_bookmarked', true);
$list_ids = is_array($list_ids) ? $list_ids : [];

if (isset($_GET['remove-bookmarked'])
    && isset($_GET['remove-bookmarked-nonce'])
    && wp_verify_nonce(wp_unslash($_GET['remove-bookmarked-nonce']), 'remove_bookmarked')
    && in_array($_GET['remove-bookmarked'], $list_ids)
) {
    if (wpap_is_demo()) {
        wpap_print_notice(__('کاربر دمو (demo) قادر به تغییر نیست.', 'arvand-panel'),
            'error',
            true,
            '0 0 30px 0'
        );
    } else {
        $list_ids = array_values(array_diff($list_ids, [$_GET['remove-bookmarked']]));
        update_user_meta($user_id, 'wpap_bookmarked', $list_ids);
    }
}

$limit = 20;
$products = [];
$products_count = 0;
$total_pages = 0;

if (!empty($list_ids)) {
    $page = absint($_GET['panel-page'] ?? 1);

    $products = wc_get_products([
        'include' => $list_ids,
        'limit' => $limit,
        'status' => 'publish',
        'paged' => $page,
    ]);

    $products_count = count(wc_get_products([
        'include' => $list_ids,
        'limit' => -1,
        'status' => 'publish',
        'return' => 'ids'
    ]));

    $total_pages = ceil($products_count / $limit);

    // Sort products to match original $visited_ids order
    usort($products, function($a, $b) use ($list_ids) {
        return array_search($b->get_id(), $list_ids) - array_search($a->get_id(), $list_ids);
    });
}
?>

<div id="wpap-bookmarked">
    <h2 class="wpap-section-title wpap-mb-30">
        <?php esc_html_e('لیست علاقه مندی ها', 'arvand-panel'); ?>
    </h2>

    <?php if (!empty($products)): ?>
        <div id="wpap-bookmarked-wrap">
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

                        <a href="<?php echo esc_url(add_query_arg(['remove-bookmarked-nonce' => wp_create_nonce('remove_bookmarked'), 'remove-bookmarked' => $product->get_id()])); ?>">
                            <i class="ri-delete-bin-7-line"></i>
                        </a>
                    </footer>
                </article>
            <?php endforeach; ?>
        </div>

        <?php wpap_pagination($total_pages); ?>
    <?php else: ?>
        <div class="wpap-notfound">
            <p><?php esc_html_e('موردی یافت نشد.', 'arvand-panel'); ?></p>
        </div>
    <?php endif; ?>
</div>