<?php
defined('ABSPATH') || exit;

$google_opt = wpap_google_options();
?>

<form id="wpap-google-section" class="wpap-settings-section wpap-form" method="post">
    <?php wp_nonce_field('google', 'google_nonce'); ?>
    <input type="hidden" name="form" value="wpap_google"/>

    <div class="wpap-field-wrap">
        <label for="wpap-enable-recaptcha">
            <?php esc_html_e('Enable google recaptcha', 'arvand-panel'); ?>
        </label>

        <div>
            <span class="wpap-checkbox-wrap">
                <label>
                    <input id="wpap-enable-recaptcha" name="enable_recaptcha"
                           type="checkbox" <?php checked($google_opt['enable_recaptcha']); ?>/>
                    <span class="wpap-checkbox"></span>
                </label>
            </span>
        </div>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-recaptcha-site-key">
            <?php esc_html_e('Google recaptcha site key', 'arvand-panel'); ?>
        </label>

        <div>
            <input id="wpap-recaptcha-site-key" class="regular-text" type="text" name="recaptcha_site_key"
                   value="<?php echo esc_attr($google_opt['recaptcha_site_key']); ?>"/>
        </div>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-recaptcha-secret-key">
            <?php esc_html_e('Google recaptcha secret key', 'arvand-panel'); ?>
        </label>

        <div>
            <input id="wpap-recaptcha-secret-key" class="regular-text" type="text" name="recaptcha_secret_key"
                   value="<?php echo esc_attr($google_opt['recaptcha_secret_key']); ?>"/>
        </div>
    </div>

    <footer>
        <?php require WPAP_ADMIN_TEMPLATES_PATH . 'parts/button.php'; ?>
    </footer>
</form>