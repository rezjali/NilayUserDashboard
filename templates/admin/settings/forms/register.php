<?php
defined('ABSPATH') || exit;

$register = wpap_register_options();
?>

<form id="wpap-register-section" class="wpap-settings-section wpap-form" method="post">
    <?php wp_nonce_field('register_nonce', 'register_nonce'); ?>
    <input type="hidden" name="form" value="wpap_register"/>

    <div class="wpap-field-wrap">
        <label for="wpap-enable-def-reg">
            <?php esc_html_e('Enable plugin default register', 'arvand-panel'); ?>
        </label>

        <span class="wpap-checkbox-wrap">
            <label>
                <input id="wpap-enable-def-reg" name="enable_def_reg"
                       type="checkbox" <?php checked($register['enable_def_reg']); ?>/>
                <span class="wpap-checkbox"></span>
            </label>
        </span>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-redirect-default-register">
            <?php esc_html_e('Redirect default register', 'arvand-panel'); ?>
        </label>

        <span class="wpap-checkbox-wrap">
            <label>
                <input id="wpap-redirect-default-register" name="default_register"
                       type="checkbox" <?php checked($register['default_register']); ?>/>
                <span class="wpap-checkbox"></span>
            </label>
        </span>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-enable-pass-strength">
            <?php esc_html_e('Enable password strength', 'arvand-panel'); ?>
        </label>

        <span class="wpap-checkbox-wrap">
            <label>
                <input id="wpap-enable-pass-strength" name="pass_strength"
                       type="checkbox" <?php checked($register['pass_strength']); ?>/>
                <span class="wpap-checkbox"></span>
            </label>
        </span>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-register-activation">
            <?php _e('Require user to activate account by email', 'arvand-panel'); ?>
        </label>

        <span class="wpap-checkbox-wrap">
            <label>
                <input id="wpap-register-activation" name="register_activation"
                       type="checkbox" <?php checked($register['register_activation']); ?>/>
                <span class="wpap-checkbox"></span>
            </label>
        </span>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-sms-password">
            <?php esc_html_e('Password in mobile registration', 'arvand-panel'); ?>
        </label>

        <span class="wpap-checkbox-wrap">
            <label>
                <input id="wpap-sms-password" name="sms_reg_password"
                       type="checkbox" <?php checked($register['sms_reg_password']); ?>/>
                <span class="wpap-checkbox"></span>
            </label>
        </span>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-enable-admin-approval">
            <?php esc_html_e('Enable account approval by admin', 'arvand-panel'); ?>
        </label>

        <div>
            <span class="wpap-checkbox-wrap">
                <label>
                    <input id="wpap-enable-admin-approval" name="enable_admin_approval"
                           type="checkbox" <?php checked($register['enable_admin_approval']); ?>/>
                    <span class="wpap-checkbox"></span>
                </label>
            </span>

            <p class="description">
                <?php esc_html_e('By activating this option, registered users can login to their account only if approved by site administrator. To approve user account, go to the users menu and the WordPress user editing page.', 'arvand-panel'); ?>
            </p>
        </div>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-enable-agree">
            <?php esc_html_e('Enable agree to terms', 'arvand-panel'); ?>
        </label>

        <span class="wpap-checkbox-wrap">
            <label>
                <input id="wpap-enable-agree" name="enable_agree"
                       type="checkbox" <?php checked($register['enable_agree']); ?>/>
                <span class="wpap-checkbox"></span>
            </label>
        </span>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-agree-required">
            <?php esc_html_e('require user to agree', 'arvand-panel'); ?>
        </label>

        <span class="wpap-checkbox-wrap">
            <label>
                <input id="wpap-agree-required" name="agree_required"
                       type="checkbox" <?php checked($register['agree_required']); ?>/>
                <span class="wpap-checkbox"></span>
            </label>
        </span>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-agree-text"><?php esc_html_e('"Agree to terms" text', 'arvand-panel'); ?></label>

        <?php
        $args = [
            'wpautop' => true,
            'media_buttons' => false,
            'textarea_name' => 'agree_text',
            'textarea_rows' => 8,
            'quicktags' => false,
            'tinymce' => [
                'toolbar1' => 'bold,italic,underline,separator,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo',
                'toolbar2' => '',
                'toolbar3' => '',
            ],
        ];

        wp_editor($register['agree_text'], 'wpap-agree-text', $args);
        ?>
    </div>

    <footer>
        <?php require WPAP_ADMIN_TEMPLATES_PATH . 'parts/button.php'; ?>
    </footer>
</form>