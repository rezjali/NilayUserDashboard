<?php
defined('ABSPATH') || exit;

$login = wpap_login_options();
?>

<form id="wpap-login-section" class="wpap-settings-section wpap-form" method="post">
    <?php wp_nonce_field('login_nonce', 'login_nonce'); ?>
    <input type="hidden" name="form" value="wpap_login"/>

    <div class="wpap-field-wrap">
        <label for="wpap-enable-def-login"><?php esc_html_e('Enable plugin default login', 'arvand-panel'); ?></label>

        <span class="wpap-checkbox-wrap">
            <label>
                <input id="wpap-enable-def-login" name="enable_def_login"
                       type="checkbox" <?php checked($login['enable_def_login']); ?>/>
                <span class="wpap-checkbox"></span>
            </label>
        </span>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-disable-default-login">
            <?php esc_html_e('Redirect default login to plugin login', 'arvand-panel'); ?>
        </label>

        <span class="wpap-checkbox-wrap">
            <label>
                <input id="wpap-disable-default-login" name="default_login"
                       type="checkbox" <?php checked($login['default_login']); ?>/>
                <span class="wpap-checkbox"></span>
            </label>
        </span>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-enable-sms-reg-login">
            <?php esc_html_e('Enable sms register or login', 'arvand-panel'); ?>
        </label>

        <span class="wpap-checkbox-wrap">
            <label>
                <input id="wpap-enable-sms-reg-login" name="enable_sms_register_login"
                       type="checkbox" <?php checked($login['enable_sms_register_login']); ?>/>
                <span class="wpap-checkbox"></span>
            </label>
        </span>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-enable-force-to-add-mobile">
            <?php esc_html_e('Force user to add and verify phone number.', 'arvand-panel'); ?>
        </label>

        <div>
            <span class="wpap-checkbox-wrap">
                <label>
                    <input id="wpap-enable-force-to-add-mobile" name="force_to_add_mobile"
                           type="checkbox" <?php checked($login['force_to_add_mobile']); ?>/>
                    <span class="wpap-checkbox"></span>
                </label>
            </span>

            <p class="description">
                <?php esc_html_e('If "Enable sms login" option and this option is enabled, users who have not registered or confirmed their mobile number will be required to register or confirm their mobile number.', 'arvand-panel'); ?>
            </p>
        </div>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-reset-pass-method">
            <?php esc_html_e('Reset password method', 'arvand-panel'); ?>
        </label>

        <select id="wpap-reset-pass-method" class="regular-text" name="reset_pass_method">
            <option value="both" <?php selected($login['reset_pass_method'], 'both'); ?>>
                <?php esc_html_e('Both', 'arvand-panel'); ?>
            </option>

            <option value="email" <?php selected($login['reset_pass_method'], 'email'); ?>>
                <?php esc_html_e('By email', 'arvand-panel'); ?>
            </option>

            <option value="mobile" <?php selected($login['reset_pass_method'], 'mobile'); ?>>
                <?php esc_html_e('By mobile', 'arvand-panel'); ?>
            </option>
        </select>
    </div>

    <footer>
        <?php require WPAP_ADMIN_TEMPLATES_PATH . 'parts/button.php'; ?>
    </footer>
</form>