<?php
defined('ABSPATH') || exit;

$google_opt = wpap_google_options();
?>

<?php do_action('wpap_before_register_form'); ?>

<form id="wpap-register-form" class="wpap-auth-form" method="post">
    <div class="wpap-fields">
        <?php wp_nonce_field('register_nonce', 'wpap_register_nonce'); ?>
        <input type="hidden" name="redirect_to" value="<?php echo get_permalink($pages_opt['after_register_page_id']); ?>"/>

        <?php
        foreach (\Arvand\ArvandPanel\Form\WPAPFieldSettings::get() as $field_settings) {
            if (!in_array($field_settings['display'], ['register', 'both'])) {
                continue;
            }

            $field_class = 'Arvand\ArvandPanel\Form\Fields\wpap_field_' . sanitize_text_field($field_settings['field_name']);

            if (class_exists($field_class)) {
                call_user_func([new $field_class, 'output'], $field_settings);
            }
        }
        ?>

        <?php do_action('wpap_register_fields'); ?>

        <?php if ($register['enable_agree']): ?>
            <div class="wpap-field-wrap">
                <span id="wpap-agree" class="wpap-checkbox-wrap">
                    <label for="wpap-agree-input">
                        <input id="wpap-agree-input" type="checkbox" name="agree"/>
                        <span class="wpap-checkbox"></span>
                        <span id="wpap-agree-text"><?php echo wp_kses($register['agree_text'], 'post'); ?></span>
                    </label>
                </span>
            </div>
        <?php endif; ?>

        <?php if ($google_opt['enable_recaptcha'] and !empty($google_opt['recaptcha_site_key'])): ?>
            <script src="https://www.google.com/recaptcha/api.js?hl=fa" async defer></script>
            <div class="g-recaptcha" data-sitekey="<?php echo esc_attr($google_opt['recaptcha_site_key']); ?>"></div>
        <?php endif; ?>

        <footer>
            <button class="wpap-btn-1" type="submit">
                <span class="wpap-btn-text"><?php esc_attr_e('register', 'arvand-panel'); ?></span>
                <div class="wpap-loading"></div>
            </button>
        </footer>
    </div>
</form>

<?php do_action('wpap_after_register_form'); ?>