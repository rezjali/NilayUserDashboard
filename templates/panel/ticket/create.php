<?php
defined('ABSPATH') || exit;

$department_opt = wpap_ticket_department_options();
?>

<div id="wpap-new-ticket">
    <div class="wpap-form-wrap">
        <form id="wpap-new-ticket-form" method="post" enctype="multipart/form-data">
            <header>
                <h2><?php esc_html_e('ارسال تیکت', 'arvand-panel'); ?></h2>
            </header>

            <div>
                <?php wp_nonce_field('new_ticket', 'new_ticket_nonce'); ?>
                <input type="hidden" name="action" value="send_ticket"/>

                <div class="wpap-field-wrap">
                    <label class="wpap-field-label" for="wpap-ticket-title-field">
                        <?php esc_html_e('عنوان تیکت', 'arvand-panel'); ?>
                    </label>

                    <input id="wpap-ticket-title-field" type="text" name="ticket_subject"/>
                </div>

                <div class="wpap-field-wrap">
                    <label class="wpap-field-label" for="wpap-ticket-priority-field">
                        <?php esc_html_e('Priority', 'arvand-panel'); ?>
                    </label>

                    <select id="wpap-ticket-priority-field" name="ticket_priority">
                        <option value="low"><?php esc_html_e('Low', 'arvand-panel'); ?></option>
                        <option value="normal" selected><?php _e('Normal', 'arvand-panel'); ?></option>
                        <option value="high"><?php esc_html_e('High', 'arvand-panel'); ?></option>
                    </select>
                </div>

                <?php if (!empty($department_opt['departments'])): ?>
                    <div class="wpap-field-wrap">
                        <label class="wpap-field-label" for="wpap-ticket-department-field">
                            <?php esc_html_e('Department', 'arvand-panel'); ?>
                        </label>

                        <select id="wpap-ticket-department-field" name="ticket_department">
                            <?php foreach ($department_opt['departments'] as $dep): ?>
                                <option value="<?php echo esc_attr($dep); ?>">
                                    <?php echo esc_html($dep); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="wpap-field-wrap">
                    <label class="wpap-field-label">
                        <?php esc_html_e('Your Message', 'arvand-panel'); ?>
                    </label>

                    <?php
                    wp_editor('', 'wpap-ticket-content', [
                        'media_buttons' => false,
                        'textarea_name' => 'ticket_content',
                        'textarea_rows' => 5,
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
                        <span class="wpap-btn-text"><?php esc_attr_e('Send Message', 'arvand-panel'); ?></span>
                        <div class="wpap-loading"></div>
                    </button>
                </footer>
            </div>
        </form>
    </div>
</div>