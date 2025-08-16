<?php
defined('ABSPATH') || exit;

global $current_user;
?>

<div id="wpap-header-notice-wrap">
    <div>
        <header>
            <h2><?php esc_html_e('notifications', 'arvand-panel'); ?></h2>

            <a id="wpap-header-hide-notice-wrap" href="#" title="<?php esc_attr_e('Hide Notice', 'arvand-panel'); ?>">
                <i class="ri-close-large-line"></i>
            </a>
        </header>

        <div>
            <?php
            $notices = get_posts([
                'post_type' => ['wpap_notifications', 'wpap_private_message'],
                'post_status' => 'publish',
                'numberposts' => -1
            ]);

            $post_count = 0;

            if (count($notices)):
                $notice_seen_date = \Arvand\ArvandPanel\WPAPNotification::getSeenDate($current_user->ID);

                foreach ($notices as $notice):
                    if ('wpap_notifications' === $notice->post_type && !wpap_is_valid_section('notifications')) {
                        continue;
                    }

                    if ('wpap_private_message' === $notice->post_type && !wpap_is_valid_section('private_msg')) {
                        continue;
                    }

                    if ('wpap_notifications' === $notice->post_type
                        && (!\Arvand\ArvandPanel\WPAPNotification::isRecipient($current_user, $notice->ID))
                    ) {
                        continue;
                    }

                    $unseen = get_post_meta($notice->ID, 'wpap_seen', 1);

                    if (0 == $unseen
                        || ($notice->post_type === 'wpap_notifications' && get_post_timestamp($notice->ID) > $notice_seen_date)
                    ):
                        $parent = get_post($notice->post_parent);
                        $display = true;

                        if ($notice->post_type === 'wpap_private_message') {
                            $recipient = get_post_meta($notice->ID, 'wpap_private_msg_recipient', 1);
                            if ($recipient != $current_user->ID) {
                                continue;
                            }
                        }

                        $post_count++;
                        ?>

                        <article class="wpap-header-notice">
                            <header>
                                <?php if ($notice->post_type === 'wpap_private_message'): ?>
                                    <?php $post_id = $notice->post_parent ?: $notice->ID; ?>

                                    <a href="<?php echo esc_url(wpap_get_page_url_by_name('private_msg', $post_id)); ?>">
                                        <h3>
                                            <?php
                                            if (!empty($notice->post_title)) {
                                                echo get_the_title($notice->ID);
                                            } else {
                                                esc_html_e('Private message to you', 'arvand-panel');
                                            }
                                            ?>
                                        </h3>
                                    </a>
                                <?php endif; ?>

                                <?php if ($notice->post_type === 'wpap_notifications'): ?>
                                    <a href="<?php echo esc_url(wpap_get_page_url_by_name('notifications', $notice->ID)); ?>">
                                        <h3>
                                            <?php
                                            echo !empty(get_the_title($notice->ID))
                                                ? get_the_title($notice->ID)
                                                : esc_html__('Untitled', 'arvand-panel');
                                            ?>
                                        </h3>
                                    </a>
                                <?php endif; ?>

                                <span>
                                    <?php
                                    switch ($notice->post_type) {
                                        case 'wpap_notifications';
                                            esc_html_e('Notification', 'arvand-panel');
                                            break;
                                        case 'wpap_private_message';
                                            esc_html_e('Private Message', 'arvand-panel');
                                            break;
                                    }
                                    ?>
                                </span>
                            </header>

                            <footer>
                                <time>
                                    <?php
                                    echo sprintf(
                                        esc_html__('%s at %s', 'arvand-panel'),
                                        get_the_date('', $notice->ID),
                                        get_the_time('', $notice->ID)
                                    );
                                    ?>
                                </time>
                            </footer>
                        </article>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!$post_count > 0): ?>
                <div id="wpap-header-notfound-notice">
                    <?php esc_html_e('There is no unread notice.', 'arvand-panel'); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>