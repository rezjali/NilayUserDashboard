<?php
defined('ABSPATH') || exit;

$response = \Arvand\ArvandPanel\Admin\Handlers\WPAPAdminHandler::removeRegisterFields();
?>

<div class="wpap-wrap wrap">
    <h1><?php esc_html_e('Register Fields', 'arvand-panel'); ?></h1>

    <?php
    if (!\Arvand\ArvandPanel\Form\WPAPFieldSettings::get('user_login')
        || !\Arvand\ArvandPanel\Form\WPAPFieldSettings::get('user_email')
        || !\Arvand\ArvandPanel\Form\WPAPFieldSettings::get('user_pass')
    ) {
        wp_admin_notice(
            __('The three fields of username, email and password are required.', 'arvand-panel'),
            ['type' => 'error']
        );
    }

    if (isset($response['ok'])) {
        wp_admin_notice(esc_html($response['msg']), ['type' => 'success', 'dismissible' => true]);
    }
    ?>

    <div id="wpap-register-fields">
        <div id="wpap-reg-fields-sidebar">
            <div id="wpap-buttons-wrap">
                <?php
                $file_list = scandir(WPAP_SRC_PATH . 'Form/Fields');
                unset($file_list[0], $file_list[1]);

                $field_classes = array_map(function ($value) {
                   return substr($value, 0, -4);
                }, $file_list);

                foreach ($field_classes as $field_class) {
                    $button = call_user_func(["Arvand\\ArvandPanel\\Form\Fields\\$field_class", 'adminButton']);
                    $name = substr($field_class, 11, strlen($field_class));
                    printf('<button class="wpap-buttons" data-type="%s"><i class="%s"></i>%s</button>', $name, $button[0], $button[1]);
                }
                ?>
            </div>
        </div>

        <form id="wpap-reg-fields-form" method="post" enctype="multipart/form-data">
            <header>
                <button class="wpap-btn-1" type="submit">
                    <span class="wpap-btn-text">
                        <?php esc_html_e('Save Settings', 'arvand-panel'); ?>
                    </span>

                    <span class="wpap-success-btn-text">
                        <i class='bx bx-check-circle'></i>
                        <?php esc_html_e('Changed', 'arvand-panel'); ?>
                    </span>

                    <div class="wpap-loading"></div>
                </button>

                <button class="wpap-btn-2"
                        form="wpap-reset-field-settings"
                        onclick="return confirm('<?php esc_html_e('Are you sure to reset all settings?', 'arvand-panel'); ?>')">
                    <?php esc_html_e('Reset Settings', 'arvand-panel'); ?>
                </button>
            </header>

            <?php wp_nonce_field('register_field', 'register_field_nonce'); ?>

            <div id="wpap-fields-wrap">
                <?php
                if ($field_settings = \Arvand\ArvandPanel\Form\WPAPFieldSettings::get()) {
                    foreach ($field_settings as $settings) {
                        $field_class = 'Arvand\ArvandPanel\Form\Fields\wpap_field_' . sanitize_text_field($settings['field_name']);

                        if (class_exists($field_class)) {
                            echo call_user_func([new $field_class, 'settingsOutput'], $settings);
                        }
                    }
                }
                ?>
            </div>
        </form>
    </div>
</div>

<form id="wpap-reset-field-settings" method="post">
    <?php wp_nonce_field('reset_field_settings_nonce', 'reset_field_settings'); ?>
    <input type="hidden" style="display: none" name="wpap_reset_field_settings" />
</form>

<script>
    jQuery(document).ready(function ($) {
        <?php
        $unrepeatable = [];

        foreach ($field_classes as $field_name) {
            $class_name = 'Arvand\\ArvandPanel\\Form\Fields\\' . $field_name;
            $instance = new $class_name;

            if (!$instance->repeatable) {
                $field_name = substr($field_name, 11, strlen($field_name));
                $unrepeatable[] = $field_name;
            }
        }
        ?>

        var unrepeatable = JSON.parse('<?php echo json_encode($unrepeatable); ?>');
        var existingFields = [];
        var savedFields = JSON.parse('<?php echo json_encode(\Arvand\ArvandPanel\Form\WPAPFieldSettings::get()); ?>');

        $(document).on("click", ".wpap-buttons", function () {
            var fieldName = $(this).data("type");

            if (existingFields.includes(fieldName) || savedFields.hasOwnProperty(fieldName)) {
                alert('<?php esc_html_e('این فیلد از قبل موجود است.', 'arvand-panel'); ?>');

                return;
            }

            var wrap = $("#wpap-fields-wrap");

            wrap.append(`<div class="wpap-field-loading"></div>`);

            $.ajax({
                type: "post",
                url: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
                data: {
                    action: "wpap_add_register_field",
                    field_name: fieldName,
                },
                success: function (response) {
                    if (response.success) {
                        wrap.find("div.wpap-field-loading").remove();
                        wrap.append(response.data);

                        if (unrepeatable.includes(fieldName)) {
                            existingFields.push(fieldName);
                        }
                    }
                },
                error: function (error) {},
            });
        });

        $(document).on("click", ".wpap-show-field-settings", function () {
            $(this).parents(".wpap-field-preview").next(".wpap-popup-form-wrap").fadeIn(200);
        });

        $(document).on("click", ".wpap-add-option", function () {
            var parent = $(this).parents(".wpap-field");

            $(this).before(`
                <div class="wpap-option">
                    <input type="text" name="fields[${parent.data("field-id")}][options][]"/>
                    <i class='wpap-delete-option ri-delete-bin-7-line'></i>
                </div>
            `);
        });

        $(document).on("click", ".wpap-delete-option", function () {
            $(this).parent().remove();
        });

        $(document).on("click", ".wpap-delete-field", function () {
            if (confirm('<?php esc_html_e('آیا از حذف این فیلد مطمئنید؟', 'arvand-panel'); ?>')) {
                const parent = $(this).parents(".wpap-field");
                parent.remove();

                existingFields = $.grep(existingFields, function (value) {
                    return value !== parent.data("field");
                });

                delete savedFields[parent.data("field")];
            }
        });

        $("#wpap-fields-wrap").sortable({
            handle: ".wpap-field-preview",
            axis: "y",
            cancel: null,
            stop: function () {},
        });
    });
</script>