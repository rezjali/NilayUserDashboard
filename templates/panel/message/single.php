<?php
defined('ABSPATH') || exit;

global $current_user;

if ($current_user->user_login !== WPAP_DEMO) {
    update_post_meta($message->ID, 'wpap_seen', 1);
}

$attachment = get_post_meta($message->ID, 'wpap_msg_attachment', 1);

$panel_page = isset($_GET['panel-page']) ? absint($_GET['panel-page']) : 1;
$limit = 20;
$offset = ($panel_page * $limit) - $limit;

$replies_query = new WP_Query([
    'post_type' => 'wpap_private_message',
    'post_parent' => $message->ID,
    'posts_per_page' => $limit,
    'post_status' => 'publish',
    'paged' => $panel_page,
    'offset' => $offset,
    'order' => 'ASC'
]);
?>

<div id="wpap-single-msg" class="wpap-chat-wrap">
    <header>
        <h1><?php echo esc_html($message->post_title); ?></h1>

        <div>
            <span class="wpap-sender-name">
                <?php echo get_the_author_meta('display_name', $message->post_author); ?>
            </span>

            <span>
                <?php
                printf(
                    esc_html__('%s ساعت %s', 'arvand-panel'),
                    get_the_date('', $message),
                    get_the_time('', $message)
                );
                ?>
            </span>
        </div>
    </header>

    <div id="wpap-msg-replies" class="wpap-chat-replies">
        <article class="wpap-chat wpap-chat-sender">
            <div class="wpap-chat-content wpap-post-content">
                <?php echo wp_kses_post(wpautop($message->post_content)); ?>
            </div>

            <footer>
                <div class="wpap-chat-author">
                    <?php
                    echo get_avatar($message->post_author, '30');
                    echo esc_html(get_the_author_meta('display_name', $message->post_author));
                    ?>
                </div>

                <?php if (!empty($attachment['url'])): ?>
                    <?php
                    $args = [
                        'msg_attachment_download' => $message->ID,
                        'msg_attachment_download_nonce' => wp_create_nonce('msg_attachment_download')
                    ];
                    ?>

                    <a class="wpap-chat-attachment-file" href="<?php echo esc_url(add_query_arg($args)); ?>">
                        <i class="ri-attachment-2"></i>
                        <?php esc_html_e('فایل ضمیمه', 'arvand-panel'); ?>
                    </a>
                <?php endif; ?>

                <time>
                    <?php
                    echo human_time_diff(
                        get_post_modified_time('U', '', $message),
                        current_time('timestamp')
                    );

                    esc_html_e(' قبل', 'arvand-panel');
                    ?>
                </time>
            </footer>
        </article>

        <?php if ($replies_query->have_posts()): ?>
            <?php while ($replies_query->have_posts()): ?>
                <?php
                $replies_query->the_post();

                if ($current_user->user_login !== WPAP_DEMO) {
                    update_post_meta(get_the_ID(), 'wpap_seen', 1);
                }

                $author_id = get_the_author_meta('id');
                $admin_reply = ($current_user->ID != $author_id) ? 'wpap-admin-reply' : null;
                $is_creator = ($author_id == $message->post_author);
                $reply_attachment = get_post_meta(get_the_ID(), 'wpap_msg_attachment', true);
                ?>

                <article class="wpap-chat <?php echo $is_creator ? 'wpap-chat-sender' : 'wpap-chat-recipient'; ?>">
                    <div class="wpap-chat-content wpap-post-content">
                        <?php echo wp_kses_post(wpautop(get_the_content())); ?>
                    </div>

                    <footer>
                        <div class="wpap-chat-author">
                            <?php
                            echo get_avatar($author_id, '30');
                            echo esc_html(get_the_author_meta('display_name'));
                            ?>
                        </div>

                        <?php if (!empty($reply_attachment['url'])): ?>
                            <?php
                            $args = [
                                'msg_attachment_download' => get_the_ID(),
                                'msg_attachment_download_nonce' => wp_create_nonce('msg_attachment_download')
                            ];
                            ?>

                            <a class="wpap-chat-attachment-file" href="<?php echo esc_url(add_query_arg($args)); ?>">
                                <i class="ri-attachment-2"></i>
                                <?php esc_html_e('فایل ضمیمه', 'arvand-panel'); ?>
                            </a>
                        <?php endif; ?>

                        <time>
                            <?php
                            echo human_time_diff(
                                get_post_modified_time('U', '', get_the_ID()),
                                current_time('timestamp')
                            );

                            esc_html_e(' قبل', 'arvand-panel');
                            ?>
                        </time>
                    </footer>
                </article>
            <?php endwhile; ?>
            <?php $replies_query->reset_postdata(); ?>

            <?php wpap_pagination($replies_query->max_num_pages); ?>
        <?php endif; ?>
    </div>

    <div class="wpap-chat-reply-form-wrap wpap-form-wrap">
        <form id="wpap-message-reply-form" method="post" enctype="multipart/form-data">
            <header>
                <h2><?php esc_html_e('ارسال پاسخ', 'arvand-panel'); ?></h2>
            </header>

            <div>
                <?php wp_nonce_field('private_msg_nonce', 'private_msg_nonce'); ?>
                <input type="hidden" name="action" value="send_msg_reply"/>
                <input type="hidden" name="message" value="<?php echo esc_attr($message->ID); ?>"/>

                <div class="wpap-field-wrap">
                    <label class="wpap-field-label">
                        <?php esc_html_e('Your Message', 'arvand-panel'); ?>
                    </label>

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
                            size_format(1024 * wpap_general_options()['private_msg_attachment_size'])
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