<?php

$task = $scope == 'site' ? 'stc_runcleanup_site' : 'stc_runcleanup_network';
$url = 'admin.php?page=smart-cleanup-tools-scheduler&task=';

$all_jobs = array();
$schedules = wp_get_schedules();
$time_slots = _get_cron_array();

foreach ($time_slots as $key => $jobs) {
    foreach ($jobs as $job => $values) {
        foreach ($values as $id => $schedule) {
            if ($job == $task) {
                $time = sct_server_to_local_timestamp($key);

                $cron = array(
                    'id' => $id,
                    'key' => $key,
                    'job' => $task,
                    'run_once' => false,
                    'real_time' => $time,
                    'args' => $schedule['args'][0]
                );

                if (isset($schedule['schedule']) && $schedule['schedule'] !== false) {
                    $cron['schedule'] = $schedule['schedule'];
                    $cron['interval'] = $schedule['interval'];
                } else {
                    $cron['run_once'] = true;
                }

                $all_jobs[] = $cron;
            }
        }
    }
}

?>
<div class="sct-cleanup-left">
    <p>
        <?php _e("You can schedule cleanup jobs to run automatically. On the right you have a list of all scheduled jobs.", "smart-cleanup-tools"); ?><br/>
    </p>
    <input onclick="window.location='admin.php?page=smart-cleanup-tools-scheduler&job=new';" class="button-primary" type="button" value="<?php _e("Create New Job", "smart-cleanup-tools"); ?>" />
</div>
<div class="sct-cleanup-right sct-normal">
    <?php if (empty($all_jobs)) { ?>
        <h2 class="sct-cleanup-stats-title"><?php _e("There are no cleanup jobs scheduduled yet.", "smart-cleanup-tools") ?></h2>
    <?php } else { ?>
    <table class="widefat sct-table-grid">
        <thead>
            <tr>
                <th style="width: 35%"><?php _e("Job Details", "smart-cleanup-tools"); ?></th>
                <th style="width: 15%"><?php _e("Next Run", "smart-cleanup-tools"); ?></th>
                <th><?php _e("Tools Included", "smart-cleanup-tools"); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php

            $tr_class = '';

            foreach ($all_jobs as $job) {
                echo '<tr class="'.$tr_class.'">';
                    echo '<td>';
                    echo '<h4>'.$job['args']['job_title'];
                    echo '<span class="sct-job-operations"><a href="'.$url.'edit&job='.$job['key'].'-'.$job['id'].'">'.__("edit", "smart-cleanup-tools").'</a> | ';
                    echo '<a onclick="return sct_admin.confirm()" href="'.$url.'delete&job='.$job['key'].'-'.$job['id'].'">'.__("delete", "smart-cleanup-tools").'</a></span>';
                    echo '</h4>';
                    if ($job['run_once']) {
                        echo __("To run only once.", "smart-cleanup-tools");
                    } else {
                        echo __("Run", "smart-cleanup-tools").': '.$schedules[$job['schedule']]['display'];
                    }
                    echo '</td>';
                    echo '<td>';
                        echo date(get_option('date_format'), $job['real_time']).'<br/>';
                        echo __("at", "smart-cleanup-tools").' '.date(get_option('time_format'), $job['real_time']);
                    echo '</td>';
                    echo '<td>';
                        echo join(', ', $job['args']['labels']);
                    echo '</td>';
                echo '</tr>';

                $tr_class = $tr_class == '' ? 'alternate ' : $tr_class = '';
            }

        ?>
        </tbody>
    </table>
    <?php } ?>
</div>
