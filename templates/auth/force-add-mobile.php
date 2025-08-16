<?php defined('ABSPATH') || exit; ?>

<form id="wpap-force-add-mobile-form" class="wpap-auth-form" method="post">
    <div class="wpap-fields">
        <?php wp_nonce_field('force_add_mobile_nonce', 'force_add_mobile_nonce'); ?>

        <label class="wpap-field-wrap">
            <span class="wpap-field-label">
                <?php esc_html_e('شماره همراه', 'arvand-panel'); ?>
            </span>

            <input type="text" name="phone" />
        </label>

        <footer>
            <button class="wpap-btn-1" type="submit" name="send_code">
                <span class="wpap-btn-text">
                    <?php esc_attr_e('Send verification code', 'arvand-panel'); ?>
                </span>

                <div class="wpap-loading"></div>
            </button>
        </footer>
    </div>
</form>

<form id="wpap-force-verification-form" class="wpap-auth-form" method="post">
    <div class="wpap-fields">
        <?php wp_nonce_field('force_verification_nonce', 'force_verification_nonce'); ?>

        <label class="wpap-field-wrap">
            <span class="wpap-field-label">
                <?php esc_html_e('کد تایید', 'arvand-panel'); ?>
            </span>

            <input type="text" name="verification_code" />
        </label>

        <footer>
            <button class="wpap-btn-1" type="submit" name="add_mobile_number">
                <span class="wpap-btn-text">
                    <?php esc_attr_e('Verify and add number', 'arvand-panel'); ?>
                </span>

                <div class="wpap-loading"></div>
            </button>
        </footer>
    </div>
</form>