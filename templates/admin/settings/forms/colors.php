<?php
defined('ABSPATH') || exit;

$opt_colors = wpap_colors();

$colors = [
    'color_1' => [__('رنگ اول', 'arvand-panel'), '#f2f8ff'],
    'color_2' => [__('رنگ دوم', 'arvand-panel'), '#0062f5'],
    'bg_color_1' => [__('رنگ پس زمینه اول', 'arvand-panel'), '#ffffff'],
    'bg_color_2' => [__('رنگ پس زمینه دوم', 'arvand-panel'), '#f7f7f7'],
    'text_color_1' => [__('رنگ متن اول', 'arvand-panel'), '#303030'],
    'text_color_2' => [__('رنگ متن دوم', 'arvand-panel'), '#707070'],
    'text_color_3' => [__('رنگ متن سوم', 'arvand-panel'), '#ffffff'],
    'border_color_1' => [__('رنگ حاشیه اول', 'arvand-panel'), '#e6e6e6'],
    'border_color_2' => [__('رنگ حاشیه دوم', 'arvand-panel'), '#ffffff'],
];

$panel_dark_colors = [
    'color_1' => [__('رنگ اول', 'arvand-panel'), '#2e303c'],
    'color_2' => [__('رنگ دوم', 'arvand-panel'), '#3e8dff'],
    'bg_color_1' => [__('رنگ پس زمینه اول', 'arvand-panel'), '#282828'],
    'bg_color_2' => [__('رنگ پس زمینه دوم', 'arvand-panel'), '#202020'],
    'text_color_1' => [__('رنگ متن اول', 'arvand-panel'), '#e5e5e5'],
    'text_color_2' => [__('رنگ متن دوم', 'arvand-panel'), '#a5a5a5'],
    'text_color_3' => [__('رنگ متن سوم', 'arvand-panel'), '#ffffff'],
    'border_color_1' => [__('رنگ حاشیه اول', 'arvand-panel'), '#424242'],
    'border_color_2' => [__('رنگ حاشیه دوم', 'arvand-panel'), '#ffffff'],
];
?>

<div id="wpap-colors">
    <form id="wpap-colors-form" class="wpap-form" method="post">
        <?php wp_nonce_field('colors', 'colors_nonce'); ?>
        <input type="hidden" name="form" value="wpap_colors"/>

        <div class="wpap-fields-group">
            <strong>
                <?php esc_html_e('رنگ های پنل کاربری', 'arvand-panel'); ?>
            </strong>

            <?php foreach ($colors as $color => $value): ?>
                <div class="wpap-field-wrap">
                    <label for="panel_<?php echo $color; ?>">
                        <?php echo esc_html($value[0]); ?>
                    </label>

                    <div>
                        <input id="panel_<?php echo $color; ?>"
                               class="wpap-coloris"
                               type="text"
                               name="panel_<?php echo $color; ?>"
                               value="<?php echo esc_attr($opt_colors["panel_$color"]); ?>"
                               data-default="<?php echo $value[1]; ?>">

                        <i class="ri-reset-right-line wpap-reset-color"></i>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="wpap-fields-group">
            <strong>
                <?php esc_html_e('رنگ های حالت دارک پنل کاربری', 'arvand-panel'); ?>
            </strong>

            <?php foreach ($panel_dark_colors as $color => $value): ?>
                <div class="wpap-field-wrap">
                    <label for="panel_<?php echo $color; ?>">
                        <?php echo esc_html($value[0]); ?>
                    </label>

                    <div>
                        <input id="panel_dark_<?php echo $color; ?>"
                               class="wpap-coloris"
                               type="text"
                               name="panel_dark_<?php echo $color; ?>"
                               value="<?php echo esc_attr($opt_colors["panel_dark_$color"]); ?>"
                               data-default="<?php echo $value[1]; ?>">

                        <i class="ri-reset-right-line wpap-reset-color"></i>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="wpap-fields-group">
            <strong>
                <?php esc_html_e('رنگ های فرم های احراز هویت', 'arvand-panel'); ?>
            </strong>

            <?php foreach ($colors as $color => $value): ?>
                <div class="wpap-field-wrap">
                    <label for="auth_<?php echo $color; ?>">
                        <?php echo esc_html($value[0]); ?>
                    </label>

                    <div>
                        <input id="auth_<?php echo $color; ?>"
                               class="wpap-coloris"
                               type="text"
                               name="auth_<?php echo $color; ?>"
                               value="<?php echo esc_attr($opt_colors["auth_$color"]); ?>"
                               data-default="<?php echo $value[1]; ?>">

                        <i class="ri-reset-right-line wpap-reset-color"></i>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="wpap-fields-group">
            <strong>
                <?php esc_html_e('رنگ های منوی حساب کاربری', 'arvand-panel'); ?>
            </strong>

            <?php foreach ($colors as $color => $value): ?>
                <div class="wpap-field-wrap">
                    <label for="am_<?php echo $color; ?>">
                        <?php echo esc_html($value[0]); ?>
                    </label>

                    <div>
                        <input id="am_<?php echo $color; ?>"
                               class="wpap-coloris"
                               type="text"
                               name="am_<?php echo $color; ?>"
                               value="<?php echo esc_attr($opt_colors["am_$color"]); ?>"
                               data-default="<?php echo $value[1]; ?>">

                        <i class="ri-reset-right-line wpap-reset-color"></i>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <footer>
            <button id="wpap-reset-colors" class="wpap-btn-2" type="button">
                <?php esc_html_e('رنگ های پیشفرض', 'arvand-panel'); ?>
            </button>

            <?php include WPAP_ADMIN_TEMPLATES_PATH . 'parts/button.php'; ?>
        </footer>
    </form>
</div>

<script type="text/javascript">
    Coloris({
        el: '.wpap-coloris',
        theme: 'large',
        themeMode: 'dark',
        swatches: [
            '#264653',
            '#2a9d8f',
            '#e9c46a',
            '#f4a261',
            '#e76f51',
            '#d62828',
            '#023e8a',
            '#0077b6',
            '#0096c7',
            '#00b4d8',
            '#48cae4',
        ]
    });

    jQuery(document).ready(function ($) {
        var colorInputs = $('input.wpap-coloris');

        function wpapShowRestButton () {
            colorInputs.each(function () {
                var resetButton = $(this).parent().parent().find('.wpap-reset-color');
                var defaultColor = $(this).data('default');
                var currentColor = $(this).val();

                if (defaultColor !== currentColor) {
                    resetButton.show(0);
                } else {
                    resetButton.hide(0);
                }
            });
        }

        $('#wpap-reset-colors').on('click', function (e) {
            e.preventDefault();

            colorInputs.each(function () {
                var defaultColor = $(this).data('default');
                $(this).val(defaultColor);
                $(this).parent().css('color', defaultColor)
            });
        })

        $(document).on('click', '.wpap-reset-color', function () {
            var input = $(this).parent().find('.wpap-coloris');
            var defaultColor = input.data('default');
            input.val(defaultColor);
            input.parent().css('color', defaultColor)
            wpapShowRestButton();
        });

        colorInputs.each(function () {
            $(this).on('change', function () {
                wpapShowRestButton();
            })
        });
    });
</script>