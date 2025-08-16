<?php
defined('ABSPATH') || exit;

wpap_print_notice(
    __('Below you can Add or change your mobile number.', 'arvand-panel'),
    'info',
    false,
    '0 0 30px'
);
?>

<div id="wpap-mobile">
    <div class="wpap-form-wrap">
        <form id="wpap-add-phone-form" method="post">
            <header>
                <h2><?php esc_html_e('تغییر یا ثبت شماره همراه', 'arvand-panel'); ?></h2>
            </header>

            <div>
                <?php wp_nonce_field('wpap_mobile_nonce', 'wpap_mobile_nonce'); ?>

                <div class="wpap-field-wrap">
                    <label class="wpap-field-label" for="wpap-phone-field">
                        <?php esc_html_e('Phone number', 'arvand-panel'); ?>
                    </label>

                    <input id="wpap-phone-field" type="text" name="phone"/>
                </div>

                <footer>
                    <button class="wpap-btn-1" type="submit">
                        <span class="wpap-btn-text"><?php esc_html_e('Send verification code', 'arvand-panel'); ?></span>
                        <div class="wpap-loading"></div>
                    </button>
                </footer>
            </div>
        </form>

        <form id="wpap-verify-add-phone-form" method="post">
            <header>
                <h2><?php esc_html_e('کد تایید را وارد کنید', 'arvand-panel'); ?></h2>
            </header>

            <div>
                <?php wp_nonce_field('wpap_verify_mobile_nonce', 'wpap_verify_mobile_nonce'); ?>

                <div class="wpap-field-wrap">
                    <label class="wpap-field-label" for="wpap-code-field">
                        <?php esc_attr_e('کد تایید', 'arvand-panel'); ?>
                    </label>

                    <input id="wpap-code-field" type="text" name="code"/>
                </div>

                <footer>
                    <button class="wpap-btn-1" type="submit">
                        <span class="wpap-btn-text"><?php esc_html_e('Verify and add number', 'arvand-panel'); ?></span>
                        <div class="wpap-loading"></div>
                    </button>
                </footer>
            </div>
        </form>
    </div>
</div>

