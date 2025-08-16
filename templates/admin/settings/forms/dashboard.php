<?php
defined('ABSPATH') || exit;

$dash = wpap_dash_options();
?>

<form id="wpap-dashboard-section" class="wpap-settings-section wpap-form" method="post">
    <?php wp_nonce_field('dash_nonce', 'dash_nonce'); ?>
    <input type="hidden" name="form" value="wpap_dashboard"/>

    <div class="wpap-field-wrap">
        <label for="wpap-date-latest-tickets-display">
            <?php esc_html_e('نمایش المان های داشبورد پنل', 'arvand-panel'); ?>
        </label>

        <select style="height: 150px;" id="wpap-date-latest-tickets-display" class="regular-text" name="dash_widgets[]" multiple>
            <option value="coupons" <?php selected(in_array('coupons', $dash['dash_widgets'])); ?>>
                <?php esc_html_e('کپن ها (ووکامرس)', 'arvand-panel'); ?>
            </option>

            <option value="orders" <?php selected(in_array('orders', $dash['dash_widgets'])); ?>>
                <?php esc_html_e('سفارش ها (ووکامرس)', 'arvand-panel'); ?>
            </option>

            <option value="products" <?php selected(in_array('products', $dash['dash_widgets'])); ?>>
                <?php esc_html_e('خریدهای پرتکراذ (ووکامرس)', 'arvand-panel'); ?>
            </option>

            <option value="tickets" <?php selected(in_array('tickets', $dash['dash_widgets'])); ?>>
                <?php esc_html_e('تیکت های اخیر', 'arvand-panel'); ?>
            </option>

            <option value="notices" <?php selected(in_array('notices', $dash['dash_widgets'])); ?>>
                <?php esc_html_e('اعلانات اخیر', 'arvand-panel'); ?>
            </option>
        </select>

        <p class="description">
            <?php esc_html_e('انتخاب یا عدم انتخاب گزینه ها با ctrl + click', 'arvand-panel'); ?>
        </p>
    </div>

    <footer>
        <?php require WPAP_ADMIN_TEMPLATES_PATH . 'parts/button.php'; ?>
    </footer>
</form>