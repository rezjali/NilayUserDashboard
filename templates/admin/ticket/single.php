<?php
defined('ABSPATH') || exit;

$response = \Arvand\ArvandPanel\Admin\Handlers\WPAPAdminTicketHandler::ticketReply($post);
?>

<div id="wpap-single-ticket" class="wpap-container">
    <header>
        <a href="<?php echo esc_url(admin_url('admin.php?page=wpap-tickets')); ?>">
            <i class="ri-arrow-right-line"></i>
            <?php esc_html_e('تیکت ها', 'arvand-panel'); ?>
        </a>

        <a href="<?php echo esc_url(add_query_arg(['section' => 'edit', 'ticket' => $post->ID])); ?>">
            <i class="ri-edit-2-line"></i>
            <?php esc_html_e('ویرایش', 'arvand-panel'); ?>
        </a>

        <a href="<?php echo esc_url(add_query_arg('section', 'new')); ?>">
            <i class="ri-add-line"></i>
            <?php esc_html_e('تیکت جدید', 'arvand-panel'); ?>
        </a>

        <a href="#wpap-reply-form">
            <i class="ri-reply-line"></i>
            <?php esc_html_e('پاسخ', 'arvand-panel'); ?>
        </a>

        <button name="ticket_delete"
                form="wpap-ticket-delete-form"
                onclick="return confirm('<?php esc_attr_e('آیا از حذف دائمی این تیکت مطمئن هستید؟', 'arvand-panel'); ?>')">
            <i class="ri-delete-bin-7-line"></i>
            <?php esc_html_e('حذف', 'arvand-panel'); ?>
        </button>
    </header>

    <div>
        <div id="wpap-ticket-info">
            <?php
            $status = get_post_meta($post->ID, 'wpap_ticket_status', 1);

            if ($status === 'closed') {
                $status_name = __('بسته شده', 'arvand-panel');
            } elseif ($status === 'solved') {
                $status_name = __('حل شده', 'arvand-panel');
            } elseif ($status === 'open') {
                $status_name = __('باز است', 'arvand-panel');
            } else {
                $status_name = $status;
            }

            printf(
                '<span><strong>%s</strong>%s</span>',
                esc_html__('وضعیت: ', 'arvand-panel'), esc_html($status_name)
            );

            $ticket_priority = get_post_meta($post->ID, 'wpap_ticket_priority', 1);

            if ($ticket_priority === 'low') {
                $priority_name = __('کم', 'arvand-panel');
            } elseif ($ticket_priority === 'normal') {
                $priority_name = __('متوسط', 'arvand-panel');
            } else {
                $priority_name = __('بالا', 'arvand-panel');
            }

            printf(
                '<span><strong>%s</strong>%s</span>',
                esc_html__('اولویت: ', 'arvand-panel'), esc_html($priority_name)
            );

            printf(
                '<span><strong>%s</strong>%s</span>',
                esc_html__('بخش پشتیبانی: ', 'arvand-panel'), esc_html($department)
            );

            $recipient = get_post_meta($post->ID, 'wpap_ticket_recipient', 1);
            $user = get_user_by('id', $recipient);

            printf(
                '<span><strong>%s</strong>%s</span>',
                esc_html__('فرسنتده: ', 'arvand-panel'), get_the_author_meta('display_name', $post->post_author)
            );

            if ($user) {
                printf(
                    '<span><strong>%s</strong>%s</span>',
                    esc_html__('گیرنده: ', 'arvand-panel'), esc_html($user->display_name)
                );
            }
            ?>
        </div>

        <article class="wpap-ticket wpap-message-card">
            <div class="wpap-message-card-content">
                <?php
                echo '<strong class="wpap-message-card-title">' . get_the_title($post) . '</strong>';
                echo get_the_content(null, false, $post);
                ?>
            </div>

            <footer>
                <?php
                echo get_avatar($post->post_author, 70);
                echo '<span>' . get_the_author_meta('display_name', $post->post_author) . '</span>';

                printf(
                    '<time>%s</time>',
                    sprintf(
                        esc_html__('%s ساعت %s', 'arvand-panel'),
                        get_the_date('', $post->ID), get_the_time('', $post->ID)
                    )
                );

                printf(
                    '<a href="%s"><i class="bx bx-pencil"></i></a>',
                    esc_url(add_query_arg(['section' => 'edit', 'ticket' => $post->ID]))
                );

                $attachment = get_post_meta($post->ID, 'wpap_ticket_attachment', 1);

                if (is_array($attachment) && !empty($attachment['url'])):
                    $args = [
                        'ticket_attachment_download' => $post->ID,
                        'ticket_attachment_download_nonce' => wp_create_nonce('ticket_attachment_download')
                    ];
                    ?>

                    <a href="<?php echo esc_url(add_query_arg($args)); ?>">
                        <i class="ri-attachment-2"></i>
                    </a>
                <?php endif; ?>

                <!-------- DEPRECATED -------->
                <?php if (!empty($attachment) && is_string($attachment)): ?>
                    <a href="<?php echo esc_url($attachment); ?>" download>
                        <i class="ri-attachment-2"></i>
                    </a>
                <?php endif; ?>
                <!-------- DEPRECATED -------->
            </footer>
        </article>

        <?php
        $query = new WP_Query([
            'post_type' => 'wpap_ticket',
            'post_parent' => $post->ID,
            'posts_per_page' => 20,
            'fields' => 'ids'
        ]);

        if ($query->have_posts()):
            printf('<strong><i class="bx bx-chat"></i>%s</strong>', esc_html__('پاسخ ها', 'arvand-panel'));

            while ($query->have_posts()):
                $query->the_post();
                $author = get_the_author_meta('ID');
                $class = ($post->post_author != $author) ? 'wpap-user-reply' : '';
                ?>

                <article class="wpap-ticket wpap-message-card <?php echo esc_attr($class); ?>">
                    <div class="wpap-message-card-content"><?php the_content(); ?></div>

                    <footer>
                        <?php echo get_avatar(get_the_author_meta('ID'), 50); ?>
                        <span><?php echo get_the_author_meta('login'); ?></span>

                        <?php
                        printf(
                            '<time>%s</time>',
                            sprintf(esc_html__('%s ساعت %s', 'arvand-panel'), get_the_date(), get_the_time())
                        );

                        printf(
                            '<a href="%s"><i class="bx bx-pencil"></i></a>',
                            esc_url(add_query_arg(['section' => 'edit', 'ticket' => get_the_ID()]))
                        );

                        $attachment = get_post_meta(get_the_ID(), 'wpap_ticket_attachment', true);

                        if (is_array($attachment) && !empty($attachment['url'])):
                            $args = [
                                'ticket_attachment_download' => get_the_ID(),
                                'ticket_attachment_download_nonce' => wp_create_nonce('ticket_attachment_download')
                            ];
                            ?>

                            <a href="<?php echo esc_url(add_query_arg($args)); ?>">
                                <i class="ri-attachment-2"></i>
                            </a>
                        <?php endif; ?>

                        <!-------- DEPRECATED -------->
                        <?php if (!empty($attachment) && is_string($attachment)): ?>
                            <a href="<?php echo esc_url($attachment); ?>" download>
                                <i class="ri-attachment-2"></i>
                            </a>
                        <?php endif; ?>
                        <!-------- DEPRECATED -------->
                    </footer>
                </article>
            <?php endwhile;
        endif; ?>
    </div>
