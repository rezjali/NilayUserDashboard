<?php
defined('ABSPATH') || exit;

$general = wpap_general_options();
$response = \Arvand\ArvandPanel\Admin\Handlers\WPAPAdminHandler::privateMessageReply(compact('post', 'user', 'general'));
?>

<div class="wpap-container">
    <header>
        <a href="<?php echo esc_url(admin_url('admin.php?page=wpap-private-message')); ?>">
            <<i class="ri-arrow-right-line"></i>
            <?php esc_html_e('پیام ها', 'arvand-panel'); ?>
        </a>

        <a href="<?php echo esc_url(add_query_arg('section', 'new')); ?>">
            <i class="ri-add-line"></i>
            <?php esc_html_e('ارسال پیام جدید', 'arvand-panel'); ?>
        </a>

        <a href="#wpap-reply-form">
            <i class="ri-reply-line"></i>
            <?php esc_html_e('ارسال پاسخ', 'arvand-panel'); ?>
        </a>

        <button name="private_message_delete"
                form="wpap-message-delete-form"
                onclick="return confirm('<?php esc_attr_e('آیا از حذف دائمی این پیام مطمئن هستید؟', 'arvand-panel'); ?>')">
            <i class="ri-delete-bin-7-line"></i>
            <?php esc_html_e('حذف پیام', 'arvand-panel'); ?>
        </button>
    </header>

    <div id="wpap-message-wrap">
        <article id="wpap-private-message" class="wpap-message-card">
            <div class="wpap-message-card-content">
                <?php
                if (!empty(get_the_title($post->ID))) {
                    echo '<strong class="wpap-message-card-title">' . get_the_title($post->ID) . '</strong>';
                }

                echo get_the_content('', '', $post->ID);
                ?>
            </div>

            <footer>
                <?php
                echo get_avatar($post->post_author);
                echo '<span>' . get_the_author_meta('display_name', $post->post_author) . '</span>';
                ?>

                <time>
                    <i class="bx bx-clock"></i>

                    <?php
                    printf(
                        esc_html__('%s ساعت %s', 'arvand-panel'),
                        get_the_date('', $post->ID),
                        get_the_time('', $post->ID)
                    );
                    ?>
                </time>

                <a class="wpap-message-edit-link" href="<?php echo esc_url(add_query_arg(['section' => 'edit', 'msg' => $post->ID])); ?>">
                    <i class="ri-pencil-line"></i>
                </a>

                <?php
                $attachment = get_post_meta($post->ID, 'wpap_msg_attachment', true);

                if (is_array($attachment) && !empty($attachment['path'])):
                    $args = [
                        'msg_attachment_download' => $post->ID,
                        'msg_attachment_download_nonce' => wp_create_nonce('msg_attachment_download')
                    ];
                    ?>

                    <a class="wpap-attachment-file" href="<?php echo esc_url(add_query_arg($args)); ?>">
                        <i class="ri-attachment-2"></i>
                    </a>
                <?php endif; ?>
            </footer>
        </article>

        <?php
        $query = new WP_Query([
            'post_type' => 'wpap_private_message',
            'post_parent' => $post->ID,
            'post_status' => 'publish',
            'posts_per_page' => 20
        ]);

        if ($query->have_posts()):
            printf('<strong><i class="ri-chat-1-line"></i>%s</strong>', esc_html__('پاسخ ها', 'arvand-panel'));

            while ($query->have_posts()):
                $query->the_post();
                $author = get_the_author_meta('ID');
                $class = ($post->post_author != $author) ? 'wpap-user-reply' : ''; ?>

                <article class="wpap-private-message-reply wpap-message-card <?php echo esc_attr($class); ?>">
                    <?php
                    if (0 == get_post_meta(get_the_ID(), 'wpap_admin_seen', 1)) {
                        update_post_meta(get_the_ID(), 'wpap_admin_seen', 1);
                    }
                    ?>

                    <div class="wpap-message-card-content"><?php the_content(); ?></div>

                    <footer>
                        <?php
                        echo get_avatar($author);
                        echo '<span>' . get_the_author_meta('display_name', $author) . '</span>';
                        ?>

                        <time>
                            <i class="ri-time-line"></i>
                            <?php printf(esc_html__('%s ساعت %s', 'arvand-panel'), get_the_date(), get_the_time()); ?>
                        </time>

                        <a class="wpap-message-edit-link" href="<?php echo esc_url(add_query_arg(['section' => 'edit', 'msg' => get_the_ID()])); ?>">
                            <i class="ri-pencil-line"></i>
                        </a>

                        <?php
                        $attachment = get_post_meta(get_the_ID(), 'wpap_msg_attachment', true);

                        if (is_array($attachment) && !empty($attachment['path'])):
                            $args = [
                                'msg_attachment_download' => get_the_ID(),
                                'msg_attachment_download_nonce' => wp_create_nonce('msg_attachment_download')
                            ];
                            ?>

                            <a class="wpap-attachment-file" href="<?php echo esc_url(add_query_arg($args)); ?>">
                                <i class="ri-attachment-2"></i>
                            </a>
                        <?php endif; ?>

                        <!--------- DEPRECATED --------->
                        <?php if ($attachment && is_string($attachment)): ?>
                            <a class="wpap-attachment-file" href="<?php echo esc_url($attachment); ?>" download>
                                <i class="ri-attachment-2"></i>
                            </a>
                        <?php endif; ?>
                        <!--------- DEPRECATED --------->
                    </footer>
                </article>
            <?php endwhile;
        endif; ?>
    </div>
</div>

<form id="wpap-reply-form" class="wpap-container" method="post" enctype="multipart/form-data">
    <header>
        <button type="submit" name="private_message_reply">
            <i class="ri-send-plane-2-line"></i>
            <?php esc_html_e('ارسال پاسخ جدید', 'arvand-panel'); ?>
        </button>
    </header>

    <div>
        <?php
        if ($response) {
            wpap_admin_notice($response['msg'], $response['ok'] ? 'success' : 'error');
        }

        wp_nonce_field('private_msg_nonce', 'private_msg_nonce');
        ?>

        <div class="wpap-field-wrap">
            <?php
            wp_editor('', 'wpap-private-message-content', [
                'media_buttons' => false,
                'textarea_name' => 'private_message_content',
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
                    size_format(1024 * $general['private_msg_attachment_size'])
                );
                ?>
            </p>
        </div>
    </div>
</form>

<form id="wpap-message-delete-form" method="post">
    <?php wp_nonce_field('wpap_message_delete', 'wpap_message_delete_nonce'); ?>
    <input type="text" name="wpap_private_message_delete" value="<?php echo esc_attr($post->ID); ?>" hidden />
</form>