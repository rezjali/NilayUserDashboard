<?php
defined('ABSPATH') || exit;

global $current_user;
$dash_box = wpap_dash_box_options();
?>

<div id="wpap-dash-info-wrap" class="wpap-mb-30 wpap-grid wpap-col-sm-2 wpap-col-lg-3 wpap-col-xl-4 wpap-gap-20">
    <?php
    foreach ($dash_box as $key => $box) {
        if ($box['display'] === 'show') {
            $box_file = str_replace('_', '-', $box['name']);
            wpap_template("panel/dash/info-boxes/$box_file", compact("box"));
        }
    }
    ?>
</div>
