<?php
defined('ABSPATH') || exit;

global $current_user;
$ticket_opt = wpap_ticket_options();
$status = get_post_meta($main_ticket->ID, 'wpap_ticket_status', true);
$priority = get_post_meta(get_the_ID(), 'wpap_ticket_priority', true);
?>

<div id="wpap-single-ticket" class="wpap-chat-wrap">
    <header>
        <h1><?php echo esc_html($main_ticket->post_title); ?></h1>

        <div>
            <span>
                <?php
                if ($status) {
                    if ($status === 'closed') {
                        printf(
                            '<strong class="wpap-badge-error">%s</strong>',
                            esc_html__('بسته شده', 'arvand-panel')
                        );
                    } elseif ($status === 'solved') {
                        printf(
                            '<strong class="wpap-badge-success">%s</strong>',
                            esc_html__('حل شده', 'arvand-panel')
                        );
                    } elseif ($status === 'open') {
                        printf(
                            '<strong class="wpap-badge-error">%s</strong>',
                            esc_html__('باز', 'arvand-panel')
                        );
                    } else {
                        printf(
                            '<strong class="wpap-badge" style="background-color: %s; color: %s">%s</strong>',
                            esc_attr(get_post_meta($main_ticket->ID, 'wpap_ticket_status_color', 1)),
                            esc_attr(get_post_meta($main_ticket->ID, 'wpap_ticket_status_text_color', 1)),
                            esc_html__('باز', 'arvand-panel')
                        );
                    }
                } else {
                    echo '---';
                }
                ?>
            </span>

            <span>
                <?php
                printf(
                    esc_html__('فرستنده: %s', 'arvand-panel'),
                    get_the_author_meta('display_name', $main_ticket->post_author)
                );
                ?>
            </span>

            <span>
                <?php printf(esc_html__('شماره: %s', 'arvand-panel'), esc_html($main_ticket->ID)); ?>
            </span>

            <span>
                <?php
                esc_html_e('اولویت: ', 'arvand-panel');
                if ($priority === 'low') {
                    esc_html_e('پایین', 'arvand-panel');
                } elseif ($priority === 'normal') {
                    esc_html_e('معمولی', 'arvand-panel');
                } else {
                    esc_html_e('بالا', 'arvand-panel');
                }
                ?>
            </span>

            <span>
                <?php
                printf(
                    esc_html__('%s ساعت %s', 'arvand-panel'),
                    get_the_date('', $main_ticket),
                    get_the_time('', $main_ticket)
                );
                ?>
            </span>
        </div>
    </header>

    <?php
    wpap_template(
        'panel/ticket/single/replies',
        compact('main_ticket', 'ticket_opt', 'status')
    );
    ?>

    <?php
    wpap_template(
        'panel/ticket/single/reply-form',
        compact('main_ticket', 'status', 'recipient', 'user_dep')
    );
    ?>
</div>