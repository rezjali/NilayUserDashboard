<?php defined( 'ABSPATH' ) || exit; ?>

<?php if (!in_array($status, ['closed', 'solved'])): ?>
    <div class="wpap-chat-reply-form-wrap wpap-form-wrap">
        <form id="wpap-ticket-reply-form" method="post" enctype="multipart/form-data">
            <header>
                <h2><?php esc_html_e('ارسال پاسخ', 'arvand-panel'); ?></h2>
            </header>

            <div>
                <?php wp_nonce_field('ticket_reply', 'ticket_reply_nonce'); ?>
                <input type="hidden" name="action" value="send_ticket_reply"/>
                <input type="hidden" name="post" value="<?php echo esc_attr($main_ticket->ID); ?>"/>
                <input type="hidden" name="recipient" value="<?php echo esc_attr($recipient); ?>"/>

                <?php if ($user_dep): ?>
                    <div class="wpap-field-wrap">
                        <label class="wpap-field-label" for="wpap-status-field">
                            <?php esc_html_e('وضعیت تیکت', 'arvand-panel'); ?>
                        </label>

                        <select id="wpap-status-field" name="ticket_status">
                            <option value="-1" selected>
                                <?php esc_html_e('Select ticket status', 'arvand-panel'); ?>
                            </option>

                            <option value="open">
                                <?php esc_html_e('Open', 'arvand-panel'); ?>
                            </option>

                            <?php
                            $ticket_opt = wpap_ticket_options();
                            $ticket_status = $ticket_opt['ticket_status'];
                            ?>

                            <?php for ($i = 0; $i < count($ticket_status['name']); $i++): ?>
                                <option value="<?php esc_attr_e($ticket_status['name'][$i]); ?>">
                                    <?php esc_html_e($ticket_status['name'][$i]); ?>
                                </option>
                            <?php endfor; ?>

                            <option value="solved">
                                <?php esc_html_e('Solved', 'arvand-panel'); ?>
                            </option>

                            <option value="closed">
                                <?php esc_html_e('Closed', 'arvand-panel'); ?>
                            </option>
                        </select>
                    </div>
                <?php else: ?>
                    <div class="wpap-field-wrap">
                        <label class="wpap-field-label" for="wpap-status-field">
                            <?php esc_html_e('وضعیت تیکت', 'arvand-panel'); ?>
                        </label>

                        <select id="wpap-status-field" name="ticket_status">
                            <option value="-1" selected>
                                <?php esc_html_e('Select ticket status', 'arvand-panel'); ?>
                            </option>

                            <option value="open">
                                <?php esc_html_e('I\'m waiting for replay.', 'arvand-panel'); ?>
                            </option>

                            <option value="solved">
                                <?php esc_html_e('Ticket solved.', 'arvand-panel'); ?>
                            </option>

                            <option value="closed">
                                <?php esc_html_e('Close the ticket.', 'arvand-panel'); ?>
                            </option>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="wpap-field-wrap">
                    <label class="wpap-field-label">
                        <?php esc_html_e('پاسخ خود را بنویسید', 'arvand-panel'); ?>
                    </label>

                    <?php
                    wp_editor('', 'wpap-ticket-content', [
                        'media_buttons' => false,
                        'textarea_name' => 'ticket_content',
                        'textarea_rows' => 8,
                        'quicktags' => false,
                        'tinymce' => [
                            'toolbar1' => 'bold,italic,underline,separator,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo',
                            'toolbar2' => '',
                            'toolbar3' => '',
                        ],
                    ]);
                    ?>
                </div>

                <div class="wpap-field-wrap">
                    <label class="wpap-field-label">
                        <?php esc_html_e('فایل ضمیمه', 'arvand-panel'); ?>
                    </label>

                    <div class="wpap-upload-attachment">
                        <header></header>

                        <footer>
                            <label class="wpap-btn-1">
                                <input type="file" name="wpap_attachment" hidden/>
                                <i class="ri-upload-line"></i>
                                <span><?php esc_html_e('آپلود', 'arvand-panel'); ?></span>
                            </label>
                        </footer>
                    </div>

                    <span class="wpap-input-info">
                        <?php
                        printf(
                            esc_html__('حداکثر حجم %s می باشد. پسوندهای مجاز: jpg - jpeg - png - pdf - zip', 'arvand-panel'),
                            size_format(1024 * wpap_ticket_options()['ticket_attachment_size'])
                        );
                        ?>
                    </span>
                </div>

                <footer>
                    <button class="wpap-btn-1" type="submit">
                        <span class="wpap-btn-text"><?php esc_html_e('Send Reply', 'arvand-panel'); ?></span>
                        <div class="wpap-loading"></div>
                    </button>
                </footer>
            </div>
        </form>
    </div>
<?php endif; ?>
