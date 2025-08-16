<?php

namespace Arvand\ArvandPanel\PanelControllers;

defined( 'ABSPATH' ) || exit;

use Arvand\ArvandPanel\WPAPNotification;
use Arvand\ArvandPanel\WPAPTicket;

class WPAPPanelController
{
    public function notifications($id)
    {
        global $current_user;
        $notice = get_post($id);

        if (!$notice || !WPAPNotification::isRecipient($current_user, $notice->ID)) {
            return wpap_view('notfound');
        }

        return wpap_view('notice.notice', compact('notice'));
    }

    public function singleTicket($id)
    {
        $main_ticket = get_post(absint($id));

        if (!$main_ticket
            || $main_ticket->post_type !== 'wpap_ticket'
            || $main_ticket->post_parent > 0 // Check if is not reply
        ) {
            wp_redirect(wpap_get_page_url_by_name('tickets'));
            exit;
        }

        global $current_user;
        $recipient = (int) get_post_meta($main_ticket->ID, 'wpap_ticket_recipient', true);
        $creator = (int) get_post_meta($main_ticket->ID, 'wpap_ticket_creator', true);
        $department = get_post_meta($main_ticket->ID, 'wpap_ticket_department', true);
        $user_dep = WPAPTicket::userDepartment($current_user->ID);

        // Check user access to the ticket
        if (!in_array($current_user->ID, [$creator, $recipient]) &&
            (!empty($user_dep) && !in_array($department, $user_dep))
        ) {
            wp_redirect(wpap_get_page_url_by_name('tickets'));
            exit;
        }

        wpap_view('ticket.single.single', [
            'current_user' => $current_user,
            'main_ticket' => $main_ticket,
            'recipient' => $recipient,
            'user_dep' => $user_dep,
        ]);
    }

    public function singleMessage($id)
    {
        $message = get_post(intval($id));

        if (!$message
            || $message->post_type !== 'wpap_private_message'
            || $message->post_parent > 0 // Check if is not reply
        ) {
            wp_redirect(wpap_get_page_url_by_name('private_msg'));
            exit;
        }

        global $current_user;
        $recipient = get_post_meta($message->ID, 'wpap_private_msg_recipient', true);

        // Check user access
        if ($recipient != $current_user->ID) {
            wp_redirect(wpap_get_page_url_by_name('private_msg'));
            exit;
        }

        wpap_view('message.single', compact('message', 'recipient'));
    }
}