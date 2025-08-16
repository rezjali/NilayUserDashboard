<?php
/**
 * View Order
 *
 * Shows the details of a particular order on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/view-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.0.0
 */

defined('ABSPATH') || exit;

$notes = $order->get_customer_order_notes();
?>

<section id="wpap-wc-view-order">
    <?php
    wpap_print_notice(
        sprintf(
             esc_html__('Order #%1$s was placed on %2$s and is currently %3$s.', 'woocommerce'),
             '<strong class="order-number">' . $order->get_order_number() . '</strong>',
             '<strong class="order-date">' . wc_format_datetime($order->get_date_created()) . '</strong>',
             '<strong class="order-status">' . wc_get_order_status_name($order->get_status()) . '</strong>'
         ),
        'info',
        true,
        '0 0 30px'
    );
    ?>

    <?php if ($notes): ?>
        <div id="wpap-order-notes" class="wpap-list">
            <header>
                <h2><?php esc_html_e('Order updates', 'woocommerce'); ?></h2>
            </header>

            <?php foreach ($notes as $note) : ?>
                <div class="wpap-list-item">
                    <div>
                        <time>
                            <?php
                            echo date_i18n(
                                esc_html__('d M Y ساعت H:i', 'woocommerce'),
                                strtotime($note->comment_date)
                            );
                            ?>
                        </time>

                        <div><?php echo wpautop(wptexturize($note->comment_content)); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php do_action('woocommerce_view_order', $order_id); ?>
</section>
