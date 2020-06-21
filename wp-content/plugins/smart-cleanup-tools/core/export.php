<?php

$settings = array();
$validxpr = array('jobs', 'settings');

define('SMART_PLUGINS_WPLOAD', '');

function smart_server_to_local_timestamp($server) {
    $local = $server + get_option('gmt_offset') * 3600;
    return $local;
}

function smart_export_settings($settings, $export, $scope = 'site') {
    $data = new stdClass();

    if (in_array('settings', $export)) {
        foreach ($settings as $option) {
            $raw = $scope == 'site' ? get_option($option) : get_site_option($option);

            unset($raw['cron']);
            unset($raw['global']);
            unset($raw['statistics']);

            $data->$option = $raw;
        }
    }

    if (in_array('jobs', $export)) {
        $task = !is_network_admin() ? 'stc_runcleanup_site' : 'stc_runcleanup_network';

        $time_slots = _get_cron_array();

        $to_export = array();

        foreach ($time_slots as $key => $jobs) {
            foreach ($jobs as $job => $values) {
                foreach ($values as $id => $schedule) {
                    if ($job == $task) {
                        $time = smart_server_to_local_timestamp($key);

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

                        $to_export[] = $cron;
                    }
                }
            }
        }

        if (!empty($to_export)) {
            $key = 'smart-scheduled-jobs-'.$scope;
            $data->$key = $to_export;
        }
    }

    return serialize($data);
}

function smart_is_current_user_role($role = 'administrator') {
    global $current_user;

    if (is_array($current_user->roles)) {
        return in_array($role, $current_user->roles);
    } else {
        return false;
    }
}

function smart_get_wpload_path() {
    if (SMART_PLUGINS_WPLOAD == '') {
        $d = 0;

        while (!file_exists(str_repeat('../', $d).'wp-load.php'))
            if (++$d > 16) exit;
        return str_repeat('../', $d).'wp-load.php';
    } else {
        return SMART_PLUGINS_WPLOAD;
    }
}

$wpload = smart_get_wpload_path();
require($wpload);

@ini_set('memory_limit', '255M');
@set_time_limit(360);

check_ajax_referer('sct-settings-export');

if (!smart_is_current_user_role()) {
    wp_die(__("Only administrators can use export features.", "smart-cleanup-tools"));
}

$export_code = 'site';
$export_date = date('Y-m-d');

if (isset($_GET['_xport_scope']) && $_GET['_xport_scope'] == 'network') {
    $export_code = 'network';
    $settings = array('smart-cleanup-tools-network');
} else {
    $settings = array('smart-cleanup-tools');
}

$export = array_values(array_intersect($validxpr, explode(',', $_GET['export'])));

if (empty($export)) {
    wp_die(__("Nothing is selected for export.", "smart-cleanup-tools"));
}

$export_name = 'smart_cleanup_tools_'.$export_code.'_'.join('-', $export).'_'.$export_date;

header('Content-type: application/force-download');
header('Content-Disposition: attachment; filename="'.$export_name.'.sct"');

echo smart_export_settings($settings, $export, $export_code);

?>