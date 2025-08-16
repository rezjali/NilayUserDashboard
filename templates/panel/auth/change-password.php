<?php
defined('ABSPATH') || exit;

$pass_options = \Arvand\ArvandPanel\Form\WPAPFieldSettings::password();
?>

<div class="wpap-form-wrap">
    <form id="wpap-change-password" method="post">
        <header>
            <h2><?php esc_html_e('تغییر رمز عبور', 'arvand-panel'); ?></h2>
        </header>

        <div>
            <?php wp_nonce_field('change_password_nonce', 'change_password_nonce'); ?>
            <input type="hidden" name="action" value="change_pass"/>

            <div class="wpap-field-wrap">
                <label class="wpap-field-label" for="wpap-password-field">
                    <?php esc_html_e('رمز عبور جدید', 'arvand-panel'); ?>
                </label>

                <input id="wpap-password-field" type="password" name="user_pass"/>

                <span class="wpap-input-info">
                    <?php
                    echo sprintf(
                        esc_html__('At least %d letters. For more security, use a combination of letters and numbers.', 'arvand-panel'),
                        esc_html($pass_options['rules']['min_length'])
                    );
                    ?>
                </span>
            </div>

            <div class="wpap-field-wrap">
                <label class="wpap-field-label" for="wpap-confirm-user-pass-field">
                    <?php esc_html_e('تایید رمز عبور', 'arvand-panel'); ?>
                </label>

                <input id="wpap-confirm-user-pass-field" type="password" name="confirm_user_pass"/>
            </div>

            <footer>
                <button class="wpap-btn-1" type="submit">
                    <span class="wpap-btn-text"><?php esc_html_e('تغییر رمز', 'arvand-panel'); ?></span>
                    <div class="wpap-loading"></div>
                </button>
            </footer>
        </div>
    </form>
</div>