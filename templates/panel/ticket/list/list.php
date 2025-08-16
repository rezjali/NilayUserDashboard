<?php
defined('ABSPATH') || exit;

global $current_user;
$ticket_opt = wpap_ticket_options();
$ticket_status = $ticket_opt['ticket_status'];
$user_dep = \Arvand\ArvandPanel\WPAPTicket::userDepartment($current_user->ID);
$panel_page = absint($_GET['panel-page'] ?? 1);
$limit = absint($ticket_opt['tickets_per_page']);
$args = require(WPAP_TEMPLATES_PATH . 'panel/ticket/list/query-args.php');
$query = new WP_Query($args);
?>

<div id="wpap-ticket-list">
    <a class="wpap-btn-1" href="<?php echo esc_url(wpap_panel_url('new-ticket')); ?>">
        <i></i>
        <?php esc_html_e('New Ticket', 'arvand-panel'); ?>
    </a>

    <?php
    wpap_template('panel/ticket/list/actions-form', compact('ticket_status'));

    wpap_template('panel/ticket/list/statuses', compact(
        'ticket_status',
        'user_dep'
    ));
    ?>

    <?php do_action('wpap_before_ticket_list'); ?>

    <div class="wpap-table-wrap">
        <table>
            <thead>
                <tr>
                    <th><?php esc_html_e('فرستنده', 'arvand-panel'); ?></th>
                    <th><?php esc_html_e('عنوان', 'arvand-panel'); ?></th>
                    <th><?php esc_html_e('بخش', 'arvand-panel'); ?></th>
                    <th><?php esc_html_e('وضعیت', 'arvand-panel'); ?></th>
                    <th><?php esc_html_e('بروزرسانی', 'arvand-panel'); ?></th>
                    <th></th>
                </tr>
            </thead>

            <?php if ($query->have_posts()): ?>
                <?php $ticket_num = 0; ?>

                <?php while ($query->have_posts()): ?>
                    <?php $query->the_post(); ?>

                    <tr>
                        <td>
                            <a class="wpap-table-row-sender" href="<?php echo esc_url(wpap_panel_url('tickets/' . get_the_ID())); ?>">
                                <div>
                                    <div class="wpap-table-row-avatar">
                                        <?php echo get_avatar(get_the_author_meta('ID')); ?>
                                    </div>

                                    <?php echo esc_html(get_the_author_meta('display_name')); ?>
                                </div>
                            </a>
                        </td>

                        <td class="wpap-table-row-title">
                            <a href="<?php echo esc_url(wpap_panel_url('tickets/' . get_the_ID())); ?>">
                                <strong>
                                    <?php echo wp_trim_words(esc_html(get_the_title()), 10); ?>
                                </strong>
                            </a>
                        </td>

                        <td>
                            <?php
                            $department = get_post_meta(get_the_ID(), 'wpap_ticket_department', true);
                            echo $department ? esc_html($department) : '---';
                            ?>
                        </td>

                        <td>
                            <?php
                            $status = get_post_meta(get_the_ID(), 'wpap_ticket_status', true);
                            if ($status) {
                                if ($status === 'closed') {
                                    echo '<strong class="wpap-badge-success">' . esc_html__('Closed', 'arvand-panel') . '</strong>';
                                } elseif ($status === 'solved') {
                                    echo '<strong class="wpap-badge-success">' . esc_html__('Solved', 'arvand-panel') . '</strong>';
                                } elseif ($status === 'open') {
                                    echo '<strong class="wpap-badge-error">' . esc_html__('Open', 'arvand-panel') . '</strong>';
                                } else {
                                    $status_color = esc_html(get_post_meta(get_the_ID(), 'wpap_ticket_status_color', 1));
                                    $status_text_color = esc_html(get_post_meta(get_the_ID(), 'wpap_ticket_status_text_color', 1));
                                    echo "<span class='wpap-badge' style='background-color: $status_color; color: $status_text_color;'>$status</span>";
                                }
                            } else {
                                echo '---';
                            }
                            ?>
                        </td>

                        <td>
                            <time>
                                <?php
                                echo human_time_diff(
                                    get_post_modified_time('U', '', get_the_ID()),
                                    current_time('timestamp')
                                );

                                esc_html_e(' قبل', 'arvand-panel');
                                ?>
                            </time>
                        </td>

                        <td>
                            <a class="wpap-view-ticket wpap-btn-1"
                               href="<?php echo esc_url(wpap_panel_url('tickets/' . get_the_ID())); ?>">
                                <?php esc_html_e('View', 'arvand-panel'); ?>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                <?php $query->reset_postdata(); ?>
            <?php else: ?>
                <td colspan="7">
                    <?php esc_html_e('There is no ticket.', 'arvand-panel'); ?>
                </td>
            <?php endif; ?>
        </table>
    </div>

    <?php wpap_pagination($query->max_num_pages); ?>

    <?php do_action('wpap_after_ticket_list'); ?>
</div>
