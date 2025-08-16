<?php
defined('ABSPATH') || exit;

global $current_user;
$panel_opt = wpap_panel_options();
$fields = \Arvand\ArvandPanel\Form\WPAPFieldSettings::get();
?>

<div id="wpap-user-edit">
    <div class="wpap-form-wrap">
        <form id="user-edit-form" method="post" enctype="multipart/form-data">
            <header>
                <h2><?php esc_html_e('ویرایش مشخصات', 'arvand-panel'); ?></h2>
            </header>

            <div>
                <?php if ($panel_opt['upload_avatar'] && get_avatar($current_user->ID)): ?>
                    <div class="wpap-field-wrap">
                        <div id="wpap-pro-pic">
                            <?php echo get_avatar($current_user->ID, '80'); ?>

                            <div id="wpap-pro-pic-buttons">
                                <label id="wpap-upload-avatar-btn" class="wpap-btn-1" for="wpap-upload-pro-pic">
                                    <?php esc_html_e('آپلود تصویر', 'arvand-panel'); ?>
                                </label>

                                <?php $profile_img_url = get_user_meta($current_user->ID, 'wpap_profile_img', true); ?>
                                <?php if ($profile_img_url): ?>
                                    <a id="wpap-delete-avatar-btn" class="wpap-btn-1" href="" data-user="<?php echo esc_attr($current_user->ID); ?>">
                                        <?php esc_html_e('حذف', 'arvand-panel'); ?>
                                    </a>
                                <?php endif; ?>
                            </div>

                            <input id="wpap-upload-pro-pic" type="file" name="profile_pic" hidden/>
                        </div>

                        <span class="wpap-input-info">
                            <?php
                            echo sprintf(
                                esc_html__('حداکثر حجم تصویر باید %s باشد. پسوندهای مجاز: jpg ,jpeg و png.', 'arvand-panel'),
                                esc_html(size_format(intval($panel_opt['avatar_size']) * 1024))
                            );
                            ?>
                        </span>
                    </div>
                <?php endif; ?>

                <?php wp_nonce_field('user_edit_nonce', 'user_edit_nonce'); ?>
                <input type="hidden" name="action" value="user_edit"/>

                <?php
                if ($fields) {
                    foreach ($fields as $field) {
                        if (!in_array($field['display'], ['panel', 'both'])) {
                            continue;
                        }

                        if ('user_email' === $field['field_name'] && empty($current_user->user_email)) {
                            continue;
                        }

                        if ('user_pass' === $field['field_name']) {
                            continue;
                        }

                        $field_class = 'Arvand\ArvandPanel\Form\Fields\wpap_field_' . $field['field_name'];
                        if (!class_exists($field_class)) {
                            continue;
                        }

                        $field_class = new $field_class;

                        if ('users' === $field_class->type) {
                            $field_class->output($field, $current_user->{$field['field_name']});
                        }

                        if ('user_meta' === $field_class->type) {
                            $meta = get_user_meta($current_user->ID, $field['meta_key'], 1);

                            if ('mobile' === $field['field_name'] && empty($meta)) {
                                continue;
                            }

                            $field_class->output($field, $meta, $current_user->ID);
                        }

                        do_action('wpap_user_edit_fields', $current_user);
                    }
                }
                ?>

                <footer>
                    <button class="wpap-btn-1" type="submit">
                        <span class="wpap-btn-text"><?php esc_html_e('Edit', 'arvand-panel'); ?></span>
                        <div class="wpap-loading"></div>
                    </button>
                </footer>
            </div>
        </form>
    </div>
</div>