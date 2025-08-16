<?php
defined('ABSPATH') || exit;

$response = \Arvand\ArvandPanel\Admin\Handlers\WPAPAdminHandler::newPrivateMessage();
?>

<form id="wpwp-new-message-form" class="wpap-container" method="post" enctype="multipart/form-data">
    <header>
        <a href="<?php echo esc_url(admin_url('admin.php?page=wpap-private-message')); ?>">
            <i class="ri-arrow-right-line"></i>
            <?php esc_html_e('پیام ها', 'arvand-panel'); ?>
        </a>

        <button name="new_message" type="submit">
            <i class="ri-send-plane-2-line"></i>
            <?php esc_html_e('ارسال پیام', 'arvand-panel'); ?>
        </button>
    </header>

    <div>
        <?php
        if ($response) {
            wpap_admin_notice($response['msg'], $response['ok'] ? 'success' : 'error');
        }

        wp_nonce_field('new_message_nonce', 'new_message_nonce');
        ?>

        <div class="wpap-field-wrap">
            <label><?php esc_html_e('نام کاربری / شناسه (ID) / ایمیل گیرنده پیام', 'arvand-panel'); ?></label>

            <div class="wpap-ajax-field">
                <input type="text" name="user"/>

                <div>
                    <div class="wpap-loading"></div>
                    <ul></ul>
                </div>
            </div>
        </div>

        <div class="wpap-field-wrap">
            <label><?php esc_html_e('Title', 'arvand-panel'); ?></label>
            <input class="regular-text" type="text" name="message_title">
        </div>

        <div class="wpap-field-wrap">
            <label><?php esc_html_e('Message', 'arvand-panel'); ?></label>

            <?php
            wp_editor('', 'wpap-message-content', [
                'wpautop' => true,
                'media_buttons' => true,
                'textarea_name' => 'message_content',
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
                echo sprintf(
                    esc_html__('حداکثر حجم %s می باشد. پسوندهای مجاز: jpg - jpeg - png - pdf - zip', 'arvand-panel'),
                    size_format(1024 * wpap_general_options()['private_msg_attachment_size'])
                );
                ?>
            </p>
        </div>
    </div>
</form>