<?php defined('ABSPATH') || exit; ?>

<form class="wpap-container" method="post" enctype="multipart/form-data">
    <header id="wpap-menu-header">
        <a href="<?php echo esc_url(add_query_arg('section', 'list')); ?>">
            <i class="ri-arrow-right-line"></i>
            <?php esc_html_e('منوها', 'arvand-panel'); ?>
        </a>

        <?php if ($menu->menu_parent > 0): ?>
            <a href="<?php echo add_query_arg(['section' => 'edit', 'menu' => $menu->menu_parent]); ?>">
                <i class="ri-menu-line"></i>
                <?php esc_html_e('منوی والد', 'arvand-panel'); ?>
            </a>
        <?php endif; ?>

        <a href="<?php echo add_query_arg('section', 'new'); ?>">
            <i class="ri-add-line"></i>
            <?php esc_html_e('منوی جدید', 'arvand-panel'); ?>
        </a>

        <button type="submit" name="edit_menu">
            <i class="ri-save-line"></i>
            <?php esc_html_e('ذخیره تغییرات', 'arvand-panel'); ?>
        </button>
    </header>

    <div>
        <?php
        if ($response) {
            wpap_admin_notice($response['msg'], $response['ok'] ? 'success' : 'error');
        }
        ?>

        <strong>
            <?php
            $icon = \Arvand\ArvandPanel\WPAPMenu::icon($menu);

            $icon_image = wp_get_attachment_image($icon['image_id'], 'thumbnail', false, [
                'style' => 'width: auto; height: 20px;'
            ]);

            echo $icon_image ?: sprintf(
                '<i style="%s" class="%s"></i>',
                esc_attr('color: ' . $icon['color']), esc_attr($icon['classes'])
            );

            printf('<span>%s</span>', esc_html($menu->menu_title));
            ?>
        </strong>

        <?php wp_nonce_field('edit_menu', 'edit_menu_nonce'); ?>

        <div class="wpap-field-wrap">
            <label><?php _e('Title', 'arvand-panel'); ?></label>
            <input name="menu_title" type="text" value="<?php echo esc_attr($menu->menu_title); ?>"/>
        </div>

        <div class="wpap-field-wrap">
            <label><?php esc_html_e('نامک', 'arvand-panel'); ?></label>
            <input name="route" type="text" value="<?php echo esc_attr(urldecode($menu->route)); ?>"/>
            <p class="description"><?php esc_html_e('بهتر است به انگلیسی وارد شود.', 'arvand-panel'); ?></p>
        </div>

        <div class="wpap-field-wrap">
            <label><?php esc_html_e('کلاس های css آیکن', 'arvand-panel'); ?></label>
            <input dir="ltr" name="menu_icon" type="text"  value="<?php echo esc_attr($icon['classes']); ?>"/>

            <p class="description">
                <?php
                printf(
                    __('برای قرار دادن آیکن می توانید از این %s استفاده کنید.', 'arvand-panel'),
                    sprintf(
                        '<a href="https://remixicon.com" target="_blank">%s</a>',
                        esc_html__('وبسایت', 'arvand-panel')
                    )
                )
                ?>

                <span style="color: darkred">پکیج آیکن های قبلی (boxicons & bootstrap icons) که در افزونه استفاده میشد، اکنون هم بطور موقت قابل استفاده هستند اما جهت بهینه سازی افزونه، در بروزرسانی های آینده حذف خواهد شد.</span>
            </p>
        </div>

        <div class="wpap-field-wrap">
            <label><?php esc_html_e('رنگ آیکن', 'arvand-panel'); ?></label>

            <select id="wpwp-icon-color-type-select" name="icon_color_type">
                <option value="0" <?php echo selected(0 == $icon['color_type']); ?>>
                    <?php esc_html_e('استفاده از رنگ عمومی (پیشفرض)', 'arvand-panel'); ?>
                </option>

                <option value="1" <?php echo selected(1 == $icon['color_type']); ?>>
                    <?php esc_html_e('رنگ اختصاصی', 'arvand-panel'); ?>
                </option>
            </select>
        </div>

        <div style="<?php echo 0 == $icon['color_type'] ? 'display: none' : ''; ?>" id="wpap-icon-color-field" class="wpap-field-wrap">
            <label><?php esc_html_e('رنگ اختصاصی برای این آیکن', 'arvand-panel'); ?></label>
            <input class="wpap-color-field" name="icon_color" value="<?php echo esc_attr($icon['color']); ?>" data-default-color="#ffffff">
        </div>

        <div class="wpap-field-wrap">
            <label><?php esc_html_e('تصویر آیکن', 'arvand-panel'); ?></label>

            <div id="wpap-icon-img" class="wpap-upload-wrap">
                <input name="icon_image_id" type="hidden" value="<?php echo esc_attr($icon['image_id']); ?>" min="0"/>

                <div class="wpap-upload-preview">
                    <?php
                    $placeholder = esc_html__('تصویری انتخاب نشده.', 'arvand-panel');

                    if ($icon['image_id'] > 0) {
                        $icon_image = wp_get_attachment_image($icon['image_id'], 'thumbnail', true, [
                            'style' => 'width: auto'
                        ]);

                        echo $icon_image ?: $placeholder;;
                    } else {
                        echo $placeholder;
                    }
                    ?>
                </div>

                <footer>
                    <button class="wpap-upload-btn wpap-btn-2" type="button">
                        <?php esc_html_e('آپلود تصویر', 'arvand-panel'); ?>
                    </button>

                    <button style="<?php echo $icon['image_id'] > 0 ? 'display: inline' : ''; ?>" class="wpap-upload-delete-btn wpap-btn-2" type="button">
                        <?php esc_html_e('حذف تصویر', 'arvand-panel'); ?>
                    </button>
                </footer>
            </div>

            <p class="description">
                <?php esc_html_e('اگر تصویر آیکن را آپلود کنید، در منوها از کلاس های آیکن استفاده نخواهد شد.', 'arvand-panel'); ?>
            </p>
        </div>

        <div class="wpap-field-wrap">
            <label><?php esc_html_e('Access for users', 'arvand-panel'); ?></label>

            <select style="height: 200px" name="menu_access[]" multiple>
                <?php
                $roles = get_editable_roles();

                foreach ($roles as $key => $details):
                    $access = unserialize($menu->menu_access);
                    $selected = (!is_array($access) or !count($access)) ? 'selected' : selected(in_array($key, $access)); ?>

                    <option value="<?php esc_attr_e($key); ?>" <?php echo $selected; ?>>
                        <?php echo esc_html($details['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php if ('dash' !== $menu->menu_name): ?>
            <div class="wpap-field-wrap">
                <label><?php esc_html_e('Display Menu', 'arvand-panel'); ?></label>

                <select name="menu_display">
                    <option value="show" <?php selected($menu->menu_display, 'show'); ?>>
                        <?php esc_html_e('Show in panel', 'arvand-panel'); ?>
                    </option>

                    <option value="hide" <?php selected($menu->menu_display, 'hide'); ?>>
                        <?php esc_html_e('Hide in panel', 'arvand-panel'); ?>
                    </option>
                </select>
            </div>
        <?php endif;

        $db = new \Arvand\ArvandPanel\DB\WPAPMenuDB();

        if ($menu->menu_type !== 'default'): ?>
            <div class="wpap-field-wrap">
                <label><?php esc_html_e('Menu Type', 'arvand-panel'); ?></label>

                <select id="wpap-menu-type-select" name="menu_type">
                    <option><?php esc_html_e('Select menu type', 'arvand-panel'); ?></option>

                    <option value="shortcode" <?php selected($menu->menu_type, 'shortcode') ?>>
                        <?php esc_html_e('کد کوتاه', 'arvand-panel'); ?>
                    </option>

                    <option value="text" <?php selected($menu->menu_type, 'text') ?>>
                        <?php esc_html_e('متن', 'arvand-panel'); ?>
                    </option>

                    <option value="link" <?php selected($menu->menu_type, 'link') ?>>
                        <?php esc_html_e('Link to other page', 'arvand-panel'); ?>
                    </option>

                    <option value="page" <?php selected($menu->menu_type, 'page'); ?>>
                        <?php esc_html_e('پست / برگه', 'arvand-panel'); ?>
                    </option>

                    <option value="parent" <?php selected($menu->menu_type, 'parent') ?>>
                        <?php esc_html_e('Parent', 'arvand-panel'); ?>
                    </option>
                </select>
            </div>

            <div style="<?php echo 'shortcode' !== $menu->menu_type ? 'display: none' : ''; ?>" id="wpap-menu-type-shortcode" class="wpap-menu-type-field wpap-field-wrap">
                <label><?php esc_html_e('Shortcode', 'arvand-panel'); ?></label>
                <textarea dir="ltr" name="menu_content_shortcode" rows="5"><?php echo esc_html(wp_unslash('shortcode' === $menu->menu_type ? $menu->menu_content : '')); ?></textarea>
            </div>

            <div style="<?php echo 'link' !== $menu->menu_type ? 'display: none' : ''; ?>" id="wpap-menu-type-link" class="wpap-menu-type-field wpap-field-wrap">
                <label><?php esc_html_e('Menu link', 'arvand-panel'); ?></label>
                <input dir="ltr" name="menu_content_link" type="text" value="<?php echo esc_attr('link' === $menu->menu_type ? $menu->menu_content : ''); ?>" />
            </div>

            <div style="<?php echo 'text' !== $menu->menu_type ? 'display: none' : ''; ?>" id="wpap-menu-type-text" class="wpap-menu-type-field wpap-field-wrap">
                <label><?php esc_html_e('Menu text', 'arvand-panel'); ?></label>

                <?php
                wp_editor(wp_kses_post('text' === $menu->menu_type ? $menu->menu_content : ''), 'wpap-menu-content-text', [
                    'wpautop' => true,
                    'textarea_name' => 'menu_content_text',
                    'editor_height' => 300,
                    'quicktags' => false,
                ]);
                ?>
            </div>

            <div style="<?php echo 'page' !== $menu->menu_type ? 'display: none' : ''; ?>" id="wpap-menu-type-page" class="wpap-menu-type-field wpap-field-wrap">
                <label><?php esc_html_e('شناسه پست / برگه (ID)', 'arvand-panel'); ?></label>
                <input dir="ltr" type="text" name="menu_post_id" value="<?php echo esc_attr($menu->menu_post_id); ?>"/>

                <p class="description">
                    <?php esc_html_e('شناسه پست یا برگه را میتوانید از آدرس قسمت ویرایش آن پست / برگه / پست تایپ در پیشخوان وردپرس مشاهده کنید.', 'arvand-panel'); ?>
                </p>
            </div>

            <div style="<?php echo 'parent' !== $menu->menu_type ? 'display: none' : ''; ?>" id="wpap-submenus-field" class="wpap-field-wrap">
                <label><?php esc_html_e('زیر منوها', 'arvand-panel'); ?></label>

                <select style="height: 200px" name="sub_menus[]" multiple>
                    <?php foreach ($db->getNonParentMenus($menu->menu_id) as $menu2): ?>
                        <option value="<?php echo esc_attr($menu2->menu_id); ?>" <?php echo selected($menu2->menu_parent, $menu->menu_id); ?>>
                            <?php echo esc_html($menu2->menu_title); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>

        <div style="<?php echo 'parent' === $menu->menu_type ? 'display: none' : ''; ?>" id="wpap-menu-parent-field" class="wpap-field-wrap">
            <label>
                <?php esc_html_e('Menu Parent', 'arvand-panel'); ?>
            </label>

            <select name="menu_parent">
                <option value="0"><?php esc_html_e('ّFirst level', 'arvand-panel'); ?></option>

                <?php var_dump($db->getParents());foreach ($db->getParents() as $parent): ?>
                    <option value="<?php echo esc_attr($parent->menu_id); ?>" <?php selected($parent->menu_id, $menu->menu_parent); ?>>
                        <?php echo esc_html($parent->menu_title); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</form>
