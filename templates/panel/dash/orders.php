<?php
defined('ABSPATH') || exit;

if (!in_array('orders', $dash['dash_widgets'])) {
    return;
}

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

$orders = wc_get_orders([
    'customer' => $current_user->ID,
    'limit' => 5,
]);

$order_list_url = wpap_get_page_url_by_name('wc_orders');
?>

<div id="wpap-dash-orders" class="wpap-list">
    <header>
        <h2><?php esc_html_e("سفارش\xE2\x80\x8Cها", 'arvand-panel'); ?></h2>

        <a href="<?php echo esc_url(wpap_get_page_url_by_name('wc_orders')); ?>" class="wpap-list-show-all">
            <?php esc_html_e('مشاهده همه', 'arvand-panel'); ?>
        </a>
    </header>

    <div class="wpap-list-wrap">
        <?php if (!empty($orders)): ?>
            <div id="wpap-dash-orders-statuses">
                <?php
                foreach ($status_order_count as $status => $count) {
                    if ($count > 0) {
                        printf(
                            '<a href="%s">%s</a>',
                            esc_url('wpap_all' === $status ? $order_list_url : "$order_list_url?status=$status"),
                            sprintf(
                                esc_html__('%s: %d', 'arvand-panel'),
                                'wpap_all' === $status ? __('همه', 'arvand-panel') : wc_get_order_status_name($status),
                                $count
                            ),
                        );
                    }
                }
                ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($orders)): ?>
            <?php foreach ($orders as $order): ?>
                <?php $item_count = $order->get_item_count() - $order->get_item_count_refunded(); ?>

                <a class="wpap-list-item" href="<?php echo esc_url(wpap_panel_url('orders/' . $order->get_id())); ?>">
                    <?php
                    if ('pending' === $order->get_status()) {
                        echo '<i style="color: var(--wpap-color-orange);" class="ri-bank-card-2-line"></i>';
                    } elseif ('processing' === $order->get_status()) {
                        echo '<i style="color: var(--wpap-color-2);" class="ri-settings-2-line"></i>';
                    } elseif ('on-hold' === $order->get_status()) {
                        echo '<i style="color: var(--wpap-color-orange);" class="ri-hourglass-fill"></i>';
                    } elseif ('completed' === $order->get_status()) {
                        echo '<i style="color: var(--wpap-color-green);" class="ri-check-double-line"></i>';
                    } elseif ('cancelled' === $order->get_status()) {
                        echo '<i style="color: var(--wpap-color-red);" class="ri-close-large-line"></i>';
                    } elseif ('refunded' === $order->get_status()) {
                        echo '<i style="color: var(--wpap-color-red);" class="ri-refund-2-line"></i>';
                    } elseif ('failed' === $order->get_status()) {
                        echo '<i style="color: var(--wpap-color-red);" class="ri-error-warning-line"></i>';
                    } else {
                        echo '<i class="ri-bill-line"></i>';
                    }
                    ?>

                    <div class="wpap-list-item-title">
                        <?php
                        printf('<h2>%s</h2>',
                            sprintf(
                                __('سفارش شماره %d - %s برای %d مورد.'),
                                esc_html($order->get_id()),
                                wp_kses_post('<strong>' . $order->get_formatted_order_total() . '</strong>'),
                                esc_html($item_count)
                            )
                        );

                        printf('<div class="wpap-list-item-subtitle"><time datetime="%s">%s</time> - <span>%s</span></div>',
                            esc_attr($order->get_date_created()->date('c')),
                            esc_html(wc_format_datetime($order->get_date_created())),
                            esc_html(wc_get_order_status_name($order->get_status())),
                        );
                        ?>
                    </div>

                    <time>
                        <?php
                        echo human_time_diff($order->get_date_created()->getTimestamp(), current_time('timestamp'));
                        esc_html_e(' قبل', 'arvand-panel');
                        ?>
                    </time>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="wpap-list-item">
                <?php esc_html_e('سفارشی وجود ندارد.', 'arvand-panel'); ?>
            </div>
        <?php endif; ?>
    </div>
</div>

