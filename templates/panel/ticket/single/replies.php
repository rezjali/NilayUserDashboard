<?php
defined('ABSPATH') || exit;

global $current_user;
$attachment = get_post_meta($main_ticket->ID, 'wpap_ticket_attachment', true);
$panel_page = absint($_GET['panel-page'] ?? 1);
$limit = absint($ticket_opt['ticket_replies_per_page']);
$offset = ($panel_page * $limit) - $limit;

$query = new WP_Query([
    'post_type' => 'wpap_ticket',
    'post_parent' => $main_ticket->ID,
    'posts_per_page' => $limit,
    'paged' => $panel_page,
    'offset' => $offset,
    'order' => 'ASC',
]);
?>

<div class="wpap-chat-replies">
    <article class="wpap-chat wpap-chat-sender">
        <div class="wpap-chat-content wpap-post-content">
            <?php echo wp_kses_post(wpautop($main_ticket->post_content)); ?>
        </div>

        <footer>
            <div class="wpap-chat-author">
                <?php
                echo get_avatar($main_ticket->post_author, '30');
                echo esc_html(get_the_author_meta('display_name', $main_ticket->post_author));
                ?>
            </div>

            <?php if (!empty($attachment['url'])): ?>
                <?php
                $args = [
                    'ticket_attachment_download' => $main_ticket->ID,
                    'ticket_attachment_download_nonce' => wp_create_nonce('ticket_attachment_download')
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
                    get_post_modified_time('U', '', $main_ticket->ID),
                    current_time('timestamp')
                );

                esc_html_e(' قبل', 'arvand-panel');
                ?>
            </time>
        </footer>
    </article>

    <?php if ($query->have_posts()): ?>
        <?php while ($query->have_posts()): ?>
            <?php
            $query->the_post();
            $author = get_the_author_meta('ID');
            $is_ticket_creator = ($author == $main_ticket->post_author);
            $attachment = get_post_meta(get_the_ID(), 'wpap_ticket_attachment', true);
            ?>

            <article class="wpap-chat <?php echo $is_ticket_creator ? 'wpap-chat-sender' : 'wpap-chat-recipient'; ?>">
                <div>
                    <div class="wpap-chat-content wpap-post-content">
                        <?php the_content(); ?>
                    </div>

                    <footer>
                        <div class="wpap-chat-author">
                            <?php
                            echo get_avatar($author, '30');
                            echo esc_html(get_the_author_meta('display_name', $author));
                            ?>
                        </div>

                        <?php if (!empty($attachment['url'])): ?>
                            <?php
                            $args = [
                                'ticket_attachment_download' => get_the_ID(),
                                'ticket_attachment_download_nonce' => wp_create_nonce('ticket_attachment_download')
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
                </div>
            </article>
        <?php endwhile; ?>
        <?php $query->reset_postdata(); ?>

        <?php wpap_pagination($query->max_num_pages); ?>
    <?php endif; ?>
</div>