</div>

<form id="wpap-reply-form" class="wpap-container" method="post" enctype="multipart/form-data">
    <header>
        <button type="submit" name="ticket_reply">
            <i class="ri-send-plane-2-line"></i>
            <?php esc_html_e('ارسال پاسخ', 'arvand-panel'); ?>
        </button>
    </header>

    <div>
        <?php
        if ($response) {
            wpap_admin_notice($response['msg'], $response['ok'] ? 'success' : 'error');
        }

        wp_nonce_field('ticket_reply', 'ticket_reply_nonce');
        ?>

        <div class="wpap-field-wrap">
            <label for="wpap-ticket-title">
                <?php esc_html_e('پیام', 'arvand-panel'); ?>
            </label>

            <?php
            wp_editor('', 'wpap-ticket-content', [
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
            <label for="wpap-ticket-title">
                <?php esc_html_e('ضمیمه', 'arvand-panel'); ?>
            </label>

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
                    size_format(1024 * wpap_ticket_options()['ticket_attachment_size'])
                );
                ?>
            </p>
        </div>
    </div>
</form>

<form id="wpap-ticket-delete-form" method="post">
    <?php wp_nonce_field('wpap_ticket_delete', 'wpap_ticket_delete_nonce'); ?>
    <input type="text" name="wpap_ticket_delete" value="<?php echo esc_attr($post->ID); ?>" hidden />
</form>