<?php

$results = array(
    'active' => array(),
    'inactive' => array(),
    'disabled' => array()
);

$summary = array(
    'active' => 0,
    'inactive' => 0,
    'disabled' => 0,
    'usable' => 0
);

$disabled = apply_filters('sct_disable_reset_tools_'.$scope, $settings['disabled'][$scope]);

foreach (smart_sct_core()->cleanup as $code => $data) {
    if (!in_array($code, $disabled)) {
        $summary['usable']++;

        $class_name = $data['class'];
        $clean = new $class_name($scope, $code);
        $check = $clean->check();
        $help = $clean->help();

        $render = '<div class="sct-cleanup-box'.($check['records'] == 0 ? ' sct-disabled' : '').($check['checked'] ? ' sct-enabled' : '').'">';
            $render.= '<h3>'.$data['label'].'</h3>';
            $render.= '<div class="sct-cleanup-statusbox">';
                $render.= $check['display'];
            $render.= '</div>';
            $render.= '<div class="sct-cleanup-infobox">';
                $render.= $clean->form();
            $render.= '</div>';
            $render.= '<div class="sct-cleanup-checkbox">';
                if ($check['records'] == 0) {
                    $render.= __("No action required", "smart-cleanup-tools");
                } else {
                    $render.= $clean->draw_checkbox($check['checked']);

                    if ($help !== false) {
                        $render.= '<a class="sct-tool-help ui-state-active ui-corner-all" title="'.__("More information available for this tweak.", "smart-cleanup-tools").'" href="#'.$code.'"><span class="ui-icon ui-icon-info"></span></a>';
                    }
                }
            $render.= '</div>';

            if ($help !== false) {
                $render.= '<div title="'.__("Help", "smart-cleanup-tools").': '.$data['label'].'" id="sct-help-'.$code.'" style="display: none;">';
                $render.= $help;
                $render.= '</div>';
            }
        $render.= '</div>';

        if ($check['records'] == 0) {
            if ($settings['cleanup_show_inactive']) {
                $results['inactive'][] = $render;
            }

            $summary['inactive']++;
        } else {
            $results['active'][] = $render;
            $summary['active']++;
        }
    } else {
        $results['disabled'][] = $code;
        $summary['disabled']++;
    }
}

?>
<form method="POST" id="sct-cleanup-form">
    <?php wp_nonce_field("smart-cleanup-tools-cleanup"); ?>

    <input type="hidden" name="sct[scope]" value="<?php echo $scope; ?>" />
    <input type="hidden" name="sct[quick]" value="off" />

    <div class="sct-cleanup-left">
        <p>
            <?php _e("Select reset tools you want to use, and set up options if needed.", "smart-cleanup-tools"); ?>
        </p>

        <?php if ($settings['cleanup_summary']) { ?>
        <div class="sct-cleanup-alls">
            <table style="width: 100%;">
                <tr><td style="text-align: left;"><?php _e("Active Tools", "smart-cleanup-tools"); ?></td>
                    <td style="text-align: right; font-weight: bold;"><?php echo $summary['active']; ?></td></tr>
            </table>
            <table style="width: 100%; border-top: 1px dotted #333;">
                <tr><td style="text-align: left;"><?php _e("Inactive Tools", "smart-cleanup-tools"); ?></td>
                    <td style="text-align: right; font-weight: bold;"><?php echo $summary['inactive']; ?></td></tr>
                <tr><td style="text-align: left;"><?php _e("Disabled Tools", "smart-cleanup-tools"); ?></td>
                    <td style="text-align: right; font-weight: bold;"><?php echo $summary['disabled']; ?></td></tr>
            </table>
        </div>
        <?php } ?>
        <input id="sct-cleanup-run" class="button-primary" type="submit" value="<?php _e("Run Reset", "smart-cleanup-tools"); ?>" />
        <p class="sct-basic-info">
            <?php _e("These tools will reset some of data, and it is good idea to create backup if you decide to revert it back later.", "smart-cleanup-tools"); ?>
        </p>

        <p class="sct-left-info sct-top-notice">
            <?php _e("Time needed to complete cleanup process depends on the size of your database, number of cleanup operations selected and amount of data that will get removed during this process.", "smart-cleanup-tools"); ?>
        </p>
    </div>
    <div class="sct-cleanup-right" style="padding: 15px 0 15px 5px">
        <?php

        echo join('', $results['active']);
        echo join('', $results['inactive']);

        if ($summary['usable'] == 0) {
            echo '<h3 style="margin-top: 0; margin-left: 15px;">'.__("All available reset tools are disabled.", "smart-cleanup-tools").'</h3>';
        }

        ?>
    </div>
</form>