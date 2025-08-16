<?php
defined('ABSPATH') || exit;

$response = \Arvand\ArvandPanel\Admin\Handlers\WPAPAdminTicketHandler::editTicket($post->ID);
$post = get_post($post->ID);
?>

<form class="wpap-container" method="post" enctype="multipart/form-data">
    <header>
        <a href="<?php echo esc_url(admin_url('admin.php?page=wpap-tickets')); ?>" role="button">
            <i class="ri-arrow-right-line"></i>
            <?php esc_html_e('تیکت ها', 'arvand-panel'); ?>
        </a>

        <a href="<?php echo esc_url(add_query_arg(['section' => 'single', 'ticket' => $post->post_parent ?: $post->ID])); ?>">
            <i class="ri-coupon-line"></i>
            <?php esc_html_e('صفحه تیکت', 'arvand-panel'); ?>
        </a>

        <button type="submit" name="edit_ticket">
            <i class="ri-save-line"></i>
            <?php esc_html_e('ذخیره تغییرات', 'arvand-panel'); ?>
        </button>

        <button name="ticket_delete"
                form="wpap-ticket-delete-form"
                onclick="return confirm('<?php esc_attr_e('آیا از حذف دائمی این تیکت مطمئن هستید؟', 'arvand-panel'); ?>')">
            <i class="ri-delete-bin-7-line"></i>
            <?php esc_html_e('حذف تیکت', 'arvand-panel'); ?>
        </button>
    </header>

    <div>
        <?php
        if ($response) {
            wpap_admin_notice($response['msg'], $response['ok'] ? 'success' : 'error');
        }

        wp_nonce_field('edit_ticket', 'edit_ticket_nonce');

        if (0 == $post->post_parent): ?>
            <div class="wpap-field-wrap">
                <label for="wpap-ticket-title">
                    <?php esc_html_e('Title', 'arvand-panel'); ?>
                </label>

                <input id="wpap-ticket-title" class="regular-text" type="text" name="ticket_title" value="<?php echo esc_attr($post->post_title); ?>">
            </div>
        <?php endif; ?>

        <div class="wpap-field-wrap">
            <label><?php esc_html_e('ارسال کننده', 'arvand-panel'); ?></label>
            <input type="text" value="<?php echo esc_attr(get_the_author_meta('display_name', $post->post_author)); ?>" readonly>
        </div>

        <?php
        $recipient = get_post_meta($post->ID, 'wpap_ticket_recipient', 1);
        $user = get_user_by('id', $recipient);

        if ($user): ?>
            <div class="wpap-field-wrap">
                <label><?php esc_html_e('گیرنده', 'arvand-panel'); ?></label>
                <input type="text" value="<?php echo esc_attr($user->display_name); ?>" readonly>
            </div>
        <?php endif;

        if (0 == $post->post_parent): ?>
            <div class="wpap-field-wrap">
                <?php
                $department_opt = wpap_ticket_department_options();
                $user_dep = unserialize(get_user_meta(get_current_user_id(), 'wpap_user_ticket_department', 1));
                $departments = $user_dep ?: $department_opt['departments'];
                $ticket_dep = get_post_meta($post->ID, 'wpap_ticket_department', 1);

                if (!empty($departments)): ?>
                    <label for="wpap-ticket-department"><?php esc_html_e('Department', 'arvand-panel'); ?></label>

                    <select id="wpap-ticket-department" class="regular-text" name="ticket_department">
                        <?php foreach ($departments as $department): ?>
                            <option value="<?php echo esc_attr($department); ?>" <?php selected($ticket_dep, $department); ?>>
                                <?php echo esc_html($department); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
            </div>

            <div class="wpap-field-wrap">
                <label><?php esc_html_e('وضعیت', 'arvand-panel'); ?></label>

                <select name="status">
                    <?php $status = get_post_meta($post->ID, 'wpap_ticket_status', 1); ?>

                    <option value="closed" <?php selected($status, 'closed'); ?>>
                        <?php esc_html_e('Closed', 'arvand-panel'); ?>
                    </option>

                    <option value="open" <?php selected($status, 'open'); ?>>
                        <?php esc_html_e('Open', 'arvand-panel'); ?>
                    </option>

                    <option value="solved" <?php selected($status, 'solved'); ?>>
                        <?php esc_html_e('Solved', 'arvand-panel'); ?>
                    </option>

                    <?php
                    $ticket_opt = wpap_ticket_options();
                    $status_names = $ticket_opt['ticket_status']['name'];

                    if (!empty($status_names)): ?>
                        <?php for ($i = 0; $i < count($status_names); $i++): ?>
                            <option value="<?php echo esc_attr($status_names[$i]); ?>" <?php selected($status, $status_names[$i]); ?>>
                                <?php echo esc_html($status_names[$i]); ?>
                            </option>
                        <?php endfor;
                    endif; ?>
                </select>
            </div>
        <?php endif; ?>

        <div class="wpap-field-wrap">
            <label for="wpap-ticket-content">
                <?php esc_html_e('Message', 'arvand-panel'); ?>
            </label>

            <?php
            wp_editor(wp_kses_post($post->post_content), 'wpap-ticket-content', [
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
            <label for="wpap-message-content"><?php esc_html_e('فایل ضمیمه', 'arvand-panel'); ?></label>

            <div class="wpap-upload-wrap">
                <?php
                $attachment = get_post_meta($post->ID, 'wpap_ticket_attachment', true); ?>

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
                            'ticket_attachment_download' => $post->ID,
                            'ticket_attachment_download_nonce' => wp_create_nonce('ticket_attachment_download')
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
                    size_format(1024 * wpap_ticket_options()['ticket_attachment_size'])
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

<form id="wpap-ticket-delete-form" method="post">
    <?php wp_nonce_field('wpap_ticket_delete', 'wpap_ticket_delete_nonce'); ?>
    <input type="text" name="wpap_ticket_delete" value="<?php echo esc_attr($post->ID); ?>" hidden/>
</form>