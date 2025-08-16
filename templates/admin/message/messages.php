<?php defined('ABSPATH') || exit; ?>

<h1><?php esc_html_e('Pravate Massage', 'arvand-panel'); ?></h1>

<a class="wpap-btn-1" href="<?php echo esc_url(add_query_arg('section', 'new')); ?>">
    <?php esc_html_e('ارسال پیام جدید', 'arvand-panel'); ?>
</a>

<form class="wpap-list-actions" method="post">
    <?php wp_nonce_field('search_user_nonce', 'search_user_nonce'); ?>
    <input type="text" name="s" placeholder="<?php esc_attr_e('عنوان/محتوای پیام', 'arvand-panel'); ?>"/>
    <input type="text" name="sender" placeholder="<?php esc_attr_e('نام کاربری/ایمیل فرستنده', 'arvand-panel'); ?>"/>
    <input type="text" name="recipient" placeholder="<?php esc_attr_e('نام کاربری/ایمیل گیرنده', 'arvand-panel'); ?>"/>

    <button class="wpap-btn-2" type="submit" name="filter_messages">
        <?php esc_html_e('اعمال فیلتر', 'arvand-panel'); ?>
    </button>
</form>

<div class="wpap-table-wrap">
    <table id="wpap-user-list">
        <thead>
            <th><?php esc_html_e('عنوان', 'arvand-panel'); ?></th>
            <th><?php esc_html_e('ارسال کننده', 'arvand-panel'); ?></th>
            <th><?php esc_html_e('گیرنده', 'arvand-panel'); ?></th>
            <th><?php esc_html_e('تعداد پاسخ ها', 'arvand-panel'); ?></th>
            <th><?php esc_html_e('عملیات', 'arvand-panel'); ?></th>
        </thead>

        <tbody>
            <?php
            $page_num = isset($_GET['page-num']) ? (int)$_GET['page-num'] : 1;
            $limit = 20;

            $args = [
                'post_type' => 'wpap_private_message',
                'post_status' => 'publish',
                'post_parent' => 0,
                'offset' => ($page_num - 1) * $limit,
                'posts_per_page' => $limit
            ];

            if (!empty($_POST['s'])) {
                if (wp_verify_nonce($_POST['search_user_nonce'], 'search_user_nonce')) {
                    $args['s'] = sanitize_text_field($_POST['s']);
                }
            }

            if (!empty($_POST['sender'])) {
                $user = get_user_by('login', sanitize_text_field($_POST['sender']));

                if ($user) {
                    $args['author'] = $user->ID;
                }
            }

            if (!empty($_POST['recipient'])) {
                $user = get_user_by('login', sanitize_text_field($_POST['recipient']));

                if ($user) {
                    $args['meta_query'][] = ['key' => 'wpap_private_msg_recipient', 'value' => $user->ID, 'compare' => '='];
                }
            }

            $query = new WP_Query($args);

            if ($query->have_posts()):
                while ($query->have_posts()):
                    $query->the_post(); ?>

                    <tr>
                        <td>
                            <?php
                            if ($title = get_the_title()) {
                                printf(
                                    '<a href="%s">%s</a>',
                                    esc_url(add_query_arg(['section' => 'single', 'msg' => get_the_ID()], remove_query_arg($remove_args))),
                                    esc_html(wp_trim_words($title, 7))
                                );
                            } else {
                                echo '---';
                            }
                            ?>
                        </td>

                        <td>
                            <?php
                            printf(
                                '<a href="%s">%s</a>',
                                get_edit_user_link(get_the_author_meta('id')),
                                esc_html(get_the_author_meta('display_name'))
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            $recipient = (int)get_post_meta($query->post->ID, 'wpap_private_msg_recipient', 1);

                            if ($user = get_user_by('id', $recipient)) {
                                printf('<a href="%s">%s</a>', get_edit_user_link($user->ID), esc_html($user->display_name));
                            } else {
                                echo '---';
                            }
                            ?>
                        </td>

                        <td>
                            <?php
                            $unseen_count = Arvand\ArvandPanel\WPAPMessage::adminUnseenCount(null, get_the_ID());

                            if ($unseen_count) {
                                printf(
                                    '<span class="wpap-badge-error">%s</span>',
                                    sprintf(esc_html__('%d خوانده نشده', 'arvand-panel'), $unseen_count),
                                );
                            } else {
                                echo Arvand\ArvandPanel\WPAPMessage::count(0, 0, get_the_ID());
                            }
                            ?>
                        </td>

                        <td>
                            <div class="wpap-table-row-actions">
                                <a href="<?php echo esc_url(add_query_arg(['section' => 'edit', 'msg' => get_the_ID()], remove_query_arg($remove_args))); ?>">
                                    <i class="ri-edit-2-line"></i>
                                </a>

                                <a href="<?php echo esc_url(add_query_arg(['section' => 'single', 'msg' => get_the_ID()], remove_query_arg($remove_args))); ?>">
                                    <i class="ri-eye-line"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <td colspan="5">
                    <?php esc_html_e('پیامی وجود ندارد.', 'arvand-panel'); ?>
                </td>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
echo paginate_links([
    'base' => add_query_arg('page-num', '%#%'),
    'total' => ceil($query->found_posts / $limit),
    'current' => $page_num,
]);
?>

