<?php
defined('ABSPATH') || exit;

global $current_user;
$panel_page = absint($_GET['panel-page'] ?? 1);
$limit = 20;
$offset = ($panel_page * $limit) - $limit;

$query = new WP_Query([
    'post_type' => 'wpap_private_message',
    'meta_key' => 'wpap_private_msg_recipient',
    'meta_value' => $current_user->ID,
    'post_parent' => 0,
    'posts_per_page' => $limit,
    'fields' => 'ids',
    'paged' => $panel_page,
    'offset' => $offset
]);
?>

<div id="wpap-private-message">
    <div class="wpap-table-wrap">
        <table>
            <thead>
                <tr>
                    <th><?php esc_html_e('Sender', 'arvand-panel'); ?></th>
                    <th><?php esc_html_e('Title', 'arvand-panel'); ?></th>
                    <th><?php esc_html_e('Date', 'arvand-panel'); ?></th>
                    <th><?php esc_html_e('Status', 'arvand-panel'); ?></th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
                <?php if ($query->have_posts()): ?>
                    <?php while ($query->have_posts()): ?>
                        <?php
                        $query->the_post();
                        $unseen_count = 0;

                        $seen = get_post_meta(get_the_ID(), 'wpap_seen', 1);
                        if (0 == $seen) {
                            $unseen_count += 1;
                        }

                        $unseen_count += \Arvand\ArvandPanel\WPAPMessage::unseenCount(get_the_ID());
                        ?>

                        <tr>
                            <td class="wpap-table-row-sender">
                                <div>
                                    <div class="wpap-table-row-avatar">
                                        <?php echo get_avatar(get_the_author_meta('id')); ?>
                                    </div>

                                    <?php echo get_the_author_meta('login'); ?>
                                </div>
                            </td>

                            <td class="wpap-table-row-title">
                                <?php
                                if (empty(get_the_title())) {
                                    echo '---';
                                } else {
                                    printf(
                                        '<a href="%s"><h2>%s</h2></a>',
                                        esc_url(wpap_panel_url('private-msg/' . get_the_ID())),
                                        wp_trim_words(esc_html(get_the_title()), 7)
                                    );
                                }
                                ?>
                            </td>

                            <td><?php echo get_the_date(); ?></td>

                            <td>
                                <?php
                                if ($unseen_count) {
                                    echo '<strong class="wpap-badge-error">' . sprintf(esc_html__('%d unread', 'arvand-panel'), $unseen_count) . '</strong>';
                                } else {
                                    echo '<strong class="wpap-badge-success">' . esc_html__('Read', 'arvand-panel') . '</strong>';
                                }
                                ?>
                            </td>

                            <td>
                                <a class="wpap-view-msg wpap-btn-1" href="<?php echo esc_url(wpap_panel_url('private-msg/' . get_the_ID())); ?>">
                                    <?php esc_html_e('View', 'arvand-panel'); ?>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <?php $query->reset_postdata(); ?>
                <?php else: ?>
                    <td colspan="5">
                        <?php esc_html_e('There is no message.', 'arvand-panel'); ?>
                    </td>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php wpap_pagination($query->max_num_pages); ?>
</div>