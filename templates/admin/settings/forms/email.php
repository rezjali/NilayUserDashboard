<?php
defined('ABSPATH') || exit;

$email = wpap_email_options();
?>

<form id="wpap-email-section" class="wpap-settings-section wpap-form" method="post">
    <?php wp_nonce_field('email_nonce', 'email_nonce'); ?>
    <input type="hidden" name="form" value="wpap_email"/>

    <div class="wpap-field-wrap">
        <label for="wpap-reg-email-subject"><?php esc_html_e('Registration notification email subject', 'arvand-panel'); ?></label>
        <input id="wpap-reg-email-subject" class="regular-text" name="reg_email_subject" type="text"
               value="<?php esc_attr_e($email['reg_email_subject']); ?>"/>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-user-reg-email-content"><?php esc_html_e('Registration notification email text', 'arvand-panel'); ?></label>

        <div>
            <?php
            $args = [
                'wpautop' => true,
                'media_buttons' => false,
                'textarea_name' => 'reg_email_content',
                'textarea_rows' => 8,
                'quicktags' => false,
                'tinymce' => [
                    'toolbar1' => 'bold,italic,underline,separator,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo',
                    'toolbar2' => '',
                    'toolbar3' => '',
                ],
            ];

            wp_editor($email['reg_email_content'], 'wpap-user-reg-email-content', $args);
            ?>

            <p class="description">
                <?php esc_html_e('Codes that can be used in the text of the email: ', 'arvand-panel'); ?><br/>

                <code>[first_name]</code>
                <code>[last_name]</code>
                <code>[user_login]</code>
                <code>[user_email]</code>
                <code>[site_name]</code>
                <code>[site_url]</code>
            </p>
        </div>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-activation-email-subject"><?php esc_html_e('Account activation email subject', 'arvand-panel'); ?></label>
        <input id="wpap-activation-email-subject" class="regular-text" name="activation_email_subject" type="text"
               value="<?php echo esc_attr($email['activation_email_subject']); ?>"/>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-activation-email"><?php esc_html_e('Account activation email text', 'arvand-panel'); ?></label>

        <div>
            <?php
            $args = [
                'wpautop' => true,
                'media_buttons' => false,
                'textarea_name' => 'activation_email',
                'textarea_rows' => 8,
                'quicktags' => false,
                'tinymce' => [
                    'toolbar1' => 'bold,italic,underline,separator,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo',
                    'toolbar2' => '',
                    'toolbar3' => '',
                ],
            ];

            wp_editor($email['activation_email'], 'wpap-activation-email', $args);
            ?>

            <p class="description">
                <?php esc_html_e('Codes that can be used in the text of the email: ', 'arvand-panel'); ?><br/>

                <code>[first_name]</code>
                <code>[last_name]</code>
                <code>[user_login]</code>
                <code>[user_email]</code>
                <code>[site_name]</code>
                <code>[site_url]</code>
                <code>[activation_link]</code>
            </p>
        </div>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-admin-approval-email-subject">
            <?php esc_html_e('Admin approval email subject', 'arvand-panel'); ?>
        </label>

        <input id="wpap-admin-approval-email-subject" class="regular-text" name="admin_approval_email_subject"
               type="text" value="<?php echo esc_attr($email['admin_approval_email_subject']); ?>"/>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-admin-approval-email-content">
            <?php esc_html_e('Admin approval email content', 'arvand-panel'); ?>
        </label>

        <div>
            <?php
            $args = [
                'wpautop' => true,
                'media_buttons' => false,
                'textarea_name' => 'admin_approval_email',
                'textarea_rows' => 8,
                'quicktags' => false,
                'tinymce' => [
                    'toolbar1' => 'bold,italic,underline,separator,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo',
                    'toolbar2' => '',
                    'toolbar3' => '',
                ],
            ];

            wp_editor($email['admin_approval_email'], 'wpap-admin-approval-email-content', $args);
            ?>

            <p class="description">
                <?php esc_html_e('Codes that can be used in the text of the email: ', 'arvand-panel'); ?><br/>
                <code>[first_name]</code>
                <code>[last_name]</code>
                <code>[user_login]</code>
                <code>[user_email]</code>
                <code>[site_name]</code>
                <code>[site_url]</code>
            </p>
        </div>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-enable-ticket-email">
            <?php esc_html_e('Enable sending emails for tickets', 'arvand-panel'); ?>
        </label>

        <span class="wpap-checkbox-wrap">
            <label>
                <input id="wpap-enable-ticket-email" name="enable_ticket_email"
                       type="checkbox" <?php checked($email['enable_ticket_email']); ?>/>
                <span class="wpap-checkbox"></span>
            </label>
        </span>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-ticket-reply-email-subject"><?php esc_html_e('Ticket reply notification email subject', 'arvand-panel'); ?></label>
        <input id="wpap-ticket-reply-email-subject" class="regular-text" name="ticket_email_subject" type="text"
               value="<?php echo esc_attr($email['ticket_email_subject']); ?>"/>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-ticket-reply-email-content">
            <?php esc_html_e('Ticket reply notification email text', 'arvand-panel'); ?>
        </label>

        <div>
            <?php
            $args = [
                'media_buttons' => false,
                'textarea_name' => 'ticket_email_content',
                'textarea_rows' => 8,
                'quicktags' => false,
                'tinymce' => [
                    'toolbar1' => 'bold,italic,underline,separator,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo',
                    'toolbar2' => '',
                    'toolbar3' => '',
                ],
            ];

            wp_editor($email['ticket_email_content'], 'wpap-ticket-reply-email-content', $args);
            ?>

            <p class="description">
                <?php esc_html_e('Codes that can be used in the text of the email: ', 'arvand-panel'); ?><br/>
                <code>[site_name]</code>
                <code>[site_url]</code>
                <code>[ticket_title]</code>
            </p>
        </div>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-new-ticket-email-subject">
            <?php esc_html_e('New ticket notification email subject', 'arvand-panel'); ?>
        </label>

        <input id="wpap-new-ticket-email-subject" class="regular-text" name="new_ticket_email_subject" type="text"
               value="<?php echo esc_attr($email['new_ticket_email_subject']); ?>"/>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-new-ticket-email-content">
            <?php esc_html_e('new ticket notification email text', 'arvand-panel'); ?>
        </label>

        <div>
            <?php
            $args = [
                'media_buttons' => false,
                'textarea_name' => 'new_ticket_email_content',
                'textarea_rows' => 8,
                'quicktags' => false,
                'tinymce' => [
                    'toolbar1' => 'bold,italic,underline,separator,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo',
                    'toolbar2' => '',
                    'toolbar3' => '',
                ],
            ];

            wp_editor($email['new_ticket_email_content'], 'wpap-new-ticket-email-content', $args);
            ?>

            <p class="description">
                <?php esc_html_e('Codes that can be used in the text of the email: ', 'arvand-panel'); ?><br/>
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