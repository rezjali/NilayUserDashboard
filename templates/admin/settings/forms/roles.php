<?php
defined('ABSPATH') || exit;

$roles_opt = wpap_roles_options();
?>

<form id="wpap-roles-section" class="wpap-settings-section wpap-form" method="post">
    <?php wp_nonce_field('roles_nonce', 'roles_nonce'); ?>
    <input type="hidden" name="form" value="wpap_roles"/>

    <div class="wpap-field-wrap">
        <label for="wpap-new-role-after-delete"><?php esc_html_e('New roles after delete', 'arvand-panel'); ?></label>

        <div>
            <select id="wpap-new-role-after-delete" class="regular-text" name="new_role_after_delete">
                <?php $roles = get_editable_roles(); ?>

                <?php foreach ($roles as $key => $details): ?>
                    <option value="<?php echo esc_attr($key); ?>" <?php selected($roles_opt['new_role'] === $key); ?>>
                        <?php esc_html_e($details['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <p class="description">
                <?php esc_html_e('If any role is deleted in the next option (Roles), this role is applied to users who have had the role deleted.', 'arvand-panel'); ?>
            </p>
        </div>
    </div>

    <div class="wpap-field-wrap">
        <label><?php esc_html_e('Roles', 'arvand-panel'); ?></label>

        <div id="wpap-roles-wrap">
            <?php $count = 0;

            foreach ($roles as $role => $details):
                if (!str_starts_with($role, 'arvand_panel')) {
                    continue;
                }

                $count++;
                ?>
                <div>
                    <span><?php echo esc_html($details['name']); ?></span>
                    <i id="wpap-remove-role" class="ri-close-large-line" data-role="<?php echo esc_attr($role); ?>"
                       data-nonce="<?php echo esc_attr(wp_create_nonce('del_role')); ?>"></i>
                </div>
            <?php endforeach; ?>

            <?php
            if (!$count) {
                echo '<span id="wpap-notfound-role">' . esc_html__('There is no role.', 'arvand-panel') . '</span>';
            }
            ?>
        </div>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-new-role-name"><?php esc_html_e('New role name', 'arvand-panel'); ?></label>

        <div>
            <input id="wpap-new-role-name" class="regular-text" type="text" name="new_role"
                   placeholder="<?php esc_attr_e('eg: first_level', 'arvand-panel'); ?>">

            <p class="description">
                <?php esc_html_e('If you want to add new role this field is required.', 'arvand-panel'); ?>
            </p>
        </div>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-role-display-name"><?php esc_html_e('Role display name', 'arvand-panel'); ?></label>

        <div>
            <input id="wpap-role-display-name" class="regular-text" type="text" name="role_display_name"
                   placeholder="<?php esc_attr_e('eg: First Level', 'arvand-panel'); ?>">

            <p class="description">
                <?php esc_html_e('If you want to add new role this field is required.', 'arvand-panel'); ?>
            </p>
        </div>
    </div>

    <footer>
        <?php require WPAP_ADMIN_TEMPLATES_PATH . 'parts/button.php'; ?>
    </footer>
</form>