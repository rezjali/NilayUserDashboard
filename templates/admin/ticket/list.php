<?php
defined('ABSPATH') || exit;

$page_num = isset($_GET['page-num']) ? absint($_GET['page-num']) : 1;
$limit = 20;

$args = [
    'post_type' => 'wpap_ticket',
    'post_parent' => 0,
    'posts_per_page' => $limit,
    'orderby' => 'post_modified',
    'offset' => ($page_num - 1) * $limit,
    'fields' => 'ids'
];

$ticket_department_opt = wpap_ticket_department_options();
$current_user_id = get_current_user_id();
$user_dep = \Arvand\ArvandPanel\WPAPTicket::userDepartment($current_user_id);

if (!empty($user_dep)) {
    $args['meta_query'][] = [
        ['key' => 'wpap_ticket_department', 'value' => $user_dep, 'compare' => 'IN']
    ];
}

$query = new WP_Query(array_merge($args, \Arvand\ArvandPanel\Admin\Handlers\WPAPAdminTicketHandler::listFilter()));
?>

<div id="wpap-ticket-list" class="wpap-wrap wrap">
    <h1><?php esc_html_e('تیکت ها', 'arvand-panel'); ?></h1>

    <a class="wpap-btn-1" href="<?php echo esc_url(add_query_arg(['section' => 'new'], remove_query_arg($remove_args))); ?>">
        <i class="ri-add-line"></i>
        <?php esc_html_e('New Ticket', 'arvand-panel'); ?>
    </a>

    <form class="wpap-list-actions" action="<?php echo esc_url(remove_query_arg($remove_args)); ?>" method="post">
        <?php wp_nonce_field('ticket_list_actions_nonce', 'ticket_list_actions_nonce'); ?>
        <input type="number" name="ticket_number" placeholder="<?php esc_attr_e('Ticket number', 'arvand-panel'); ?>" min="1"/>
        <input type="text" name="ticket_search" placeholder="<?php esc_attr_e('عنوان/محتوای تیکت', 'arvand-panel'); ?>"/>

        <select name="ticket_status">
            <option value="" selected><?php esc_html_e('ُSelect status', 'arvand-panel'); ?></option>
            <option value="open"><?php esc_html_e('Open', 'arvand-panel'); ?></option>

            <?php
            $ticket_statuses = wpap_ticket_options()['ticket_status'];

            if (count($ticket_statuses['name'])) {
                foreach ($ticket_statuses['name'] as $status_name) {
                    printf('<option value="%s">%s</option>', esc_attr($status_name), esc_html($status_name));
                }
            }
            ?>

            <option value="solved"><?php esc_html_e('Solved', 'arvand-panel'); ?></option>
            <option value="closed"><?php esc_html_e('Closed', 'arvand-panel'); ?></option>
        </select>

        <input type="text" name="ticket_author_user_login" placeholder="<?php esc_html_e('Creator username', 'arvand-panel'); ?>"/>
        <input type="text" name="ticket_creator_phone" placeholder="<?php esc_html_e('شماره همراه ایجاد کننده', 'arvand-panel'); ?>"/>
        <input type="text" name="ticket_recipient_phone" placeholder="<?php esc_html_e('شماره همراه گیرنده', 'arvand-panel'); ?>"/>

        <button class="wpap-btn-2">
            <?php esc_html_e('Apply filter', 'arvand-panel') ?>
        </button>
    </form>

    <div id="wpap-ticket-status-wrap">
        <span id="wpap-open-tickets">
            <i></i>
            <span><?php esc_html_e('Open', 'arvand-panel'); ?></span>
            <span><?php echo esc_html(\Arvand\ArvandPanel\WPAPTicket::count('open', $user_dep)); ?></span>
        </span>

        <?php if (count($ticket_statuses['name'])):
            foreach ($ticket_statuses['name'] as $key => $status_name): ?>
                <span>
                    <i style="background-color: <?php esc_attr_e($ticket_statuses['color'][$key]); ?>"></i>
                    <span><?php echo esc_html($status_name); ?></span>
                    <span><?php echo esc_html(\Arvand\ArvandPanel\WPAPTicket::count($status_name, $user_dep)); ?></span>
                </span>
            <?php endforeach;
        endif; ?>

        <span id="wpap-solved-tickets">
            <i></i>
            <span><?php esc_html_e('Solved', 'arvand-panel'); ?></span>
            <span><?php echo esc_html(\Arvand\ArvandPanel\WPAPTicket::count('solved', $user_dep)); ?></span>
        </span>

        <span id="wpap-closed-tickets">
            <i></i>
            <span><?php esc_html_e('Closed', 'arvand-panel'); ?></span>
            <span><?php echo esc_html(\Arvand\ArvandPanel\WPAPTicket::count('closed', $user_dep)); ?></span>
        </span>

        <span id="wpap-all-tickets">
            <i></i>
            <span><?php esc_html_e('All', 'arvand-panel'); ?></span>
            <span><?php echo esc_html(\Arvand\ArvandPanel\WPAPTicket::count('', $user_dep)); ?></span>
        </span>
    </div>

    <div class="wpap-table-wrap">
        <table id="wpap-ticket-list-table">
            <thead>
                <tr>
                    <th><?php esc_html_e('Number', 'arvand-panel'); ?></th>
                    <th><?php esc_html_e('Title', 'arvand-panel'); ?></th>
                    <th><?php esc_html_e('Creator', 'arvand-panel'); ?></th>
                    <th><?php esc_html_e('Recipient', 'arvand-panel'); ?></th>
                    <th><?php esc_html_e('Department', 'arvand-panel'); ?></th>
                    <th><?php esc_html_e('Status', 'arvand-panel'); ?></th>
                    <th><?php esc_html_e('Modified date', 'arvand-panel'); ?></th>
                    <th colspan="2"><?php esc_html_e('Actions', 'arvand-panel'); ?></th>
                </tr>
            </thead>

            <tbody>
                <?php if ($query->have_posts()):
                    while ($query->have_posts()):
                        $query->the_post();
                        $single_link = esc_url(add_query_arg(['section' => 'single', 'ticket' => get_the_ID()], remove_query_arg($remove_args)))
                        ?>

                        <tr>
                            <td class="wpap-ticket-number">
                                <a href="<?php echo $single_link; ?>">
                                    <?php echo get_the_ID() . '#'; ?>
                                </a>
                            </td>

                            <td>
                                <a href="<?php echo $single_link; ?>">
                                    <strong><?php echo wp_trim_words(get_the_title(), 5); ?></strong>
                                </a>
                            </td>

                            <td>
                                <?php
                                printf('<a href="%s">%s</a>',
                                    get_edit_user_link(get_the_author_meta('id')),
                                    get_the_author_meta('display_name')
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                $recipient = get_post_meta(get_the_ID(), 'wpap_ticket_recipient', 1);

                                if ($recipient && $recipient_user = get_user_by('id', $recipient)) {
                                    printf('<a href="%s">%s</a>', get_edit_user_link($recipient_user->ID), $recipient_user->user_login);
                                } else {
                                    esc_html_e('Admin', 'arvand-panel');
                                }
                                ?>
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
                                        echo '<span class="wpap-badge-success">' . __('Closed', 'arvand-panel') . '</span>';
                                    } elseif ($status === 'solved') {
                                        echo '<span class="wpap-badge-success">' . __('Solved', 'arvand-panel') . '</span>';
                                    } elseif ($status === 'open') {
                                        echo '<span class="wpap-badge-error">' . __('Open', 'arvand-panel') . '</span>';
                                    } else {
                                        $status_color = get_post_meta(get_the_ID(), 'wpap_ticket_status_color', true);
                                        $status_text_color = get_post_meta(get_the_ID(), 'wpap_ticket_status_text_color', true);
                                        echo "<span class='wpap-badge' style='background-color: $status_color; color: $status_text_color;'>$status</span>";
                                    }
                                } else {
                                    echo '___';
                                }
                                ?>
                            </td>

                            <td><time><?php echo get_the_modified_date() . ' | ' . get_the_modified_time(); ?></time></td>

                            <td>
                                <div class="wpap-table-row-actions">
                                    <a href="<?php echo esc_url(add_query_arg(['section' => 'edit', 'ticket' => get_the_ID()], remove_query_arg($remove_args))); ?>">
                                        <i class="ri-edit-2-line"></i>
                                    </a>

                                    <a class="wpap-view-ticket" href="<?php echo $single_link; ?>">
                                        <i class="ri-eye-line"></i>
                                    </a>

                                    <a class="wpap-delete-ticket" href="" data-ticket="<?php the_ID(); ?>">
                                        <i class="ri-delete-bin-7-line"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile;
                else: ?>
                    <tr>
                        <td colspan="8">
                            <?php esc_html_e('There is no ticket.', 'arvand-panel'); ?>
                        </td>
                    </tr>
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
</div>