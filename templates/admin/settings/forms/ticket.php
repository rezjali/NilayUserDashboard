<?php
defined('ABSPATH') || exit;

$ticket = wpap_ticket_options();
$status = $ticket['ticket_status'];
?>

<form id="wpap-ticket-section" class="wpap-settings-section wpap-form" method="post">
    <?php wp_nonce_field('ticket_nonce', 'ticket_nonce'); ?>
    <input type="hidden" name="form" value="wpap_ticket"/>

    <div class="wpap-field-wrap">
        <label for="wpap-ticket-department">
            <?php esc_html_e('Tickets status (name and color status)', 'arvand-panel'); ?>
        </label>

        <div>
            <div id="wpap-ticket-status-input-wrap">
                <?php if (!$status || !count($status['name'])): ?>
                    <div class="wpap-ticket-settings-status">
                        <input id="wpap-ticket-status" type="text" name="ticket_status_name[]"
                               placeholder="<?php esc_attr_e('Enter status name.', 'arvand-panel') ?>"/>

                        <div class="wpap-status-color-input-wrap">
                            <label title="<?php esc_attr_e('Background color', 'arvand-panel'); ?>">
                                <i class="ri-square-line"></i>
                                <input type="color" name="ticket_status_color[]"/>
                            </label>

                            <label title="<?php esc_attr_e('Text color', 'arvand-panel'); ?>">
                                <i class="ri-text"></i>
                                <input type="color" name="ticket_status_text_color[]"/>
                            </label>
                        </div>
                    </div>
                <?php endif;

                for ($i = 0; $i < count($status['name']); $i++): ?>
                    <div class="wpap-ticket-settings-status">
                        <input id="wpap-ticket-status" type="text" name="ticket_status_name[]"
                               value="<?php echo esc_attr($status['name'][$i]); ?>"
                               placeholder="<?php esc_attr_e('Enter status name.', 'arvand-panel'); ?>"/>

                        <div class="wpap-status-color-input-wrap">
                            <label title="<?php esc_attr_e('Background color', 'arvand-panel'); ?>">
                                <i class="ri-square-line"></i>
                                <input type="color" name="ticket_status_color[]"
                                       value="<?php echo esc_attr($status['color'][$i]); ?>"/>
                            </label>

                            <label title="<?php esc_attr_e('Text color', 'arvand-panel'); ?>">
                                <i class="ri-text"></i>
                                <input type="color" name="ticket_status_text_color[]"
                                       value="<?php echo esc_attr($status['text_color'][$i]); ?>"/>
                            </label>
                        </div>

                        <a class="wpap-delete-status" href=""><i class="bx bx-trash"></i></a>
                    </div>
                <?php endfor; ?>
            </div>

            <a id="wpap-add-status" class="wpap-btn-2" href="#">
                <i class="bx bx-plus"></i>
                <?php esc_html_e('Add', 'arvand-panel'); ?>
            </a>
        </div>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-ticket-attachment-size">
            <?php esc_html_e('Ticket attachment size in kilobytes', 'arvand-panel'); ?>
        </label>

        <input id="wpap-ticket-attachment-size" class="small-text" type="number" name="ticket_attachment_size"
               value="<?php echo esc_attr($ticket['ticket_attachment_size']); ?>"
               min="100" <?php checked($ticket['ticket_attachment_size']); ?>/>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-tickets-per-page"><?php esc_html_e('Tickets per page', 'arvand-panel'); ?></label>
        <input id="wpap-tickets-per-page" class="small-text" type="number" name="tickets_per_page" min="1"
               value="<?php echo esc_attr($ticket['tickets_per_page']); ?>"/>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-ticket-replies-per-page"><?php esc_html_e('Ticket replies per page', 'arvand-panel'); ?></label>
        <input id="wpap-ticket-replies-per-page" class="small-text" type="number" name="ticket_replies_per_page" min="1"
               value="<?php echo esc_attr($ticket['ticket_replies_per_page']); ?>"/>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-enable-ticket-sms"><?php esc_html_e('Enable ticket SMS notification', 'arvand-panel'); ?></label>

        <span class="wpap-checkbox-wrap">
            <label>
                <input id="wpap-enable-ticket-sms" name="enable_ticket_sms"
                       type="checkbox" <?php checked($ticket['enable_ticket_sms']); ?>/>
                <span class="wpap-checkbox"></span>
            </label>
        </span>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-ticket-reply-sms-text">
            <?php esc_html_e('Ticket reply notification sms text', 'arvand-panel'); ?>
        </label>

        <div>
            <textarea id="wpap-ticket-reply-sms-text" class="regular-text" name="ticket_sms_text"
                      rows="4"><?php echo esc_attr($ticket['ticket_sms_text']); ?></textarea>

            <p class="description">
                <?php esc_html_e('Codes that can be used in the SMS text: ', 'arvand-panel'); ?><br/>
                <code>[site_name]</code>
                <code>[site_url]</code>
                <code>[ticket_title]</code>
            </p>
        </div>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-new-ticket-sms-text">
            <?php esc_html_e('New ticket notification sms text', 'arvand-panel'); ?>
        </label>

        <div>
            <textarea id="wpap-new-ticket-sms-text" class="regular-text" name="new_ticket_sms_text"
                      rows="4"><?php echo esc_attr($ticket['new_ticket_sms_text']); ?></textarea>

            <p class="description">
                <?php esc_html_e('Codes that can be used in the SMS text: ', 'arvand-panel'); ?><br/>
                <code>[site_name]</code>
                <code>[site_url]</code>
                <code>[ticket_title]</code>
            </p>
        </div>
    </div>

    <footer>
        <?php require WPAP_ADMIN_TEMPLATES_PATH . 'parts/button.php'; ?>
    </footer>
</form>