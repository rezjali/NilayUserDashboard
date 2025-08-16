<?php
defined('ABSPATH') || exit;

if (!class_exists('woocommerce')) {
    wpap_print_notice(__('افزونه ووکامرس فعال نیست.', 'arvand-panel'), 'info', false);
    return;
}

global $wpdb;
$table_name = $wpdb->prefix . 'wpap_wallet_transactions';
$current_user_id = get_current_user_id();

$total = $wpdb->get_var(
    $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE user_id = %d", $current_user_id)
);

$per_page = 10;
$page = max(1, absint($_GET['panel-page'] ?? 1));
$offset = ($page - 1) * $per_page;

$transactions = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT id, amount, type, description, created_at 
         FROM $table_name 
         WHERE user_id = %d 
         ORDER BY created_at DESC 
         LIMIT %d OFFSET %d",
        $current_user_id,
        $per_page,
        $offset
    )
);
?>

<div id="wpap-wallet-transactions">
    <h2 class="wpap-section-title wpap-mb-30">
        <?php esc_html_e('تراکنش های کیف پول', 'arvand-panel'); ?>
    </h2>

    <div class="wpap-table-wrap">
        <table>
            <thead>
                <tr>
                    <th><?php esc_html_e('شماره', 'arvand-panel'); ?></th>
                    <th><?php esc_html_e('نوع تراکنش', 'arvand-panel'); ?></th>
                    <th><?php esc_html_e('مبلغ', 'arvand-panel'); ?></th>
                    <th><?php esc_html_e('توضیحات', 'arvand-panel'); ?></th>
                    <th><?php esc_html_e('تاریخ', 'arvand-panel'); ?></th>
                </tr>
            </thead>

            <tbody>
                <?php if (!empty($transactions)): ?>
                    <?php foreach ($transactions as $transaction): ?>
                        <tr>
                            <td><?php echo esc_html($transaction->id); ?></td>

                            <td>
                                <?php
                                if ($transaction->type === 'credit') {
                                    esc_html_e('شارژ', 'arvand-panel');
                                } else {
                                    esc_html_e('برداشت', 'arvand-panel');
                                }
                                ?>
                            </td>

                            <td>
                                <?php if ($transaction->type === 'credit'): ?>
                                    <strong style="background-color: rgba(0, 255, 0, 0.10); padding: 3px 8px; border-radius: 5px;">
                                        <?php echo '+ ' . wc_price($transaction->amount); ?>
                                    </strong>
                                <?php else: ?>
                                    <strong style="background-color: rgba(255, 0, 0, 0.10); padding: 3px 8px; border-radius: 5px;">
                                        <?php echo '- ' . wc_price($transaction->amount); ?>
                                    </strong>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php echo esc_html(wp_trim_words($transaction->description, 10)); ?>
                            </td>

                            <td>
                                <?php
                                echo esc_html(
                                    date_i18n(
                                        sprintf(__('%s ساعت %s', 'arvand-panel'), get_option('date_format'), get_option('time_format')),
                                        strtotime($transaction->created_at)
                                    )
                                );
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">
                            <?php esc_html_e('هیچ تراکنشی یافت نشد.', 'arvand-panel'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php wpap_pagination(ceil($total / $per_page)); ?>
</div>