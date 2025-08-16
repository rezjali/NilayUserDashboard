<?php
defined('ABSPATH') || exit;

$sms = wpap_sms_options();
?>

<div class="wpap-wrap wrap">
    <div id="wpap-sms-providers-settings">
        <div class="wpap-field-wrap">
            <label><?php esc_html_e('سامانه مورد استفاده', 'arvand-panel') ?></label>

            <?php
            $providers = \Arvand\ArvandPanel\SMS\WPAPSMS::supportedGateways();
            echo '<strong id="wpap-current-provider">' . esc_html($providers[$sms['provider']]) . '</strong>';
            ?>
        </div>

        <div id="wpap-provider-settings-select" class="wpap-field-wrap">
            <label for="wpap-sms-providers">
                <?php esc_html_e('Select the sms provider', 'arvand-panel'); ?>
            </label>

            <select id="wpap-sms-providers" name="sms_provider">
                <?php foreach ($providers as $name => $display_name): ?>
                    <option value="<?php echo esc_attr($name); ?>" <?php selected($sms['provider'], $name); ?>>
                        <?php echo $display_name; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php
        foreach (array_keys($providers) as $provider) {
            $provider = str_replace('_', '-', $provider);
            require WPAP_ADMIN_TEMPLATES_PATH . "settings/forms/sms-providers/$provider.php";
        }
        ?>
    </div>
</div>