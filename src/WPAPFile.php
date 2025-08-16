<?php

namespace Arvand\ArvandPanel;

defined('ABSPATH') || exit;

class WPAPFile
{
    public static function upload(array $file, string $path)
    {
        $wp_upload_dir = wp_get_upload_dir();

        $upload_dir = $wp_upload_dir['basedir'] . '/arvand-panel/' . $path . '/';
        if (!wp_mkdir_p($upload_dir)) {
            return false;
        }

        $htaccess = $upload_dir . '.htaccess';
        if (!file_exists($htaccess)) {
            file_put_contents($htaccess, 'deny from all');
        }

        $sub_dir = $wp_upload_dir['subdir'] . '/';

        if (!wp_mkdir_p($upload_dir . $sub_dir)) {
            return false;
        }

        $info = pathinfo($file['name']);
        $name = $sub_dir . date('Y-m-d-H-i-s-') . time() . '-' . uniqid() . '-' . $info['basename'];

        if (false === move_uploaded_file($file['tmp_name'], $upload_dir . $name)) {
            return false;
        }

        return [
            'path' => 'arvand-panel/' . $path . $name,
            'url' => $wp_upload_dir['baseurl'] . '/arvand-panel/' . $path . $name
        ];
    }

    public static function delete(string $path): bool
    {
        $file = wp_upload_dir()['basedir'] . DIRECTORY_SEPARATOR . $path;
        if (!file_exists($file)) {
            return false;
        }

        return unlink($file);
    }
}