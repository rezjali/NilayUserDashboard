<?php

namespace Arvand\ArvandPanel\Form;

defined('ABSPATH') || exit;

class WPAPFieldSettingsHtml
{
    public $default_settings;
    public $settings;
    public $id;

    public function __construct($default_settings, $new_settings = null, $id = null)
    {
        $this->default_settings = $default_settings;
        $this->settings = $new_settings ?? $default_settings;
        $this->id = $id ?? $new_settings['id'];
    }

    public function settings(callable $output = null, $preview = null): string
    {
        ob_start();
        ?>
        <div class="wpap-field" data-field="<?php echo esc_attr($this->settings['attrs']['name']); ?>" data-field-id="<?php echo esc_attr($this->id); ?>">
            <div class="wpap-field-preview">
                <?php $this->preview($preview); ?>
            </div>

            <div class="wpap-popup-form-wrap">
                <div>
                    <div class="wpap-field-settings wpap-popup-form">
                        <input class="wpap-field-id" style="display: none" type="hidden"
                               name="fields[<?php echo esc_attr($this->id); ?>][id]"
                               value="<?php echo esc_attr($this->id); ?>"/>
                        <input class="wpap-field-field-name" style="display: none" type="hidden"
                               name="fields[<?php echo esc_attr($this->id); ?>][field_name]"
                               value="<?php echo esc_attr($this->default_settings['field_name']); ?>"/>

                        <?php
                        $required_field = in_array($this->default_settings['field_name'], ['user_login', 'user_email', 'user_pass']);

                        if (isset($this->default_settings['display']) && !$required_field) {
                            $this->wrap($this->display());
                        }

                        if (isset($this->default_settings['label'])) {
                            $this->wrap($this->label());
                        }

                        if (isset($this->default_settings['attrs']['placeholder'])) {
                            $this->wrap($this->placeholder());
                        }

                        if (isset($this->default_settings['options'])) {
                            $this->wrap($this->options());
                        }

                        if ($output) {
                            call_user_func($output);
                        }

                        if (isset($this->default_settings['meta_key'])) {
                            $this->wrap($this->metaKey());
                        }

                        if (isset($this->default_settings['description'])) {
                            $this->wrap($this->description());
                        }

                        if (isset($this->default_settings['rules']['required']) && !$required_field) {
                            $this->wrap($this->required());
                        }

                        if (isset($this->default_settings['rules']['min_length'])) {
                            $this->wrap($this->minLength());
                        }

                        if (isset($this->default_settings['rules']['max_length'])) {
                            $this->wrap($this->maxLength());
                        }
                        ?>

                        <button class="wpap-close-popup wpap-btn-1">
                            <?php esc_html_e('Done', 'arvand-panel'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <?php return ob_get_clean();
    }

    public function preview(callable $preview = null): void
    {
        $text = $this->settings['label'] ?? $this->default_settings['label']; ?>

        <header>
            <span class='wpap-field-title'><?php echo esc_html($text); ?></span>

            <?php
            if ($this->settings['rules']['required']) {
                printf('<span>(%s)</span>', esc_html__('required', 'arvand-panel'));
            }

            $this->actions();
            ?>
        </header>

        <?php
        if ($preview) {
            call_user_func($preview);
        } else {
            if ('radio' === $this->default_settings['type']) {
                foreach ($this->settings['options'] as $option) {
                    printf("<label class='wpap-preview-field-options'><input type='radio' onclick='return false;' />%s</label>", esc_html($option));
                }
            } elseif ('checkbox' === $this->default_settings['type']) {
                foreach ($this->settings['options'] as $option) {
                    printf("<label class='wpap-preview-field-options'><input type='checkbox' onclick='return false;' />%s</label>", esc_html($option));
                }
            } else if ('select' === $this->default_settings['type']) {
                printf("<select readonly><option>%s</option></select>", esc_html($this->settings['select_text']));
            } else if ('textarea' === $this->default_settings['type']) {
                printf("<textarea placeholder='%s' rows='5' readonly></textarea>", esc_html($this->settings['attrs']['placeholder']));
            } else {
                printf("<input type='text' placeholder='%s' readonly/>", esc_html($this->settings['attrs']['placeholder']));
            }
        }

        if (!empty($this->settings['description'])) {
            printf("<p>%s</p>", esc_html($this->settings['description']));
        }
    }

    private function actions(): void
    {
        ?>
        <div class="wpap-field-actions">
            <i class="wpap-show-field-settings bx bx-cog"></i>

            <?php
            if (!in_array($this->default_settings['field_name'], ['user_login', 'user_email', 'user_pass'])) {
                echo "<i class='wpap-delete-field ri-delete-bin-7-line'></i>";
            }
            ?>
        </div>
        <?php
    }

    public function wrap($field, $wrap_attr = null): void
    {
        printf("<p %s>%s</p>", sanitize_text_field($wrap_attr), $field);
    }

    private function display(): string
    {
        ob_start(); ?>
        <label><?php esc_html_e('Display', 'arvand-panel'); ?></label>

        <select class="wpap-field-display" name="fields[<?php echo esc_attr($this->id); ?>][display]">
            <option value="both" <?php selected('both' === $this->settings['display']); ?>>
                <?php esc_html_e('Register and user edit form', 'arvand-panel'); ?>
            </option>

            <option value="register" <?php selected('register' === $this->settings['display']); ?>>
                <?php esc_html_e('Register form', 'arvand-panel'); ?>
            </option>

            <option value="panel" <?php selected('panel' === $this->settings['display']); ?>>
                <?php esc_html_e('User edit form', 'arvand-panel'); ?>
            </option>
        </select>

        <?php return ob_get_clean();
    }

    private function label(): string
    {
        return sprintf(
            '<label>%s</label><input class="wpap-field-label" type="text" name="fields[%s][label]" value="%s"/>',
            esc_html__('Label', 'arvand-panel'),
            esc_attr($this->id),
            esc_attr($this->settings['label'])
        );
    }

    private function placeholder(): string
    {
        return sprintf(
            '<label>%s</label><input class="wpap-field-placeholder" type="text" name="fields[%s][placeholder]" value="%s"/>',
            esc_html__('Placeholder', 'arvand-panel'),
            esc_attr($this->id),
            esc_attr($this->settings['attrs']['placeholder'])
        );
    }

    private function options(): string
    {
        ob_start(); ?>
        <label><?php esc_html_e('گزینه ها را وارد کنید', 'arvand-panel') ?></label>

        <div class="wpap-options">
            <?php foreach ($this->settings['options'] as $option): ?>
                <div class="wpap-option">
                    <input type="text" name="fields[<?php echo esc_attr($this->id); ?>][options][]" value="<?php echo esc_attr($option); ?>" />
                    <i class='wpap-delete-option ri-delete-bin-7-line'></i>
                </div>
            <?php endforeach; ?>

            <button class="wpap-add-option wpap-btn-2">
                <i class="ri-add-line"></i>
                <?php esc_html_e('Add', 'arvand-panel'); ?>
            </button>
        </div>

        <?php return ob_get_clean();
    }

    private function description(): string
    {
        return sprintf(
            '<label>%s</label><textarea class="wpap-field-description" name="fields[%s][description]">%s</textarea>',
            esc_html__('Description', 'arvand-panel'),
            esc_attr($this->id),
            esc_attr($this->settings['description'])
        );
    }

    private function required(): string
    {
        return sprintf(
            '<label><input class="wpap-field-required" name="fields[%s][required]" type="checkbox" %s/>%s</label>',
            esc_attr($this->id),
            checked($this->settings['rules']['required'], true, false),
            esc_html__('Required?', 'arvand-panel')
        );
    }

    private function minLength(): string
    {
        return sprintf(
            '<label>%s</label><input class="wpap-field-min-length" type="number" name="fields[%s][min_length]" value="%s" min="0"/>',
            esc_html__('Min length', 'arvand-panel'),
            esc_attr($this->id),
            esc_attr($this->settings['rules']['min_length'])
        );
    }

    private function maxLength(): string
    {
        return sprintf(
            '<label>%s</label><input class="wpap-field-max-length" type="number" name="fields[%s][max_length]" value="%s" min="0"/>',
            esc_html__('Max length', 'arvand-panel'),
            esc_attr($this->id),
            esc_attr($this->settings['rules']['max_length'])
        );
    }

    private function metaKey(): string
    {
        return sprintf(
            '<label>%s</label><input class="wpap-field-meta-key" type="text" name="fields[%s][meta_key]" value="%s"/>',
            esc_html__('Meta Key', 'arvand-panel'),
            esc_attr($this->id),
            esc_attr(!empty($this->settings['meta_key']) ? $this->settings['meta_key'] : "wpap_rf_{$this->id}")
        );
    }
}