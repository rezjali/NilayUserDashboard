<?php
defined('ABSPATH') || exit;

function wpap_ticket_status_name($status)
{
    switch ($status) {
        case 'open':
            return __('باز', 'arvand-panel');
        case 'solved':
            return __('حل شده', 'arvand-panel');
        case 'closed':
            return __('بسته شده', 'arvand-panel');
        default:
            return $status;
    }
}