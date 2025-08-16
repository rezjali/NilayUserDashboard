<?php
defined('ABSPATH') || exit;

$section = wpap_get_settings_current_section();
$menus = require(WPAP_INC_PATH . 'admin/settings-menus.php');
?>

<div id="wpap-settings" class="wpap-wrap wrap">
    <button id="wpap-settings-show-sidebar">
        <i class="ri-menu-line"></i>
    </button>

    <div>
        <div id="wpap-settings-sidebar">
            <div id="wpap-brand">
                <img src="<?php echo WPAP_ASSETS_URL . 'admin/images/p-logo.jpg'; ?>"
                     alt="<?php esc_attr_e('Arvand Panel', 'arvand-panel'); ?>"/>

                <div>
                    <strong>
                        <?php esc_html_e('Arvand Panel', 'arvand-panel'); ?>
                    </strong>

                    <?php
                    $plugin_data = get_file_data(
                        WPAP_DIR_PATH . 'arvand-panel.php',
                        ['Version' => 'Version'],
                        false
                    );
                    ?>

                    <span>
                        <?php
                        echo sprintf(
                            esc_html__('نسخه %s', 'arvand-panel'),
                            $plugin_data['Version']
                        );
                        ?>
                    </span>
                </div>
            </div>

            <button id="wpap-settings-hide-sidebar">
                <i class="ri-close-large-line"></i>
            </button>

            <nav id="wpap-settings-nav">
                <?php wpap_settings_menu($menus); ?>
            </nav>
        </div>

        <div id="wpap-settings-content">
            <?php wpap_template("admin/settings/forms/$section"); ?>
        </div>
    </div>
</div>