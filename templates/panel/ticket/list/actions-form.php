<?php
defined( 'ABSPATH' ) || exit;
?>

<form id="wpap-actions" method="post">
    <?php wp_nonce_field('ticket_list_actions_nonce', 'ticket_list_actions_nonce'); ?>
    <input type="number" name="ticket_number" placeholder="<?php esc_attr_e('Ticket number', 'arvand-panel'); ?>" min="1"/>
    <input type="text" name="ticket_title" placeholder="<?php esc_attr_e('Ticket title', 'arvand-panel'); ?>"/>
    <input type="text" name="ticket_author_user_login" placeholder="<?php esc_attr_e('Creator username', 'arvand-panel'); ?>"/>

    <select name="ticket_status">
        <option value="" selected>
            <span><?php esc_html_e('ُSelect status', 'arvand-panel'); ?></span>
        </option>

        <option value="open">
            <span><?php esc_html_e('Open', 'arvand-panel'); ?></span>
        </option>

        <?php if (count($ticket_status['name'])): ?>
            <?php for ($i = 0; $i < count($ticket_status['name']); $i++): ?>
                <option value="<?php echo esc_attr($ticket_status['name'][$i]); ?>">
                    <?php echo esc_html($ticket_status['name'][$i]); ?>
                </option>
            <?php endfor; ?>
        <?php endif; ?>

        <option value="solved">
            <span><?php esc_html_e('Solved', 'arvand-panel'); ?></span>
        </option>

        <option value="closed">
            <span><?php esc_html_e('Closed', 'arvand-panel'); ?></span>
        </option>
    </select>

    <button class="wpap-btn-1">
        <?php esc_html_e('Apply filter', 'arvand-panel') ?>
    </button>
</form>
