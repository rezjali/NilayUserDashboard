<?php

namespace Arvand\ArvandPanel\Form\Fields;

defined('ABSPATH') || exit;

use Arvand\ArvandPanel\Form\WPAPField;
use Arvand\ArvandPanel\Form\WPAPFieldHtml;
use Arvand\ArvandPanel\Form\WPAPFieldSettingsHtml;
use Arvand\ArvandPanel\Form\WPAPFieldSettingsValidation;

class wpap_field_file extends WPAPField
{
    public $type = 'user_meta';

    public static function defaultSettings(): array
    {
        return [
            'field_name' => 'file',
            'type' => 'file',
            'label' => __('فایل', 'arvand-panel'),
            'meta_key' => '',
            'attrs' => [
                'name' => 'file',
                'type' => 'file',
            ],
            'rules' => [
                'required' => false,
                'extensions' => ['jpg', 'jpeg', 'png', 'gif', 'zip', 'doc', 'docx', 'pdf', 'csv'],
                'min_size' => 100,
                'max_size' => 1024
            ],
            'description' => '',
            'display' => 'both'
        ];
    }

    public static function adminButton(): array
    {
        return ['ri-file-line', __('فایل', 'arvand-panel')];
    }

    public function settingsValidation($field): array
    {
        $validation = new WPAPFieldSettingsValidation($field, $this);

        return [
            $validation->validate(function () use ($validation, $field) {
                if (isset($field['extensions'])) {
                    $validation->new_settings['rules']['extensions'] = array_map('sanitize_text_field', (array)$field['extensions']);
                } else {
                    $validation->new_settings['rules']['extensions'] = [];
                }

                $validation->new_settings['rules']['min_size'] = $field['min_size'] ?? 100;
                $validation->new_settings['rules']['max_size'] = $field['max_size'] ?? 1204;
                return true;
            }),
            $validation->new_settings
        ];
    }

    public function settingsOutput(array $settings = null, $id = null): string
    {
        $html = new WPAPFieldSettingsHtml(self::defaultSettings(), $settings, $id);

        return $html->settings(
            function () use ($html) {
                $inputs = sprintf(
                    '<label>%s</label><span style="margin-top: 5px; display: flex; flex-wrap: wrap; gap: 10px">',
                    esc_html__('فرمت های مجاز', 'arvand-panel')
                );;

                foreach ($html->default_settings['rules']['extensions'] as $extension) {
                    $inputs .= sprintf(
                        '<label><input name="fields[%d][extensions][]" type="checkbox" value="%s" %s/>%s</label>',
                        esc_attr($html->id),
                        esc_attr($extension),
                        checked(in_array($extension, $html->settings['rules']['extensions'] ?? []), true, false),
                        esc_html($extension)
                    );
                }

                $inputs .= '</span>';
                $html->wrap($inputs);

                $html->wrap(sprintf(
                    '<label>%s</label><input name="fields[%s][min_size]" type="number" min="0" value="%s"/>',
                    esc_html__('حداقل حجم فایل بر حسب کیلوبایت (KB)', 'arvand-panel'),
                    esc_attr($html->id),
                    esc_attr($html->settings['rules']['min_size'] ?? 100)
                ));

                $html->wrap(sprintf(
                    '<label>%s</label><input name="fields[%s][max_size]" type="number" min="0" value="%s"/>',
                    esc_html__('حداکثر حجم فایل بر حسب کیلوبایت (KB)', 'arvand-panel'),
                    esc_attr($html->id),
                    esc_attr($html->settings['rules']['max_size'] ?? 1024)
                ));
            },
            function () {
                echo '<input type="file" disabled="disabled"/>';
            }
        );
    }

    public function adminOutput(array $settings, $value = null, $object_id = null): void
    {
        $name = esc_attr($settings['attrs']['name']);
        ob_start();
        ?>

        <input type="file" name="<?php echo $name; ?>"/>

        <?php if ($value): ?>
            <span style="margin: 10px 0; display: block;" class="description">
                 <?php esc_html_e('یک فایل بارگزاری شده است.', 'arvand-panel'); ?>
            </span>

            <a class="button-secondary"
               href="<?php echo esc_url(add_query_arg([['wpap_file_field' => $settings['meta_key'], 'wpap_file' => $object_id]])); ?>"
               role="button">
                <?php esc_html_e('دانلود فایل', 'arvand-panel'); ?>
            </a>

            <a class="button-secondary"
               href="<?php echo esc_url(add_query_arg(['wpap_file_field_delete' => $settings['meta_key'], 'wpap_file' => $object_id])); ?>"
               onclick="return confirm('<?php esc_html_e('آیا از حذف اطمینان داردی؟', 'arvand-panel'); ?>')"
               role="button">
                <?php esc_html_e('حذف فایل', 'arvand-panel'); ?>
            </a>
        <?php endif; ?>

        <p class="description">
            <?php
            $min_size = 1024 * (int)$settings['rules']['min_size'];
            $max_size = 1024 * (int)$settings['rules']['max_size'];

            printf(
                esc_html__('حداقل حجم %s و حداکثر %s. پسوند های مجاز: %s', 'arvand-panel'),
                size_format($min_size),
                size_format($max_size),
                implode(' ،', $settings['rules']['extensions']),
            );
            ?>
        </p>

        <?php
        $html = new WPAPFieldHtml($settings, $value);
        $html->wrap(ob_get_clean());
    }

