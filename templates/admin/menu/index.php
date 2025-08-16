<?php defined('ABSPATH') || exit; ?>

<div id="wpap-panel-menu-manager" class="wpap-wrap wrap">
    <?php require WPAP_ADMIN_TEMPLATES_PATH . 'menu/' . $section . '.php'; ?>
</div>

<script>
    jQuery(document).ready(function ($) {
        $(document).on("change", "#wpap-menu-type-select", function () {
            var fields = $(".wpap-menu-type-field");
            var field = $("#wpap-menu-type-" + $(this).val());

            fields.hide(0);
            field.fadeIn(500);

            var subMenusField = $("#wpap-submenus-field");
            var parentField = $("#wpap-menu-parent-field");

            if ("parent" === $(this).val()) {
                parentField.hide(0)
                subMenusField.fadeIn(500)
            } else {
                parentField.fadeIn(500)
                subMenusField.hide(0)
            }
        });

        var uploader;
        var iconImgWrap = $('#wpap-icon-img');

        $(document).on('click', '.wpap-upload-btn', function (e) {
            if (uploader) {
                return uploader.open();
            }

            uploader = wp.media({
                library: {
                    type: 'image',
                }
            });

            uploader.on('select', function () {
                var iconImgWrap = $('#wpap-icon-img');
                var attachment = uploader.state().get('selection').first().toJSON();
                iconImgWrap.find('input[name=icon_image_id]').attr('value', attachment.id);
                iconImgWrap.find('.wpap-upload-preview').html('<img src="' + attachment.url + '"/>');
                iconImgWrap.find('.wpap-upload-delete-btn').fadeIn(200);
            });

            uploader.open();
        })

        $(document).on('click', '.wpap-upload-delete-btn', function (e) {
            iconImgWrap.find('input[name=icon_image_id]').attr('value', '');
            iconImgWrap.find('.wpap-upload-preview').html('<?php esc_html_e('تصویری انتخاب نشده.', 'arvand-panel'); ?>');
            $(this).hide();
        });

        $(document).on('change', '#wpwp-icon-color-type-select', function (e) {
            var iconColorField = $('#wpap-icon-color-field');
            '1' === $(this).val() ? iconColorField.fadeIn(200) : iconColorField.fadeOut(200);
        });

        $('.wpap-color-field').wpColorPicker();
    });
</script>