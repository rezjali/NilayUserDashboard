<?php
defined('ABSPATH') || exit;

$opt = wpap_sms_provider_options('parsgreen');
?>

<form id="wpap-parsgreen" class="wpap-form wpap-sms-settings-form" method="post"
      style="<?php echo $sms['provider'] === 'parsgreen' ? 'display: block' : ''; ?>">
    <input type="hidden" name="sms_nonce" value="<?php echo wp_create_nonce('sms_nonce'); ?>"/>
    <input type="hidden" name="form" value="wpap_parsgreen"/>
    <input type="hidden" name="provider_name" value="parsgreen"/>

    <div class="wpap-field-wrap">
        <label><?php esc_html_e('API key', 'arvand-panel'); ?></label>
        <input type="text" name="api_key" value="<?php echo esc_attr($opt['api_key']); ?>"/>
    </div>

    <footer>
        <?php require WPAP_ADMIN_TEMPLATES_PATH . 'parts/button.php'; ?>
    </footer>
</form>