    public function output(array $settings, $value = null, $object_id = null): void
    {
        $name = esc_attr($settings['attrs']['name']);
        ob_start();
        ?>

        <div class="wpap-upload-attachment">
            <header></header>

            <footer>
                <label class="wpap-btn-1">
                    <input type="file" name="<?php echo $name; ?>" hidden/>
                    <i class="bi bi-file-earmark-arrow-up"></i>
                    <span><?php esc_html_e('آپلود', 'arvand-panel'); ?></span>
                </label>

                <?php if ($value): ?>
                    <a class="wpap-btn-1"
                       href="<?php echo esc_url(add_query_arg([['wpap_file_field' => $settings['meta_key'], 'wpap_file' => $object_id]])); ?>">
                        <i class="bi bi-file-arrow-down"></i>
                        <?php esc_html_e('دانلود فایل', 'arvand-panel'); ?>
                    </a>

                    <a class="wpap-btn-1"
                       href="<?php echo esc_url(add_query_arg(['wpap_file_field_delete' => $settings['meta_key'], 'wpap_file' => $object_id])); ?>"
                       onclick="return confirm('<?php esc_html_e('آیا از حذف اطمینان داردی؟', 'arvand-panel'); ?>')">
                        <i class="bi bi-trash"></i>
                        <?php esc_html_e('حذف فایل', 'arvand-panel'); ?>
                    </a>
                <?php endif; ?>
            </footer>
        </div>

        <?php
        $html = new WPAPFieldHtml($settings, $value);
        $html->wrap(ob_get_clean());
    }

    public function adminValidation($settings = null): bool
    {
        if (empty($_FILES[$settings['attrs']['name']])) {
            return false;
        }

        $file = $_FILES[$settings['attrs']['name']];
        $rule = $settings['rules'];

        if (!in_array(pathinfo($file['name'])['extension'], $rule['extensions'])) {
            return false;
        }

        $size = wp_filesize($file['tmp_name']);
        $min_size = 1024 * (int)$rule['min_size'];
        $max_size = 1024 * (int)$rule['max_size'];

        if ($size < $min_size || $size > $max_size) {
            return false;
        }

        return true;
    }

    public function validation($settings): ?string
    {
        if (empty($_FILES[$settings['attrs']['name']]['tmp_name'])) {
            return null;
        }

        $file = $_FILES[$settings['attrs']['name']];
        $extension = pathinfo($file['name'])['extension'];
        $rule = $settings['rules'];

        if (!in_array($extension, $rule['extensions'])) {
            return sprintf(
                __('پسوند %s مجاز نمی باشد. فرمت های مجاز %s', 'arvand-panel'),
                esc_html($settings['label']),
                trim(implode(' ،', $rule['extensions']))
            );
        }

        $size = wp_filesize($file['tmp_name']);
        $min_size = 1024 * (int)$rule['min_size'];
        $max_size = 1024 * (int)$rule['max_size'];

        if ($size < $min_size) {
            return sprintf(
                __('حجم %s نباید کمتر از %s باشد.', 'arvand-panel'),
                esc_html($settings['label']),
                size_format($min_size)
            );
        }

        if ($size > $max_size) {
            return sprintf(
                __('حجم %s نباید بیشتر از %s باشد.', 'arvand-panel'),
                esc_html($settings['label']),
                size_format($max_size)
            );
        }

        return null;
    }

    public static function removeFiles($field)
    {
        global $wpdb;

        $meta = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT `meta_value` FROM `$wpdb->usermeta` WHERE `meta_key` = %s",
                sanitize_text_field($field['meta_key'])
            )
        );

        if (!$meta || !$file = maybe_unserialize($meta->meta_value)) {
            return;
        }

        if (isset($file['path'])) {
            $file = wp_upload_dir()['basedir'] . DIRECTORY_SEPARATOR . $file['path'];

            if (file_exists($file)) {
                unlink($file);
            }
        }
    }
}