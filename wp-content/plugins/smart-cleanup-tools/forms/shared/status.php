<?php

$results = array(
    'active' => array(),
    'inactive' => array(),
    'disabled' => array(),
    'quick' => array()
);

$summary = array(
    'quick' => 0,
    'active' => 0,
    'inactive' => 0,
    'disabled' => 0,
    'records' => 0,
    'size' => 0,
    'usable' => 0
);

$disabled = apply_filters('sct_disable_cleanup_tools_'.$scope, $settings['disabled'][$scope]);

foreach (smart_sct_core()->cleanup as $code => $data) {
    if (!in_array($code, $disabled)) {
        $summary['usable']++;

        $class_name = $data['class'];
        $clean = new $class_name($scope, $code);

        $details = false;
        $check = $clean->check();
        $help = $clean->help();

        if ($check['records'] > 0) {
            $details = $clean->details();
        }

        $quick = '';
        $render = '<div class="sct-cleanup-box'.($check['records'] == 0 ? ' sct-disabled' : '').($check['checked'] ? ' sct-enabled' : '').'">';
            $render.= '<h3>'.$data['label'].'</h3>';
            $render.= '<div class="sct-cleanup-statusbox">';
                $render.= $check['display'];

                if ($details !== false) {
                    $render.= '<a class="sct-tool-detail ui-state-active ui-corner-all" title="'.__("See detailed report for this tool.", "smart-cleanup-tools").'" href="#'.$code.'"><span class="ui-icon ui-icon-search"></span></a>';
                }
            $render.= '</div>';
            $render.= '<div class="sct-cleanup-infobox">';
                $render.= $clean->form();
            $render.= '</div>';
            $render.= '<div class="sct-cleanup-checkbox">';
                if ($check['records'] == 0) {
                    $render.= __("No cleanup required", "smart-cleanup-tools");
                } else {
                    $render.= $clean->draw_checkbox($check['checked']);

                    $quick.= $clean->form_quick();
                    $quick.= $clean->draw_checkbox_quick();
                }

                if ($help !== false) {
                    $render.= '<a class="sct-tool-help ui-state-active ui-corner-all" title="'.__("More information available for this tweak.", "smart-cleanup-tools").'" href="#'.$code.'"><span class="ui-icon ui-icon-info"></span></a>';
                }
            $render.= '</div>';

            if ($help !== false) {
                $render.= '<div title="'.$data['label'].'" id="sct-help-'.$code.'" style="display: none;">';
                $render.= $help;
                $render.= '</div>';
            }

            if ($details !== false || (is_array($details) && !empty($details))) {
                $render.= '<div title="'.$data['label'].'" id="sct-details-'.$code.'" style="display: none;">';
                    $render.= '<table class="widefat">';

                    if (is_array($details['__header__'])) {
                        $render.= '<thead><tr>';
                            foreach ($details['__header__'] as $label) {
                                $render.= '<th>'.$label.'</th>';
                            }
                        $render.= '</tr></thead>';
                    }

                    $render.= '<tbody>';
                        foreach ($details as $key => $row) {
                            if ($key != '__header__') {
                                $render.= '<tr>';
                                foreach ($row as $row_key => $row_val) {
                                    $render.= '<td>'.$row_val.'</td>';
                                }
                                $render.= '</tr>';
                            }
                        }
                    $render.= '</tbody></table>';
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
            $results['quick'][] = $quick;

            $summary['quick']++;
            $summary['active']++;
            $summary['records']+= $check['records'];
            $summary['size']+= $check['size'];
        }
    } else {
        $results['disabled'][] = $code;

        $summary['disabled']++;
    }
}

?>