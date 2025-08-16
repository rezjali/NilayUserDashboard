<?php
defined('ABSPATH') || exit;

$opt = wpap_sms_provider_options('raygansms');
?>

<form id="wpap-raygansms" class="wpap-form wpap-sms-settings-form" method="post"
      style="<?php echo $sms['provider'] === 'raygansms' ? 'display: block' : ''; ?>">
    <input type="hidden" name="sms_nonce" value="<?php echo wp_create_nonce('sms_nonce'); ?>"/>
    <input type="hidden" name="form" value="wpap_raygansms"/>
    <input type="hidden" name="provider_name" value="raygansms"/>

    <div class="wpap-field-wrap">
        <label><?php esc_html_e('Your sms provider username', 'arvand-panel'); ?></label>
        <input type="text" name="username" value="<?php echo esc_attr($opt['username']); ?>"/>
    </div>

    <div class="wpap-field-wrap">
        <label><?php esc_html_e('Your sms provider password', 'arvand-panel'); ?></label>
        <input type="password" name="password" value="<?php echo esc_attr($opt['password']); ?>"/>
    </div>

    <div class="wpap-field-wrap">
        <label><?php esc_html_e('From', 'arvand-panel'); ?></label>

        <div>
            <input type="text" name="from" value="<?php echo esc_attr($opt['from']); ?>"/>
            <p class="description"><?php esc_html_e('Your sms service number.', 'arvand-panel'); ?></p>
        </div>
    </div>

    <div class="wpap-field-wrap">
        <label><?php esc_html_e('SMS message', 'arvand-panel'); ?></label>

        <div>
            <textarea class="regular-text" name="text" rows="4"><?php echo esc_attr($opt['text']); ?></textarea>
            <p class="description"><?php esc_html_e('جهت استفاده در پیامک های ورود و ثبت نام.', 'arvand-panel'); ?></p>

            <p class="description">
                <?php esc_html_e('کداعتبارسنجی: ', 'arvand-panel'); ?>
                <code>[verification_code]</code><br/>
                <?php esc_html_e('نام سایت: ', 'arvand-panel'); ?>
                <code>[site_name]</code>
            </p>
        </div>
    </div>

    <footer>
        <?php require WPAP_ADMIN_TEMPLATES_PATH . 'parts/button.php'; ?>
    </footer>
</form>