<form method="POST" id="sct-cleanup-form">
    <?php settings_fields('smart-cleanup-tools-job'); ?>
    <input type="hidden" name="job_scope[]" value="<?php echo $to_load[0]; ?>" />
    <input type="hidden" name="job_scope[]" value="<?php echo $to_load[1]; ?>" />
    <input type="hidden" name="job_code" value="<?php echo $job; ?>" />

    <div class="sct-cleanup-left">
        <p>
            <?php _e("Set up the job schedule parameters and tools to use.", "smart-cleanup-tools"); ?><br/>
        </p>
        <p>
            <label><?php _e("Job Title", "smart-cleanup-tools"); ?>:</label>
            <input type="text" class="widefat" name="job_title" value="<?php echo $cron['job_title']; ?>" />
            <label><?php _e("Schedule", "smart-cleanup-tools"); ?>:</label>
            <select class="widefat" name="job_method" id="job_method">
                <?php

                $periods = wp_get_schedules();
                $schedule = array('once' => __("Run Once", "smart-cleanup-tools"));

                foreach ($periods as $key => $value) {
                    $schedule['run__'.$key] = __("Run", "smart-cleanup-tools").': '.$value['display'];
                }

                foreach ($schedule as $key => $title) {
                    $selected = $cron['job_method'] == $key ? ' selected="selected"' : '';
                    echo '<option value="'.$key.'"'.$selected.'>'.$title.'</option>';
                }

                ?>
            </select>
            <label><?php _e("First Run", "smart-cleanup-tools"); ?>:</label>
            <input type="text" class="widefat" name="first_run" id="job_first_run" value="<?php echo $cron['first_run']; ?>" />
        </p>
        <input class="button-primary" type="submit" value="<?php _e("Save Job", "smart-cleanup-tools"); ?>" />
    </div>
    <div class="sct-cleanup-right" style="padding: 15px 0 15px 5px">
        <?php

        $disabled = apply_filters('sct_disable_cleanup_tools_schedule_'.$to_load[0], $settings['disabled'][$to_load[0]]);
        $disabled = array_merge($disabled, apply_filters('sct_disable_cleanup_tools_schedule_'.$to_load[1], $settings['disabled'][$to_load[1]]));

        foreach (smart_sct_core()->cleanup as $code => $data) {
            if (!in_array($code, $disabled) && $data['cron']) {
                $class_name = $data['class'];
                $clean = new $class_name($data['scope'], $code);
                $help = $clean->help();

                $args = isset($cron['args'][$code]) ? $cron['args'][$code] : array();

                $render = '<div class="sct-cleanup-box sct-cleanup-job-box'.(in_array($code, $cron['tools']) ? ' sct-enabled' : '').'">';
                    $render.= '<h3>'.$data['label'].'</h3>';
                    $render.= '<div class="sct-cleanup-infobox">';
                        $render.= $clean->form($args);
                    $render.= '</div>';
                    $render.= '<div class="sct-cleanup-checkbox">';
                        $render.= $clean->draw_checkbox(in_array($code, $cron['tools']), __("Include this cleanup tool", "smart-cleanup-tools"), $data['label']);

                        if ($help !== false) {
                            $render.= '<a class="sct-tool-help ui-state-active ui-corner-all" title="'.__("More information available for this tweak.", "smart-cleanup-tools").'" href="#'.$code.'"><span class="ui-icon ui-icon-info"></span></a>';
                        }
                    $render.= '</div>';

                    if ($help !== false) {
                        $render.= '<div title="'.__("Help", "smart-cleanup-tools").': '.$data['label'].'" id="sct-help-'.$code.'" style="display: none;">';
                        $render.= $help;
                        $render.= '</div>';
                    }
                $render.= '</div>';

                echo $render;
            }
        }
        
        ?>
    </div>
</form>
<script type="text/javascript">
    jQuery(document).ready(function() {
        sct_admin.job();
    });
</script>