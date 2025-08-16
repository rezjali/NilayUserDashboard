<?php
defined( 'ABSPATH' ) || exit;

$opened_count = \Arvand\ArvandPanel\WPAPTicket::count('open', $user_dep);
$solved_count = \Arvand\ArvandPanel\WPAPTicket::count('solved', $user_dep);
$closed_count = \Arvand\ArvandPanel\WPAPTicket::count('closed', $user_dep);
$all_count = \Arvand\ArvandPanel\WPAPTicket::count('', $user_dep);
?>

<div id="wpap-ticket-statues-wrap">
    <a id="wpap-open-tickets" <?php echo !$opened_count ? 'style="opacity: 0.3"' : ''; ?>
       href="<?php echo esc_url(add_query_arg(['ticket-status' => 'open'])); ?>">
        <i></i>

        <?php
        printf(
            '<span>%s</span>',
            sprintf(esc_html__('%d تیکت باز', 'arvand-panel'), $opened_count)
        );
        ?>
    </a>

    <?php if (count($ticket_status['name'])): ?>
        <?php for ($i = 0; $i < count($ticket_status['name']); $i++): ?>
            <?php
            $count = \Arvand\ArvandPanel\WPAPTicket::count($ticket_status['name'][$i], $user_dep);
            if (!$count) {
                continue;
            }
            ?>

            <a style="<?php echo $count ? '' : 'opacity: 0.3;'; ?>" href="<?php echo esc_url(add_query_arg(['ticket-status' => $ticket_status['name'][$i]])); ?>">
                <i style="border-color: <?php esc_attr_e($ticket_status['color'][$i]); ?>"></i>

                <?php
                printf(
                    '<span>%s</span>',
                    sprintf(esc_html__('%d تیکت %s', 'arvand-panel'), $count, $ticket_status['name'][$i])
                );
                ?>
            </a>
        <?php endfor; ?>
    <?php endif; ?>

    <a id="wpap-solved-tickets" <?php echo !$solved_count ? 'style="opacity: 0.3"' : ''; ?>
       href="<?php echo esc_url(add_query_arg(['ticket-status' => 'solved'])); ?>">
        <i></i>

        <?php
        printf(
            '<span>%s</span>',
            sprintf(esc_html__('%d تیکت حل شده', 'arvand-panel'), $solved_count)
        );
        ?>
    </a>

    <a id="wpap-closed-tickets" <?php echo !$closed_count ? 'style="opacity: 0.3"' : ''; ?>
       href="<?php echo esc_url(add_query_arg(['ticket-status' => 'closed'])); ?>">
        <i></i>

        <?php
        printf(
            '<span>%s</span>',
            sprintf(esc_html__('%d تیکت بسته شده', 'arvand-panel'), $closed_count)
        );
        ?>
    </a>

    <a id="wpap-all-tickets" <?php echo !$all_count ? 'style="opacity: 0.3"' : ''; ?>
       href="<?php echo esc_url(wpap_get_page_url_by_name('tickets')); ?>">
        <i></i>

        <?php
        printf(
            '<span>%s</span>',
            sprintf(esc_html__('همه تیکت ها: %d', 'arvand-panel'), $all_count)
        );
        ?>
    </a>
</div>
