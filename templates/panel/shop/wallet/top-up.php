<?php
defined( 'ABSPATH' ) || exit;

if (!class_exists('woocommerce')) {
    wpap_print_notice(__('Woocommerce plugin is not active.', 'arvand-panel'), 'info', false);
    return;
}

$opt_wallet = wpap_wallet_options();

if(!$opt_wallet['enabled']) {
    wpap_print_notice(__('کیف پول غیرفعال است.', 'arvand-panel'), 'info', false);
    return;
}

if (isset($_REQUEST['error'])) {
    if ('demo' === $_REQUEST['error']) {
        wpap_print_notice(
            __('کاربر دمو (demo) قادر به تغییر نیست.', 'arvand-panel'),
            'error',
            true,
            '0 0 30px'
        );
    }

    if ('empty_amount' === $_REQUEST['error']) {
        wpap_print_notice(
            __('لطفا مبلغ شارژ را وارد کنید.', 'arvand-panel'),
            'error',
            true,
            '0 0 30px'
        );
    }

    if ('min_amount' === $_REQUEST['error']) {
        wpap_print_notice(
            sprintf(__('حداقل مبلغ برای شارژ %s می باشد.', 'arvand-panel'), wc_price($opt_wallet['min_amount'])),
            'error',
            true,
            '0 0 30px'
        );
    }
}
?>

<div id="wpap-wallet-top-up">
    <div class="wpap-form-wrap">
        <form method="post">
            <header>
                <h2><?php esc_html_e('شارژ کیف پول', 'arvand-panel'); ?></h2>
            </header>

            <div>
                <?php wp_nonce_field('wpap_mobile_nonce', 'wpap_mobile_nonce'); ?>

                <div>
                    <p>
                        <?php
                        printf(
                            esc_html__('مبلغ فعلی کیف پول شما: %s', 'arvand-panel'),
                            '<strong>' . wc_price(wpap_wallet_get_balance(get_current_user_id())) . '</strong>'
                        );
                        ?>
                    </p>
                </div>

                <div class="wpap-field-wrap">
                    <label class="wpap-field-label" for="wpap-wallet-amount">
                        <?php printf(esc_html__('مبلغ را وارد کنید (به %s)', 'arvand-panel'), get_woocommerce_currency_symbol()); ?>
                    </label>

                    <input id="wpap-wallet-amount"
                           type="number"
                           name="wpap_wallet_amount"
                           min="1000"
                           step="1000"
                           placeholder="<?php esc_attr_e('برای مثال: 50000', 'arvand-panel'); ?>"/>

                    <span class="wpap-input-info">
                        <?php
                        esc_html_e('ورود مبلغ الزامی است و باید بصورت اعداد و معتبر باشد.', 'arvand-panel');
                        echo '&nbsp;';

                        printf(
                            wp_kses_post(__('حداقل مبلغ برای َشارژ %s می باشد.', 'arvand-panel')),
                            '<strong>' . wc_price($opt_wallet['min_amount']) . '</strong>'
                        )
                        ?>
                    </span>
                </div>

                <footer>
                    <button class="wpap-btn-1" type="submit" name="wpap_wallet_topup">
                        <?php esc_html_e('ادامه', 'arvand-panel'); ?>
                    </button>
                </footer>
            </div>
        </form>
    </div>
</div>
