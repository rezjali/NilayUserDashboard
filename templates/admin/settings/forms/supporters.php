<?php defined('ABSPATH') || exit; ?>

<div id="wpap-supporters-wrap">
    <?php
    $users = get_users(['meta_key' => 'wpap_user_ticket_department']);
    $count = 0;

    if (count($users)):
        foreach ($users as $user): ?>
            <div class="wpap-supporter">
                <div class="wpap-supporter-avatar">
                    <?php echo get_avatar($user->ID, '40'); ?>
                </div>

                <div class="wpap-supporter-username">
                    <?php echo esc_html($user->user_login); ?>
                </div>

                <a class="wpap-delete-supporter" href=""
                   data-nonce="<?php echo wp_create_nonce('del_supporter_nonce'); ?>"
                   data-user="<?php echo esc_attr($user->ID); ?>">
                    <i class="ri-delete-bin-7-line"></i>
                    <div class="wpap-loading"></div>
                </a>
            </div>

            <div class="wpap-popup-form-wrap">
                <div>
                    <form class="wpap-popup-form" method="post">
                        <?php wp_nonce_field('ticket_department_nonce', 'ticket_department_nonce'); ?>
                        <input type="hidden" name="form" value="wpap_edit_supporter"/>

                        <p>
                            <label for="wpap-dep-responsible"><?php esc_html_e('Username', 'arvand-panel'); ?></label>
                            <input id="wpap-dep-responsible" class="regular-text" type="text" name="responsible"
                                   value="<?php echo esc_attr($user->user_login); ?>" readonly/>
                        </p>

                        <p>
                            <label for="wpap-responsible-for"><?php esc_html_e('responsible for', 'arvand-panel'); ?></label>

                            <select id="wpap-responsible-for" class="regular-text" name="responsible_for[]" multiple>
                                <?php
                                $td = wpap_ticket_department_options();
                                $user_dep = \Arvand\ArvandPanel\WPAPTicket::userDepartment($user->ID);

                                for ($i = 0; $i < count($td['departments']); $i++): ?>
                                    <option <?php selected(in_array($td['departments'][$i], $user_dep)); ?>>
                                        <?php echo esc_html($td['departments'][$i]); ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </p>

                        <div class="wpap-popup-form-btn-wrap">
                            <?php require WPAP_ADMIN_TEMPLATES_PATH . 'parts/button.php'; ?>

                            <button type="button" class="wpap-close-popup wpap-btn-1">
                                <?php esc_html_e('Close', 'arvand-panel'); ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endforeach;
    else:
        esc_html_e('There is no supporter.', 'arvand-panel');
    endif; ?>
</div>