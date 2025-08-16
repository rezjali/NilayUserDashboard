<?php defined('ABSPATH') || exit; ?>

<button class="wpap-settings-submit-btn wpap-btn-1" type="submit">
    <span class="wpap-btn-text">
        <?php echo !empty($text) ? $text : esc_html__('Save Settings', 'arvand-panel'); ?>
    </span>

    <span class="wpap-success-btn-text">
        <i class="ri-checkbox-circle-line"></i>
        <?php echo !empty($success_text) ? $success_text : esc_html__('Changed', 'arvand-panel'); ?>
    </span>

    <div class="wpap-loading"></div>
</button>