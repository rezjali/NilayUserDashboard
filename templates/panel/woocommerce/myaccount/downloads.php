<?php
/**
 * Downloads
 *
 * Shows downloads on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/downloads.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.2.0
 */

defined('ABSPATH') || exit;

$downloadable_products = WC()->customer->get_downloadable_products();
$has_downloads = (bool)$downloadable_products;
$downloads = [];

foreach ($downloadable_products as $downloadable_product) {
    $downloads[$downloadable_product['order_id']][$downloadable_product['product_id']][] = $downloadable_product;
}
?>

<div id="wpap-wc-downloads" class="wpap-wc-downloads">
    <?php do_action('woocommerce_before_account_downloads', $has_downloads); ?>

    <?php if ($has_downloads): ?>
        <?php do_action('woocommerce_before_available_downloads'); ?>

        <div class="wpap-wc-downloadable-orders">
            <?php foreach ($downloads as $order_id => $products): ?>
                <div class="wpap-downloadable-order">
                    <div class="wpap-downloadable-order-num">
                        <?php
                        echo sprintf(
                            esc_html__('#%d - %s', 'arvand-panel'),
                            $order_id,
                            get_the_date('', $order_id)
                        );
                        ?>
                    </div>

                    <div class="wpap-wc-download-items">
                        <?php foreach ($products as $product_id => $product): ?>
                            <div class="wpap-wc-download-item">
                                <div class="wpap-show-downloadable-product-btn">
                                    <span><?php echo get_the_title($product_id); ?></span>
                                    <i class="ri-arrow-up-s-line"></i>
                                </div>

                                <div class="wpap-downloads-table-wrap wpap-table-wrap">
                                    <table>
                                        <thead>
                                        <tr>
                                            <?php foreach (wc_get_account_downloads_columns() as $column_id => $column_name) : ?>
                                                <th class="<?php echo esc_attr($column_id); ?>">
                                                    <span class="nobr"><?php echo esc_html($column_name); ?></span>
                                                </th>
                                            <?php endforeach; ?>
                                        </tr>
                                        </thead>

                                        <?php foreach ($product as $download): ?>
                                            <tr>
                                                <?php foreach (wc_get_account_downloads_columns() as $column_id => $column_name) : ?>
                                                    <td class="<?php echo esc_attr($column_id); ?>" data-title="<?php echo esc_attr($column_name); ?>">
                                                        <?php
                                                        if (has_action('woocommerce_account_downloads_column_' . $column_id)) {
                                                            do_action('woocommerce_account_downloads_column_' . $column_id, $download);
                                                        } else {
                                                            switch ($column_id) {
                                                                case 'download-product':
                                                                    if ($download['product_url']) {
                                                                        echo '<a href="' . esc_url($download['product_url']) . '">' . esc_html($download['product_name']) . '</a>';
                                                                    } else {
                                                                        echo esc_html($download['product_name']);
                                                                    }
                                                                    break;
                                                                case 'download-file':
                                                                    echo '<a href="' . esc_url($download['download_url']) . '" class="woocommerce-MyAccount-downloads-file button alt">' . esc_html($download['download_name']) . '</a>';
                                                                    break;
                                                                case 'download-remaining':
                                                                    echo is_numeric($download['downloads_remaining']) ? esc_html($download['downloads_remaining']) : esc_html__('&infin;', 'woocommerce');
                                                                    break;
                                                                case 'download-expires':
                                                                    if (!empty($download['access_expires'])) {
                                                                        echo '<time datetime="' . esc_attr(date('Y-m-d', strtotime($download['access_expires']))) . '" title="' . esc_attr(strtotime($download['access_expires'])) . '">' . esc_html(date_i18n(get_option('date_format'), strtotime($download['access_expires']))) . '</time>';
                                                                    } else {
                                                                        esc_html_e('Never', 'woocommerce');
                                                                    }
                                                                    break;
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </table>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php do_action('woocommerce_after_available_downloads'); ?>
    <?php else: ?>
       <?php wpap_print_notice(__('No downloads available yet.', 'woocommerce'), 'info', false); ?>
    <?php endif; ?>

    <?php do_action('woocommerce_after_account_downloads', $has_downloads); ?>
</div>