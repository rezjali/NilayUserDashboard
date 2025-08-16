<?php
defined('ABSPATH') || exit;

$wallet_opt = wpap_wallet_options();
?>

<form id="wpap-wallet-section" class="wpap-settings-section wpap-form" method="post">
    <?php wp_nonce_field('wallet', 'wallet_nonce'); ?>
    <input type="hidden" name="form" value="wpap_wallet"/>

    <div class="wpap-field-wrap">
        <label>
            <input name="enabled" type="checkbox" <?php checked($wallet_opt['enabled']); ?>/>
            <?php esc_html_e('فعالسازی کیف پول', 'arvand-panel'); ?>
        </label>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-wallet-min-amount">
            <?php esc_html_e('حداقل مبلغ برای شارژ', 'arvand-panel'); ?>
        </label>

        <div>
            <input id="wpap-wallet-min-amount" class="regular-text" type="number" name="min_amount" step="1000" inputmode="numeric"
                   value="<?php echo esc_attr($wallet_opt['min_amount']); ?>"/>
        </div>
    </div>

    <footer>
        <?php require WPAP_ADMIN_TEMPLATES_PATH . 'parts/button.php'; ?>
    </footer>
</form>