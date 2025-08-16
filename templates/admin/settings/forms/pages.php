<?php
defined('ABSPATH') || exit;

$pages_opt = wpap_pages_options();
?>

<form id="wpap-pages-section" class="wpap-settings-section wpap-form" method="post">
    <?php wp_nonce_field('pages_nonce', 'pages_nonce'); ?>
    <input type="hidden" name="form" value="wpap_pages"/>

    <div class="wpap-field-wrap">
        <label for="wpap-register-page">
            <?php esc_html_e('Register page', 'arvand-panel'); ?>
        </label>

        <div>
            <?php $pages = get_pages(['post_status' => ['publish', 'draft']]);

            if (count($pages) > 0): ?>
                <select id="wpap-register-page" class="regular-text" name="register_page">
                    <option <?php selected(!$pages_opt['register_page_id']) ?>>
                        <?php esc_html_e('None', 'arvand-panel'); ?>
                    </option>

                    <?php foreach ($pages as $page):
                        $page_name = empty($page->post_title) ? __('Untitled', 'arvand-panel') : $page->post_title; ?>

                        <option value="<?php echo esc_attr($page->ID); ?>" <?php selected($pages_opt['register_page_id'], $page->ID); ?>>
                            <?php echo esc_html($page_name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php else: ?>
                <span><?php esc_html_e('There is no page.', 'arvand-panel'); ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-after-register-page">
            <?php esc_html_e('After register page', 'arvand-panel'); ?>
        </label>

        <div>
            <?php $pages = get_pages(['post_status' => ['publish', 'draft']]); ?>

            <?php if (count($pages) > 0): ?>
                <select id="wpap-after-register-page" class="regular-text" name="after_register_page">
                    <option <?php selected(!$pages_opt['after_register_page_id']) ?>>
                        <?php esc_html_e('None', 'arvand-panel'); ?>
                    </option>

                    <?php foreach ($pages as $page): ?>
                        <option value="<?php echo esc_attr($page->ID); ?>" <?php selected($pages_opt['after_register_page_id'], $page->ID); ?>>
                            <?php
                            $page_name = empty($page->post_title) ? __('Untitled', 'arvand-panel') : $page->post_title;
                            echo esc_html($page_name);
                            ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php else: ?>
                <span><?php esc_html_e('There is no page.', 'arvand-panel'); ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-login-page"><?php esc_html_e('Login page', 'arvand-panel'); ?></label>

        <div>
            <?php if (count($pages) > 0): ?>
                <select id="wpap-login-page" class="regular-text" name="login_page">
                    <option <?php selected(!$pages_opt['login_page_id']) ?>>
                        <?php esc_html_e('None', 'arvand-panel'); ?>
                    </option>

                    <?php foreach ($pages as $page):
                        $page_name = empty($page->post_title) ? __('Untitled', 'arvand-panel') : $page->post_title; ?>

                        <option value="<?php echo esc_attr($page->ID); ?>" <?php selected($pages_opt['login_page_id'], $page->ID); ?>>
                            <?php echo esc_html("$page_name"); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php else: ?>
                <span><?php esc_html_e('There is no page.', 'arvand-panel') ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-after-login">
            <?php esc_html_e('After login page', 'arvand-panel'); ?>
        </label>

        <div>
            <?php if (count($pages) > 0): ?>
                <select id="wpap-after-login" class="regular-text" name="after_login">
                    <option <?php selected(!$pages_opt['after_login_page_id']) ?>>
                        <?php esc_html_e('None', 'arvand-panel'); ?>
                    </option>

                    <?php foreach ($pages as $page): ?>
                        <?php $page_name = empty($page->post_title) ? __('Untitled', 'arvand-panel') : $page->post_title; ?>

                        <option value="<?php echo esc_attr($page->ID); ?>" <?php selected($pages_opt['after_login_page_id'], $page->ID); ?>>
                            <?php echo esc_html("$page_name"); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php else: ?>
                <span><?php esc_html_e('There is no page.', 'arvand-panel'); ?></span>
            <?php endif; ?>

            <p class="description">
                <?php esc_html_e('This option is related to logging in through the plugin login form and for non-administrator users.', 'arvand-panel'); ?>
            </p>
        </div>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-sms-register-login-page"><?php esc_html_e('SMS register/login page', 'arvand-panel'); ?></label>

        <div>
            <?php if (count($pages) > 0): ?>
                <select id="wpap-sms-register-login-page" class="regular-text" name="sms_register_login_page">
                    <option <?php selected(!$pages_opt['sms_register_login_page_id']) ?>>
                        <?php esc_html_e('None', 'arvand-panel'); ?>
                    </option>

                    <?php foreach ($pages as $page):
                        $page_name = empty($page->post_title) ? __('Untitled', 'arvand-panel') : $page->post_title; ?>

                        <option value="<?php echo esc_attr($page->ID); ?>" <?php selected($pages_opt['sms_register_login_page_id'], $page->ID); ?>>
                            <?php echo esc_html("$page_name"); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php else: ?>
                <span><?php esc_html_e('There is no page.', 'arvand-panel') ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-after-sms-register-login-page">
            <?php esc_html_e('After SMS register/login page', 'arvand-panel'); ?>
        </label>

        <div>
            <?php if (count($pages) > 0): ?>
                <select id="wpap-after-sms-register-login-page" class="regular-text"
                        name="after_sms_register_login_page">
                    <option <?php selected(!$pages_opt['after_sms_register_login_page_id']) ?>>
                        <?php esc_html_e('None', 'arvand-panel'); ?>
                    </option>

                    <?php foreach ($pages as $page):
                        $page_name = empty($page->post_title) ? __('Untitled', 'arvand-panel') : $page->post_title; ?>

                        <option value="<?php echo esc_attr($page->ID); ?>" <?php selected($pages_opt['after_sms_register_login_page_id'], $page->ID); ?>>
                            <?php echo esc_html("$page_name"); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php else: ?>
                <span><?php esc_html_e('There is no page.', 'arvand-panel') ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-lost-pass">
            <?php esc_html_e('Lost password page', 'arvand-panel'); ?>
        </label>

        <?php if (count($pages) > 0): ?>
            <select id="wpap-lost-pass" name="lost_pass_page">
                <option <?php selected(!$pages_opt['lost_pass_page_id']) ?>>
                    <?php esc_html_e('None', 'arvand-panel'); ?>
                </option>

                <?php foreach ($pages as $page):
                    $page_name = empty($page->post_title) ? __('Untitled', 'arvand-panel') : $page->post_title; ?>

                    <option value="<?php echo esc_attr($page->ID); ?>" <?php selected($pages_opt['lost_pass_page_id'], $page->ID); ?>>
                        <?php echo esc_html("$page_name"); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        <?php else: ?>
            <span><?php esc_html_e('There is no page.', 'arvand-panel') ?></span>
        <?php endif; ?>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-reset-pass-page">
            <?php esc_html_e('Reset password page', 'arvand-panel'); ?>
        </label>

        <div>
            <?php if (count($pages) > 0): ?>
                <select id="wpap-reset-pass-page" class="regular-text" name="reset_pass_page">
                    <option <?php selected(!$pages_opt['reset_pass_page_id']) ?>>
                        <?php esc_html_e('None', 'arvand-panel'); ?>
                    </option>

                    <?php foreach ($pages as $page):
                        $page_name = empty($page->post_title) ? __('Untitled', 'arvand-panel') : $page->post_title; ?>

                        <option value="<?php echo esc_attr($page->ID); ?>" <?php selected($pages_opt['reset_pass_page_id'], $page->ID); ?>>
                            <?php echo esc_html("$page_name"); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php else: ?>
                <span><?php esc_html_e('There is no page.', 'arvand-panel') ?></span>
            <?php endif; ?>

            <p class="description">
                <?php esc_html_e('Proper selection of this page and login page can be required for the correct password recovery actions of the plugin.', 'arvand-panel'); ?>
            </p>
        </div>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-after-login">
            <?php esc_html_e('After logout page', 'arvand-panel'); ?>
        </label>

        <?php if (count($pages) > 0): ?>
            <select id="wpap-after-logout" class="regular-text" name="after_logout">

                <option <?php selected(!$pages_opt['after_logout_page_id']) ?>>
                    <?php esc_html_e('Default', 'arvand-panel'); ?>
                </option>

                <?php foreach ($pages as $page):
                    $page_name = empty($page->post_title) ? __('Untitled', 'arvand-panel') : $page->post_title; ?>

                    <option value="<?php echo esc_attr($page->ID); ?>" <?php selected($pages_opt['after_logout_page_id'], $page->ID); ?>>
                        <?php echo esc_html("$page_name"); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        <?php else: ?>
            <span><?php esc_html_e('There is no page.', 'arvand-panel') ?></span>
        <?php endif; ?>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-panel-page">
            <?php esc_html_e('Panel page', 'arvand-panel'); ?>
        </label>

        <div>
            <?php if (count($pages) > 0): ?>
                <select id="wpap-panel-page" class="regular-text" name="panel_page">
                    <option <?php selected(!$pages_opt['panel_page_id']) ?>>
                        <?php esc_html_e('None', 'arvand-panel'); ?>
                    </option>

                    <?php foreach ($pages as $page):
                        $page_name = empty($page->post_title) ? __('Untitled', 'arvand-panel') : $page->post_title; ?>

                        <option value="<?php echo esc_attr($page->ID); ?>" <?php selected($pages_opt['panel_page_id'], $page->ID); ?>>
                            <?php echo esc_html("$page_name"); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php else: ?>
                <span><?php esc_html_e('There is no page.', 'arvand-panel'); ?></span>
            <?php endif; ?>

            <p class="description">
                <?php esc_html_e('The page that you have considered as the user panel page of Arvand Panel plugin.', 'arvand-panel'); ?>
            </p>
        </div>
    </div>

    <footer>
        <?php require WPAP_ADMIN_TEMPLATES_PATH . 'parts/button.php'; ?>
    </footer>
</form>