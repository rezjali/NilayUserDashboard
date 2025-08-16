<?php
defined( 'ABSPATH' ) || exit;

function wpap_wallet_get_balance($user_id)
{
    if (!$user_id) {
        return 0;
    }

    global $wpdb;
    $table = $wpdb->prefix . 'wpap_wallet_transactions';

    $credit = $wpdb->get_var(
        $wpdb->prepare("SELECT SUM(amount) FROM $table WHERE user_id = %d AND type = 'credit'", $user_id)
    ) ?? 0;

    $debit = $wpdb->get_var(
        $wpdb->prepare("SELECT SUM(amount) FROM $table WHERE user_id = %d AND type = 'debit'", $user_id)
    ) ?? 0;

    return max(0, $credit - $debit);
}

function wpap_wallet_insert_transaction($user_id, $amount, $type, $desc = '')
{
    if (!$user_id || !$amount || !in_array($type, ['credit', 'debit'])) {
        return false;
    }

    global $wpdb;

    $wpdb->insert(
        $wpdb->prefix . 'wpap_wallet_transactions',
        [
            'user_id' => $user_id,
            'amount' => $amount,
            'type' => $type,
            'description' => sanitize_text_field($desc)
        ]
    );
}