<?php
defined('ABSPATH') || exit;

$response = \Arvand\ArvandPanel\Admin\Handlers\WPAPAdminTicketHandler::newTicket();
?>

<form class="wpap-container" method="post" enctype="multipart/form-data">
    <header>
        <a href="<?php echo esc_url(admin_url('admin.php?page=wpap-tickets')); ?>" role="button">
            <i class="ri-arrow-right-line"></i>
            <?php esc_html_e('تیکت ها', 'arvand-panel'); ?>
        </a>

        <button type="submit" name="new_ticket">
            <i class="ri-add-line"></i>
            <?php esc_html_e('ایجاد تیکت', 'arvand-panel'); ?>
        </button>
    </header>

    <div>
        <?php
        if ($response) {
            wpap_admin_notice($response['msg'], $response['ok'] ? 'success' : 'error');
        }

        wp_nonce_field('new_ticket', 'new_ticket_nonce');
        ?>

        <div class="wpap-field-wrap">
            <label for="wpap-ticket-title">
                <?php esc_html_e('Title', 'arvand-panel'); ?>
            </label>

            <input id="wpap-ticket-title" class="regular-text" type="text" name="ticket_title">
        </div>

        <div class="wpap-field-wrap">
            <label><?php esc_html_e('نام کاربری / شناسه (ID) / ایمیل گیرنده تیکت', 'arvand-panel'); ?></label>

            <div class="wpap-ajax-field">
                <input type="text" name="user"/>

                <div>
                    <div class="wpap-loading"></div>
                    <ul></ul>
                </div>
            </div>
        </div>

        <div class="wpap-field-wrap">
            <?php
            $department_opt = wpap_ticket_department_options();
            $user_dep = unserialize(get_user_meta(get_current_user_id(), 'wpap_user_ticket_department', true));
            $departments = $user_dep ?: $department_opt['departments'];

            if (!empty($departments)): ?>
                <label for="wpap-ticket-department"><?php esc_html_e('Department', 'arvand-panel'); ?></label>

                <select id="wpap-ticket-department" class="regular-text" name="ticket_department">
                    <?php foreach ($departments as $department): ?>
                        <option value="<?php echo esc_attr($department); ?>">
                            <?php echo esc_html($department); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
        </div>

        <div class="wpap-field-wrap">
            <label for="wpap-ticket-content">
                <?php esc_html_e('Message', 'arvand-panel'); ?>
            </label>

            <?php
            wp_editor('', 'wpap-ticket-content', [
                'wpautop' => true,
                'media_buttons' => true,
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
            <div class="wpap-upload-wrap">
                <div class="wpap-upload-preview">
                    <?php esc_html_e('فایلی انتخاب نشده.', 'arvand-panel'); ?>
                </div>

                <footer>
                    <label class="wpap-upload-btn wpap-btn-2">
                        <input class="wpap-attachment-field" name="wpap_attachment" type="file" hidden/>
                        <?php esc_html_e('بارگذاری ضمیمه', 'arvand-panel'); ?>
                    </label>
                </footer>
            </div>

            <p class="description">
                <?php
                printf(
                    esc_html__('حداکثر حجم %s می باشد. پسوندهای مجاز: jpg - jpeg - png - pdf - zip', 'arvand-panel'),
                    size_format(1024 * wpap_ticket_options()['ticket_attachment_size'])
                );
                ?>
            </p>
        </div>
    </div>
</form>
