<?php
defined('ABSPATH') || exit;

$general = wpap_general_options();
$response = \Arvand\ArvandPanel\Admin\Handlers\WPAPAdminHandler::editPrivateMessage(compact('post', 'general'));
$post = get_post($post->ID);
?>

<form id="wpwp-new-message-form" class="wpap-container" method="post" enctype="multipart/form-data">
    <header>
        <a href="<?php echo esc_url(admin_url('admin.php?page=wpap-private-message')); ?>">
            <i class="ri-arrow-right-line"></i>
            <?php esc_html_e('پیام ها', 'arvand-panel'); ?>
        </a>

        <a href="<?php echo esc_url(add_query_arg(['section' => 'single', 'msg' => $post->post_parent ?: $post->ID])); ?>">
            <i class="ri-message-3-line"></i>
            <?php esc_html_e('صفحه پیام', 'arvand-panel'); ?>
        </a>

        <button name="private_message_edit" type="submit">
            <i class="ri-edit-2-line"></i>
            <?php esc_html_e('ویرایش پیام', 'arvand-panel'); ?>
        </button>

        <button name="private_message_delete"
                form="wpap-message-delete-form"
                onclick="return confirm('<?php esc_attr_e('آیا از حذف دائمی این پیام مطمئن هستید؟', 'arvand-panel'); ?>')">
            <i class="ri-delete-bin-7-line"></i>
            <?php esc_html_e('حذف پیام', 'arvand-panel'); ?>
        </button>
    </header>

    <div>
        <?php
        if ($response) {
            wpap_admin_notice($response['msg'], $response['ok'] ? 'success' : 'error');
        }

        wp_nonce_field('edit_message', 'edit_message_nonce');
        ?>

        <input type="hidden" name="form" value="wpap_new_message"/>
        <input type="hidden" name="user" value="<?php echo esc_attr($user->ID); ?>"/>

        <div class="wpap-field-wrap">
            <label><?php esc_html_e('ارسال کننده', 'arvand-panel'); ?></label>
            <input type="text" value="<?php echo esc_attr(get_the_author_meta('display_name', $post->post_author)); ?>" readonly>
        </div>

        <div class="wpap-field-wrap">
            <label><?php esc_html_e('گیرنده', 'arvand-panel'); ?></label>
            <input type="text" value="<?php echo esc_attr($user->display_name); ?>" readonly>
        </div>

        <?php if ($post->post_parent == 0): ?>
            <div class="wpap-field-wrap">
                <label for="wpap-message-title"><?php esc_html_e('Title', 'arvand-panel'); ?></label>
                <input id="wpap-message-title" class="regular-text" type="text" name="message_title" value="<?php echo esc_attr($post->post_title); ?>">
            </div>
        <?php endif; ?>

        <div class="wpap-field-wrap">
            <label for="wpap-message-content"><?php esc_html_e('Message', 'arvand-panel'); ?></label>

            <?php
            wp_editor(wp_kses_post($post->post_content), 'wpap-message-content', [
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
            <label for="wpap-message-content"><?php esc_html_e('فایل ضمیمه', 'arvand-panel'); ?></label>

            <div class="wpap-upload-wrap">
                <?php
                $attachment = get_post_meta($post->ID, 'wpap_msg_attachment', true); ?>

                <div class="wpap-upload-preview">
                    <?php
                    if ($attachment) {
                        $ext = pathinfo($attachment['path']);
                        printf(esc_html__('فایل ضمیمنه با پسوند %s بارگذاری شده است.', 'arvand-panel'), $ext['extension']);
                    } else {
                        esc_html_e('فایلی انتخاب نشده.', 'arvand-panel');
                    }
                    ?>
                </div>

                <footer>
                    <label class="wpap-upload-btn wpap-btn-2">
                        <input class="wpap-attachment-field" name="wpap_attachment" type="file" hidden/>
                        <?php esc_html_e('بارگذاری ضمیمه', 'arvand-panel'); ?>
                    </label>

                    <?php
                    if (is_array($attachment) && !empty($attachment['path'])):
                        $args = [
                        'msg_attachment_download' => $post->ID,
                        'msg_attachment_download_nonce' => wp_create_nonce('msg_attachment_download')
                        ];
                        ?>

                        <a class="wpap-btn-2" href="<?php echo esc_url(add_query_arg($args)); ?>">
                            <?php esc_html_e('دانلود', 'arvand-panel'); ?>
                        </a>
                    <?php endif; ?>

                    <!--------- DEPRECATED --------->
                    <?php if ($attachment && is_string($attachment)): ?>
                        <a class="wpap-btn-2" href="<?php echo esc_url($attachment); ?>" download>
                            <?php esc_html_e('دانلود ضمیمه', 'arvand-panel'); ?>
                        </a>
                    <?php endif; ?>
                    <!--------- DEPRECATED --------->

                    <?php if ($attachment): ?>
                        <button class="wpap-btn-2"
                                form="wpap-attachment-delete-form"
                                onclick="return confirm('<?php esc_attr_e('آیا از حذف مطمئن هستید؟', 'arvand-panel'); ?>')">
                            <?php esc_html_e('حذف', 'arvand-panel'); ?>
                        </button>
                    <?php endif; ?>
                </footer>
            </div>

            <p class="description">
                <?php
                echo sprintf(
                    esc_html__('حداکثر حجم %s می باشد. پسوندهای مجاز: jpg - jpeg - png - pdf - zip', 'arvand-panel'),
                    size_format(1024 * $general['private_msg_attachment_size'])
                );
                ?>
            </p>
        </div>
    </div>
</form>

<form id="wpap-attachment-delete-form" method="post">
    <?php wp_nonce_field('wpap_attachment_delete', 'wpap_attachment_delete_nonce'); ?>
    <input type="text" name="attachment_delete" value="<?php echo esc_attr($post->ID); ?>" hidden/>
</form>

<form id="wpap-message-delete-form" method="post">
    <?php wp_nonce_field('wpap_message_delete', 'wpap_message_delete_nonce'); ?>
    <input type="text" name="wpap_private_message_delete" value="<?php echo esc_attr($post->ID); ?>" hidden/>
</form>