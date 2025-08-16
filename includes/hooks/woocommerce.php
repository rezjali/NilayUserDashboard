<?php
defined('ABSPATH') || exit;

add_filter('woocommerce_add_to_cart_validation', function ($passed, $product_id, $quantity) {
    if (wpap_is_demo()) {
        wc_add_notice(__('کاربر دمو (demo) قادر به تغییر نیست.', 'arvand-panel'), 'error');
        return false;
    }

    return $passed;
}, 10, 3);

add_action( 'woocommerce_checkout_process', function() {
    if (wpap_is_demo()) {
        wc_add_notice(__('کاربر دمو (demo) قادر به تغییر نیست.', 'arvand-panel'), 'error');
    }
});

// Load plugin WC templates
add_filter('woocommerce_locate_template', function ($template, $template_name, $template_path) {
    if (!wpap_is_panel_page()) {
        return $template;
    }

    global $woocommerce;
    $_template = $template;

    if (!$template_path) {
        $template_path = $woocommerce->template_url;
    }

    $plugin_path = WPAP_TEMPLATES_PATH . 'panel/woocommerce/';
    $template = locate_template([$template_path . $template_name, $template_name]);

    if (!$template && file_exists($plugin_path . $template_name)) {
        $template = $plugin_path . $template_name;
    }

    if (!$template) {
        $template = $_template;
    }

    return $template;
}, PHP_INT_MAX, 3);

// Filter order list by status
add_filter('woocommerce_my_account_my_orders_query', function ($args) {
    if (!wpap_is_panel_page()) {
        return $args;
    }

    if (empty($_GET['status'])) {
        return $args;
    }

    $args['status'] = 'wc-' . sanitize_title($_GET['status']);

    return $args;
});

// Change WC order view url
add_filter('woocommerce_get_view_order_url', function ($url, $order) {
    if (!wpap_is_panel_page()) {
        return $url;
    }

    return esc_url(wpap_panel_url('orders/' . $order->get_id()));
}, PHP_INT_MAX, 2);

add_filter('woocommerce_account_downloads_columns', function ($columns) {
    unset($columns['download-product']);
    return $columns;
}, PHP_INT_MAX);

// Save wc edit address forms fields
add_action('template_redirect', function () {
    if (!wpap_is_panel_page() || empty($_POST['action']) || 'edit_address' !== $_POST['action']) {
        return;
    }

    $endpoint = get_query_var('wpap-endpoint');
    $address_type = explode('/', $endpoint);

    if (!$endpoint || !in_array(($address_type[1] ?? ''), ['billing', 'shipping'])) {
        return;
    }

    $GLOBALS['wp']->query_vars['edit-address'] = $address_type[1];
    WC_Form_Handler::save_address();
}, PHP_INT_MAX);

add_action('woocommerce_after_save_address_validation', function ($address, $customer_id, $load_address) {
    if (wpap_is_demo() && !wc_has_notice(__('کاربر دمو (demo) قادر به تغییر نیست.', 'arvand-panel'), 'error')) {
        wc_add_notice(__('کاربر دمو (demo) قادر به تغییر نیست.', 'arvand-panel'), 'error');
    }
}, 10, 3);

// Redirect to plugin wc address page
add_action('woocommerce_customer_save_address', function ($user_id, $load_address) {
    if (is_admin() || !wpap_is_panel_page()) {
        return;
    }

    if (0 === wc_notice_count('error')) {
        wc_add_notice(__('آدرس با موفقیت تغییر یافت.', 'arvand-panel'));
    }

    wp_safe_redirect(wpap_panel_url('addresses/' . $load_address));
    exit;
}, PHP_INT_MAX, 2);

add_action('woocommerce_save_account_details_errors', function (WP_Error $errors) {
    if (wpap_is_demo()) {
        wc_add_notice(__('کاربر دمو (demo) قادر به تغییر نیست.', 'arvand-panel'), 'error');
    }
});

// Redirect to plugin wc edit account page
add_action('woocommerce_save_account_details', function ($user_id) {
    if (is_admin() || !wpap_is_panel_page()) {
        return;
    }

    if (0 === wc_notice_count('error')) {
        wc_add_notice(__('مشخصات حساب کاربری با موفقیت تغییر یافت.', 'arvand-panel'));
    }

    wp_safe_redirect(wpap_get_page_url_by_name('wc_edit_account'));
    exit;
}, PHP_INT_MAX, 2);

// Add "add to list button" in single product page
function wpap_add_to_list_button()
{
    if (!wpap_general_options()['add_to_list_btn_display']) {
        return;
    }

    echo do_shortcode('[wpap_bookmark_btn]');
}

add_action('woocommerce_single_product_summary', 'wpap_add_to_list_button', 35);

// Add to list button styles
add_action('wp_head', function () {
    if (!wpap_general_options()['add_to_list']) {
        return;
    }

    if (function_exists('is_product') && !is_product()) {
        return;
    }
    ?>
    <style id="wpap-add-to-list-button-styles">
        .wpap-add-to-list-button {
            background: none;
            padding: 5px;
            font-size: 24px;
            border: none;
            cursor: pointer;

            svg {
                width: 24px;
                height: 24px;
                color: darkgrey;
            }

            &.added svg {
                color: red;
            }
        }
    </style>
    <?php
});

// Add to list button scripts
add_action('wp_footer', function () {
    if (!wpap_general_options()['add_to_list']) {
        return;
    }

    if (function_exists('is_product') && !is_product()) {
        return;
    }
    ?>
    <script>
        jQuery(document).ready(function ($) {
            $('.wpap-add-to-list-button').on('click', function (e) {
                e.preventDefault();
                var button = $(this);
                var product_id = button.data('product-id');

                $.ajax({
                    url: '<?php echo admin_url("admin-ajax.php"); ?>',
                    type: 'POST',
                    data: {
                        action: 'wpap_add_to_list',
                        product_id: product_id
                    },
                    success: function (response) {
                        if (response.status === 'added') {
                            button.addClass('added');
                        } else {
                            button.removeClass('added');
                        }
                    }
                });
            });
        });
    </script>
    <?php
});

// Save user latest visited products
add_action('template_redirect', function () {
    if (wpap_is_demo()) {
        return;
    }

    // Only run on single product pages
    if (!function_exists('is_product') || !is_product()) {
        return;
    }

    global $post;

    if (!$post || $post->post_type !== 'product') {
        return;
    }

    $product_id = $post->ID;

    // Get current list of visited product IDs
    $visited_ids = get_user_meta(get_current_user_id(), 'wpap_visited', true);
    $visited_ids = is_array($visited_ids) ? $visited_ids : [];

    // Remove if it already exists (we want to move it to the end)
    $visited_ids = array_diff($visited_ids, [$product_id]);

    // Append the current product ID
    $visited_ids[] = $product_id;

    // Keep only the latest 20
    if (count($visited_ids) > 20) {
        $visited_ids = array_slice($visited_ids, -20);
    }

    // Save back to the option
    update_user_meta(get_current_user_id(), 'wpap_visited', $visited_ids);
});