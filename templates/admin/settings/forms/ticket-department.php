<?php
defined('ABSPATH') || exit;

$td = wpap_ticket_department_options();
?>

<form id="wpap-ticket-dep-section" class="wpap-form" method="post">
    <?php wp_nonce_field('ticket_department_nonce', 'ticket_department_nonce'); ?>
    <input type="hidden" name="form" value="wpap_ticket_department"/>

    <div class="wpap-field-wrap">
        <label for="wpap-ticket-department"><?php esc_html_e('Tickets department', 'arvand-panel'); ?></label>

        <div>
            <?php $department = $td['departments'][0] ?? __('Admin', 'arvand-panel'); ?>

            <div id="wpap-dep-input-wrap">
                <div>
                    <input id="wpap-ticket-department" class="regular-text" type="text" name="departments[]"
                           value="<?php esc_attr_e($department); ?>"/>
                </div>

                <?php $deps = $td['departments'];

                for ($i = 0; $i < count($deps); $i++): if ($i == 0) continue; ?>
                    <div>
                        <input id="wpap-ticket-department" class="regular-text" type="text" name="departments[]"
                               value="<?php echo esc_attr($deps[$i]); ?>"
                               placeholder="<?php esc_attr_e('Enter department name.', 'arvand-panel'); ?>"/>
                        <a class="wpap-delete-department" href=""><i class="bx bx-trash"></i></a>
                    </div>
                <?php endfor; ?>
            </div>

            <a id="wpap-add-department" class="wpap-btn-2" href="">
                <i class="ri-add-line"></i>
                <?php esc_html_e('Add', 'arvand-panel'); ?>
            </a>
        </div>
    </div>

    <footer>
        <?php require WPAP_ADMIN_TEMPLATES_PATH . 'parts/button.php'; ?>
    </footer>
</form>
