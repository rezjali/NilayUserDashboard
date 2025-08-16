<?php
defined('ABSPATH') || exit;

if(!wpap_wallet_options()['enabled']) {
    return;
}

// Create wallet transactions table on plugin activation
register_activation_hook(WPAP_DIR_PATH . 'arvand-panel.php', function () {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wpap_wallet_transactions';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT UNSIGNED NOT NULL,
        amount DECIMAL(19,4) NOT NULL,
        type ENUM('credit', 'debit') NOT NULL,
        description TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
});

// Define the wallet payment gateway class
add_action('plugins_loaded', function () {
    if (!class_exists('Woocommerce')) {
        return;
    }

    class WC_Gateway_WPAP_Wallet extends WC_Payment_Gateway
    {
        public function __construct()
        {
            $this->id = 'wpap_wallet';
            $this->method_title = __('کیف پول', 'arvand-panel');
            $this->method_description = __('پرداخت با مبلغ کیف پول (اروند پنل)', 'arvand-panel');
            $this->has_fields = true;

            $this->init_form_fields();
            $this->init_settings();

            $title = $this->get_option('title');
            $this->title = !empty($title) ? $title : __('کیف پول', 'arvand-panel');

            $this->enabled = $this->get_option('enabled');

            add_action('woocommerce_update_options_payment_gateways_' . $this->id, [$this, 'process_admin_options']);
        }

        // Define gateway settings for admin panel
        public function init_form_fields()
        {
            $this->form_fields = [
                'enabled' => [
                    'title' => 'Enable/Disable',
                    'type' => 'checkbox',
                    'label' => __('فعالسازی کیف پول اروند پنل', 'arvand-panel'),
                    'default' => 'yes',
                ],
                'title' => [
                    'title' => __('عنوان', 'arvand-panel'),
                    'type' => 'text',
                    'description' => __('هنگام انتخاب روش پرداخت به کاربر نمایش داده میشود.', 'arvand-panel'),
                    'default' => __('کیف پول', 'arvand-panel'),
                    'desc_tip' => true,
                ],
            ];
        }

        public function payment_fields()
        {
            if (!is_user_logged_in()) {
                echo '<p>' . esc_html__('برای استفاده از کیف پول ابتدا وارد شوید.', 'arvand-panel') . '</p>';
                return;
            }

            $user_id = get_current_user_id();
            $balance = wpap_wallet_get_balance($user_id);
            $formatted_balance = wc_price($balance);

            echo '<p>' . sprintf(esc_html__('موجودی فعلی شما: %s', 'arvand-panel'), "<strong>$formatted_balance</strong>") . '</p>';
        }

        public function process_payment($order_id): array
        {
            $order = wc_get_order($order_id);
            $user_id = $order->get_user_id();
            $total = $order->get_total();

            if (!$user_id || $total <= 0) {
                wc_add_notice(__('مشکلی در پردازش سفارش ایجاد شده.', 'arvand-panel'), 'error');
                return [
                    'result' => 'failure',
                    'redirect' => '',
                ];
            }

            $balance = wpap_wallet_get_balance($user_id);
            if ($balance < $total) {
                wc_add_notice(__('موجودی کیف پول کافی نیست.', 'arvand-panel'), 'error');
                return [
                    'result' => 'failure',
                    'redirect' => '',
                ];
            }

            $result = wpap_wallet_insert_transaction(
                $user_id,
                $total,
                'debit',
                sprintf(__('پرداخت سفارش #%d', 'arvand-panel'), $order_id)
            );
            if (false === $result) {
                wc_add_notice(__('خطا در کاهش مبلغ کیف پول.', 'arvand-panel'), 'error');
                return [
                    'result' => 'failure',
                    'redirect' => '',
                ];
            }

            $order->update_status('processing', __('پرداخت توسط کیف پول انجام شد.', 'arvand-panel'));
            $order->save();

            return [
                'result' => 'success',
                'redirect' => $this->get_return_url($order),
            ];
        }
    }
});

// Add wallet payment method
add_filter('woocommerce_payment_gateways', function ($gateways) {
    $gateways[] = 'WC_Gateway_WPAP_Wallet';
    return $gateways;
});

// Disable wallet payment gateway for wallet top-up orders
add_filter('woocommerce_available_payment_gateways', function ($gateways) {
    if (is_admin()) {
        return $gateways;
    }

    if (isset($_GET['pay_for_order']) && isset($_GET['key'])) {
        $order_id = wc_get_order_id_by_order_key($_GET['key']);
        $order = wc_get_order($order_id);
        if ($order && $order->get_meta('wpap_wallet_topup') === 'yes') {
            unset($gateways['wpap_wallet']);
        }
    }

    return $gateways;
});

// Credit wallet after successful top-up order payment
add_action('woocommerce_payment_complete_order', 'wpap_wallet_credit_after_payment_immediately');
add_action('woocommerce_order_status_processing', 'wpap_wallet_credit_after_payment_immediately');
add_action('woocommerce_order_status_completed', 'wpap_wallet_credit_after_payment_immediately');
function wpap_wallet_credit_after_payment_immediately($order_id)
{
    $order = wc_get_order($order_id);
    if (!$order) {
        return;
    }

    if ($order->get_meta('wpap_wallet_topup') !== 'yes') {
        return;
    }

    if (!$order->is_paid()) {
        return;
    }

    $amount = floatval($order->get_meta('wpap_wallet_amount'));
    if ($amount < floatval(wpap_wallet_options()['min_amount'])) {
        return;
    }

    $user_id = $order->get_user_id();
    $already_credited = $order->get_meta('wpap_wallet_credited');

    if ($already_credited !== 'yes' && $user_id && $amount > 0) {
        wpap_wallet_insert_transaction(
            $user_id,
            $amount,
            'credit',
            sprintf(__('شارژ کیف پول با سفارش #%d', 'arvand-panel'), $order_id)
        );

        $order->add_order_note(
            sprintf(__('کیف پول با مبلغ %s شارژ شد.', 'arvand-panel'), wc_price($amount))
        );

        $order->update_meta_data('wpap_wallet_credited', 'yes');
        $order->save();
    }
}
