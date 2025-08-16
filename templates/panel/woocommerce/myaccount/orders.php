<?php
defined('ABSPATH') || exit;

do_action('woocommerce_before_account_orders', $has_orders);

global $current_user;

$args = [
    'customer' => $current_user->ID,
    'return' => 'ids',
    'limit' => -1,
];

$status_order_count = [];
$status_order_count['wpap_all'] = count(wc_get_orders($args));

foreach (wc_get_order_statuses() as $status => $name) {
    $status_order_count[substr($status, 3)] = count(wc_get_orders($args + ['status' => $status]));
}

unset($status_order_count['checkout-draft']);
?>

<div id="wpap-wc-orders">
    <?php if (!empty($status_order_count['wpap_all'])): ?>
        <div id="wpap-orders-statuses">
            <?php foreach ($status_order_count as $status => $count): ?>
                <?php
                if (!$count) continue;

                switch ($status) {
                    case 'pending':
                        $bg_color = 'var(--wpap-color-orange)';
                        $icon = '<i class="ri-bank-card-2-line"></i>';
                        break;
                    case 'failed':
                        $bg_color = 'var(--wpap-color-red)';
                        $icon = '<i class="ri-error-warning-line"></i>';
                        break;
                    case 'on-hold':
                        $bg_color = 'var(--wpap-color-orange)';
                        $icon = '<i class="ri-hourglass-fill"></i>';
                        break;
                    case 'processing':
                        $bg_color = 'var(--wpap-color-blue)';
                        $icon = '<i class="ri-settings-2-line"></i>';
                        break;
                    case 'refunded':
                        $bg_color = 'var(--wpap-color-orange)';
                        $icon = '<i class="ri-refund-2-line"></i>';
                        break;
                    case 'completed':
                        $bg_color = 'var(--wpap-color-green)';
                        $icon = '<i class="ri-check-double-line"></i>';
                        break;
                    case 'cancelled':
                        $bg_color = 'var(--wpap-color-red)';
                        $icon = '<i class="ri-close-large-line"></i>';
                        break;
                    default:
                        $bg_color = 'var(--wpap-color-blue)';
                        $icon = '<i class="ri-bill-line"></i>';
                        break;
                }
                ?>

                <a href="<?php echo 'wpap_all' === $status ? remove_query_arg('status') : esc_url(add_query_arg(['status' => $status])); ?>">
                    <div class="wpap-orders-status-item">
                        <div style="background-color: <?php echo esc_attr($bg_color); ?>">
                            <?php echo $icon; ?>
                        </div>

                        <div>
                            <h4><?php printf(esc_html__('%s سفارش', 'arvand-panel'), $count); ?></h4>

                            <span>
                                <?php
                                echo esc_html(
                                    wc_get_order_status_name('wpap_all' === $status ? __('همه', 'arvand-panel') : $status)
                                );
                                ?>
                            </span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($has_orders): ?>
        <div id="wpap-orders-wrap">
            <?php foreach ($customer_orders->orders as $customer_order): ?>
                <?php
                $order = wc_get_order($customer_order);
                $item_count = $order->get_item_count() - $order->get_item_count_refunded();
                ?>

                <div class="wpap-list">
                    <header>
                        <div class="wpap-order-item-details">
                            <?php foreach (wc_get_account_orders_columns() as $column_id => $column_name) : ?>
                                <?php if (has_action('woocommerce_my_account_my_orders_column_' . $column_id)):
                                    do_action('woocommerce_my_account_my_orders_column_' . $column_id, $order); ?>

                                <?php elseif ('order-number' === $column_id): ?>
                                    <span>
                                        <?php echo esc_html($column_name); ?>

                                        <a href="<?php echo esc_url($order->get_view_order_url()); ?>">
                                            <?php echo esc_html(_x('#', 'hash before order number', 'woocommerce') . $order->get_order_number()); ?>
                                        </a>
                                    </span>

                                <?php elseif ('order-date' === $column_id): ?>
                                    <time datetime="<?php echo esc_attr($order->get_date_created()->date('c')); ?>">
                                        <?php echo esc_html($column_name . ': ' . wc_format_datetime($order->get_date_created())); ?>
                                    </time>

                                <?php elseif ('order-status' === $column_id):
                                    echo esc_html($column_name . ': ' . wc_get_order_status_name($order->get_status())); ?>

                                <?php elseif ('order-total' === $column_id): ?>
                                    <span>
                                        <?php
                                        echo wp_kses_post(
                                            $column_name . ': ' . sprintf(
                                                _n('%1$s for %2$s item', '%1$s for %2$s items', $item_count, 'woocommerce'),
                                                $order->get_formatted_order_total(),
                                                $item_count
                                            )
                                        );
                                        ?>
                                    </span>

                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>

                        <a class="wpap-list-show-all" href="<?php echo esc_url($order->get_view_order_url()); ?>">
                            <?php esc_html_e('مشاهده جزئیات', 'arvand-panel'); ?>
                        </a>
                    </header>

                    <div class="wpap-list-wrap">
                        <?php foreach ($customer_order->get_items() as $item): ?>
                            <?php
                            /**
                             * @var WC_Order_Item $item
                             * @var WC_Product $product
                             */
                            $product = $item->get_product();

                            $is_visible = $product && $product->is_visible();
                            $product_permalink = $is_visible ? $product->get_permalink() : '';
                            ?>

                            <?php
                            echo $product_permalink
                                ? sprintf('<a class="wpap-list-item" href="%s">', esc_url($product_permalink))
                                : '<div class="wpap-list-item">';
                            ?>

                            <div class="wpap-order-item-image">
                                <?php echo wp_kses_post($product ? $product->get_image('post_thumbnail') : wc_placeholder_img()); ?>
                            </div>

                            <div class="wpap-list-item-title">
                                <h2><?php echo esc_html($item->get_name()); ?></h2>
                            </div>

                            <?php echo $product_permalink ? '</a>' : '</div>'; ?>
                        <?php endforeach; ?>
                    </div>

                    <footer>
                        <?php
                        $actions = wc_get_account_orders_actions($order);
                        if (!empty($actions)) {
                            foreach ($actions as $key => $action) {
                                printf('<a href="%s" class="wpap-btn-1 %s">%s</a>',
                                    esc_url($action['url']),
                                    sanitize_html_class($key),
                                    esc_html($action['name'])
                                );
                            }
                        }
                        ?>
                    </footer>
                </div>
            <?php endforeach; ?>
        </div>

        <?php do_action('woocommerce_before_account_orders_pagination'); ?>

        <?php if (1 < $customer_orders->max_num_pages): ?>
            <div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination">
                <?php if (1 !== $current_page): ?>
                    <a class="wpap-btn-1"
                       href="<?php echo esc_url(add_query_arg('orders-page', $current_page - 1)); ?>">
                        <?php esc_html_e('Previous', 'woocommerce'); ?>
                    </a>
                <?php endif; ?>

                <?php if (intval($customer_orders->max_num_pages) !== $current_page): ?>
                    <a class="wpap-btn-1"
                       href="<?php echo esc_url(add_query_arg('orders-page', $current_page + 1)); ?>">
                        <?php esc_html_e('Next', 'woocommerce'); ?>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <?php
        wpap_print_notice(
            esc_html__('سفارشی وجود ندارد.', 'arvand-panel'),
            'info',
            false,
            false
        );
        ?>
    <?php endif; ?>

    <?php do_action('woocommerce_after_account_orders', $has_orders); ?>
</div>
