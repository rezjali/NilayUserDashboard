<?php
defined('ABSPATH') || exit;

$response = \Arvand\ArvandPanel\Admin\Handlers\WPAPMenuHandler::newMenu();
?>

<form id="wpap-new-menu-form" class="wpap-container" method="post" enctype="multipart/form-data">
    <header id="wpap-menu-header">
        <a href="<?php echo esc_url(add_query_arg('section', 'list')); ?>">
            <i class="ri-arrow-right-line"></i>
            <?php esc_html_e('منوها', 'arvand-panel'); ?>
        </a>

        <button type="submit" name="add_new_menu">
            <i class="ri-save-line"></i>
            <?php esc_html_e('ذخیره منو', 'arvand-panel'); ?>
        </button>
    </header>

    <div>
        <?php
        if ($response) {
            wpap_admin_notice($response['msg'], $response['ok'] ? 'success' : 'error');
        }
        ?>

        <strong><?php esc_html_e('ایجاد منوی جدید', 'arvand-panel'); ?></strong>

        <?php wp_nonce_field('new_menu', 'new_menu_nonce'); ?>
        
        <div class="wpap-field-wrap">
            <label><?php esc_html_e('Title', 'arvand-panel'); ?></label>
            <input type="text" name="menu_title"/>
        </div>

        <div class="wpap-field-wrap">
            <label><?php esc_html_e('نامک', 'arvand-panel'); ?></label>
            <input name="route" type="text"/>
            <p class="description"><?php esc_html_e('بهتر است به انگلیسی وارد شود.', 'arvand-panel'); ?></p>
        </div>

        <div class="wpap-field-wrap">
            <label><?php esc_html_e('کلاس های css آیکن', 'arvand-panel'); ?></label>
            <input dir="ltr" type="text" name="menu_icon"/>

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
                <option value="0">
                    <?php esc_html_e('استفاده از رنگ عمومی (پیشفرض)', 'arvand-panel'); ?>
                </option>

                <option value="1">
                    <?php esc_html_e('رنگ اختصاصی', 'arvand-panel'); ?>
                </option>
            </select>
        </div>

        <div style="display: none" id="wpap-icon-color-field" class="wpap-field-wrap">
            <label><?php esc_html_e('رنگ اختصاصی برای این آیکن', 'arvand-panel'); ?></label>
            <input class="wpap-color-field" name="icon_color" data-default-color="#ffffff">
        </div>

        <div class="wpap-field-wrap">
            <label><?php esc_html_e('تصویر آیکن', 'arvand-panel'); ?></label>

            <div id="wpap-icon-img" class="wpap-upload-wrap">
                <input name="icon_image_id" style="display: none" type="number" min="0"/>

                <div class="wpap-upload-preview">
                    <?php esc_html_e('تصویری انتخاب نشده.', 'arvand-panel'); ?>
                </div>

                <footer>
                    <button class="wpap-upload-btn wpap-btn-2" type="button">
                        <?php esc_html_e('آپلود تصویر', 'arvand-panel'); ?>
                    </button>

                    <button class="wpap-upload-delete-btn wpap-btn-2" type="button">
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
                <?php $roles = get_editable_roles(); ?>

                <?php foreach ($roles as $key => $details): ?>
                    <option value="<?php echo esc_attr($key); ?>" selected="selected">
                        <?php echo esc_html($details['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="wpap-field-wrap">
            <label><?php esc_html_e('Display Menu', 'arvand-panel'); ?></label>

            <select name="menu_display">
                <option value="show">
                    <?php esc_html_e('Show in panel', 'arvand-panel'); ?>
                </option>

                <option value="hide">
                    <?php esc_html_e('Hide in panel', 'arvand-panel'); ?>
                </option>
            </select>
        </div>

        <div class="wpap-field-wrap">
            <label><?php esc_html_e('Menu Type', 'arvand-panel'); ?></label>

            <select id="wpap-menu-type-select" name="menu_type">
                <option value="shortcode">
                    <?php esc_html_e('Shortcode', 'arvand-panel'); ?>
                </option>

                <option value="link">
                    <?php esc_html_e('Link to other page', 'arvand-panel'); ?>
                </option>

                <option value="text">
                    <?php esc_html_e('Text', 'arvand-panel'); ?>
                </option>

                <option value="page">
                    <?php esc_html_e('برگه / پست', 'arvand-panel'); ?>
                </option>

                <option value="parent">
                    <?php esc_html_e('Parent', 'arvand-panel'); ?>
                </option>
            </select>
        </div>

        <div id="wpap-menu-type-shortcode" class="wpap-menu-type-field wpap-field-wrap">
            <label><?php esc_html_e('Shortcode', 'arvand-panel'); ?></label>
            <textarea dir="ltr" name="menu_content_shortcode" rows="5"></textarea>
        </div>

        <div style="display: none" id="wpap-menu-type-link" class="wpap-menu-type-field wpap-field-wrap">
            <label><?php esc_html_e('Menu link', 'arvand-panel'); ?></label>
            <input dir="ltr" name="menu_content_link" type="text"/>
        </div>

        <div style="display: none" id="wpap-menu-type-text" class="wpap-menu-type-field wpap-field-wrap">
            <label><?php esc_html_e('Menu text', 'arvand-panel'); ?></label>

            <?php
            wp_editor('', 'wpap-menu-content', [
                'wpautop' => true,
                'textarea_name' => 'menu_content_text',
                'editor_height' => 300,
                'quicktags' => false
            ]);
            ?>
        </div>

        <div style="display: none" id="wpap-menu-type-page" class="wpap-menu-type-field wpap-field-wrap">
            <label><?php esc_html_e('شناسه پست / برگه (ID)', 'arvand-panel'); ?></label>
            <input dir="ltr" type="text" name="menu_post_id"/>

            <p class="description">
                <?php esc_html_e('شناسه پست یا برگه را میتوانید از آدرس قسمت ویرایش آن پست / برگه / پست تایپ در پیشخوان وردپرس مشاهده کنید.', 'arvand-panel'); ?>
            </p>
        </div>

        <div style="display: none" id="wpap-submenus-field" class="wpap-field-wrap">
            <label><?php esc_html_e('زیر منوها', 'arvand-panel'); ?></label>

            <select style="height: 200px" name="sub_menus[]" multiple>
                <?php $db = new \Arvand\ArvandPanel\DB\WPAPMenuDB(); ?>

                <?php foreach ($db->getNonParentMenus() as $menu): ?>
                    <option value="<?php esc_attr_e($menu->menu_id); ?>">
                        <?php echo esc_html($menu->menu_title); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div id="wpap-menu-parent-field" class="wpap-field-wrap">
            <label><?php esc_html_e('Menu Parent', 'arvand-panel'); ?></label>

            <select name="menu_parent">
                <option value="0"><?php esc_html_e('ّFirst level', 'arvand-panel'); ?></option>

                <?php foreach ($db->getParents() as $parent): ?>
                    <option value="<?php esc_attr_e($parent->menu_id); ?>">
                        <?php echo esc_html($parent->menu_title); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</form>