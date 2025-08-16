<?php defined('ABSPATH') || exit; ?>

<form class="wpap-form" method="post">
    <?php wp_nonce_field('ticket_department_nonce', 'ticket_department_nonce'); ?>
    <input type="hidden" name="form" value="wpap_add_supporter"/>

    <div class="wpap-field-wrap">
        <label for="wpap-dep-responsible"><?php esc_html_e('responsible username', 'arvand-panel'); ?></label>
        <input id="wpap-dep-responsible" class="regular-text" type="text" name="responsible"/>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-responsible-for"><?php esc_html_e('responsible for', 'arvand-panel'); ?></label>

        <select id="wpap-responsible-for" class="regular-text" name="responsible_for[]" multiple>
            <?php
            $td = wpap_ticket_department_options();
            $dep = $td['departments'];
            ?>

            <?php for ($i = 0; $i < count($dep); $i++): ?>
                <option><?php echo esc_html($dep[$i]); ?></option>
            <?php endfor; ?>
        </select>
    </div>

    <footer>
        <?php
        $text = __('Add', 'arvand-panel');
        $success_text = __('Added', 'arvand-panel');
        require WPAP_ADMIN_TEMPLATES_PATH . 'parts/button.php';
        ?>
    </footer>
</form>