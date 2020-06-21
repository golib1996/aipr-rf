<?php

if (!defined('ABSPATH')) exit;

class sct_worker {
    public $timestart = 0;

    function __construct() { }

    public function cron_run($sct) {
        @ini_set('memory_limit', '255M');
        @set_time_limit(0);

        $log = array();
        $report = array();
        $_tools = 0;
        $_before = null;
        $_after = null;
        $_totals = false;

        $scopes = (array)$sct['job_scope'];
        $real_scope = in_array('site', $scopes) || in_array('reset', $scopes) ? 'site' : 'network';

        do_action('sct_request_cron_cleanup_start', $real_scope, $scopes);

        smart_sct_core()->init_cleanup_methods($scopes);

        if ($real_scope == 'site') {
            $_before = sct_get_database_size();
        } else if ($real_scope == 'network') {
            $_before = sct_get_database_size(true);
        }

        $report['started'] = date('r');
        foreach ($sct['tools'] as $code) {
            $data = smart_sct_core()->cleanup[$code];
            $scope = $data['scope'];
            $_tools++;

            $total = array(
                'label' => '', 'display' => '', 'total' => 0, 'counter' => 0, 'time' => 0,
                'last' => 0, 'last_display' => '', 'last_run' => 0, 'last_time' => 0);

            if ($scope == 'network' || $scope == 'netreset') {
                if (isset(smart_sct_core()->network['statistics'][$scope][$code])) {
                    $total = smart_sct_core()->network['statistics'][$scope][$code];
                }
            } else {
                if (isset(smart_sct_core()->settings['statistics'][$scope][$code])) {
                    $total = smart_sct_core()->settings['statistics'][$scope][$code];
                }
            }

            $class_name = $data['class'];
            $report['#'.$_tools.' '.$code] = $data['label'];

            if (class_exists($class_name)) {
                $clean = new $class_name($scope, $code);

                $args = $sct['args'][$code];

                $this->_timer_start();
                $out = $clean->run($args, $total);
                $out['last_time'] = $this->_timer_stop();
                $out['time'] = $total['time'] + $out['last_time'];

                $out['label'] = $data['label'];

                if ($scope == 'network' || $scope == 'netreset') {
                    smart_sct_core()->network['statistics'][$scope][$code] = $out;
                } else {
                    smart_sct_core()->settings['statistics'][$scope][$code] = $out;
                }

                $report['#'.$_tools.' '.$code.': result'] = $out['last_display'];
                $report['#'.$_tools.' '.$code.': time'] = $out['last_time'].' '.__("seconds", "smart-cleanup-tools");

                $log[$code] = $out['last'];
            } else {
                $report['#'.$_tools.' '.$code.': error'] = __("cleanup tool is missing", "smart-cleanup-tools");
            }
        }

        if ($real_scope == 'site') {
            $_after = sct_get_database_size();
        } else if ($real_scope == 'network') {
            $_after = sct_get_database_size(true);
        }

        $saved_records = 0;
        $saved_space = 0;
        $removed_overhead = 0;

        if (!is_null($_before) && !is_null($_after)) {
            $_totals = true;

            $saved_records = $_before['rows'] - $_after['rows'];
            $saved_space = $_before['size'] - $_after['size'];
            $removed_overhead = $_before['overhead'] - $_after['overhead'];

            if ($removed_overhead < 0) {
                $removed_overhead = 0;
            }

            if ($real_scope == 'site') {
                smart_sct_core()->settings['cron']['counter']++;
                smart_sct_core()->settings['cron']['tools']+= $_tools;
                smart_sct_core()->settings['cron']['rows']+= $saved_records;
                smart_sct_core()->settings['cron']['space']+= $saved_space;
                smart_sct_core()->settings['cron']['overhead']+= $removed_overhead;
            } else if ($real_scope == 'network') {
                smart_sct_core()->network['cron']['counter']++;
                smart_sct_core()->network['cron']['tools']+= $_tools;
                smart_sct_core()->network['cron']['rows']+= $saved_records;
                smart_sct_core()->network['cron']['space']+= $saved_space;
                smart_sct_core()->network['cron']['overhead']+= $removed_overhead;
            }
        }

        $report['total: removed records'] = $saved_records;
        $report['total: overhead removed'] = sct_size_format($removed_overhead);
        $report['total: space recovered'] = sct_size_format($saved_space);
        $report['ended'] = date('r');

        smart_sct_core()->log('cron_report', $real_scope, $report, 'cleanup - '.join(' ', $scopes), 'array_log');

        if ($real_scope == 'network') {
            smart_sct_core()->save_network();
        } else {
            smart_sct_core()->save();
        }

        if (defined('SMART_SECURITY_TOOLS') && function_exists('set_log_event')) {
            $log['scope'] = $scope;
            $log['method'] = 'cron';
            $log['operation'] = 'cleanup';

            set_log_event('cleanup_job_run', array(), $log);
        }

        do_action('sct_request_cron_cleanup_finish', $real_scope, $scope);
    }

    public function ajax_run() {
        check_ajax_referer('smart-cleanup-tools-cleanup');

        @ini_set('memory_limit', '255M');
        @set_time_limit(0);

        $sct = isset($_POST['sct']) ? $_POST['sct'] : array();

        $log = array();
        $report = array();
        $results = '';
        $process = '';
        $_tools = 0;
        $_before = null;
        $_after = null;
        $_totals = false;

        if (empty($sct)) {
            $results = __("Cleanup request was invalid", "smart-cleanup-tools");
        } else {
            $results = __("These are the results of the cleanup process for all selected cleanup tools.", "smart-cleanup-tools");
        }

        $scope = $sct['scope'];
        $real_scope = $scope == 'site' || $scope == 'reset' ? 'site' : 'network';
        $real_purpose = $scope == 'reset' || $scope == 'netreset' ? 'reset' : 'cleanup';

        $quick = isset($sct['quick']) ? $sct['quick'] == 'on' : false;

        do_action('sct_request_ajax_cleanup_start', $real_scope, $real_purpose);

        if (!isset($sct[$scope]) || empty($sct[$scope])) {
            $process.= '<div class="sct-cleanup-box sct-error">';
            $process.= __("No cleanup tools selected.", "smart-cleanup-tools");
            $process.= '</div>';
        } else {
            smart_sct_core()->init_cleanup_methods($scope);

            if ($scope == 'site' || $scope == 'reset') {
                $_before = sct_get_database_size();
            } else if ($scope == 'network' || $scope == 'netreset') {
                $_before = sct_get_database_size(true);
            }

            $report['started'] = date('r');
            foreach ($sct[$scope] as $code => $value) {
                if ($value == 'on') {
                    $_tools++;

                    $total = array(
                        'label' => '', 'display' => '', 'total' => 0, 'counter' => 0, 'time' => 0,
                        'last' => 0, 'last_display' => '', 'last_run' => 0, 'last_time' => 0);

                    if ($scope == 'network' || $scope == 'netreset') {
                        if (isset(smart_sct_core()->network['statistics'][$scope][$code])) {
                            $total = smart_sct_core()->network['statistics'][$scope][$code];
                        }
                    } else {
                        if (isset(smart_sct_core()->settings['statistics'][$scope][$code])) {
                            $total = smart_sct_core()->settings['statistics'][$scope][$code];
                        }
                    }

                    $data = smart_sct_core()->cleanup[$code];

                    $class_name = $data['class'];
                    $report['#'.$_tools.' '.$code] = $data['label'];

                    if (class_exists($class_name)) {
                        $clean = new $class_name($scope, $code);

                        $args = isset($_POST['sct_args'][$scope][$code]) ? $_POST['sct_args'][$scope][$code] : array();

                        $this->_timer_start();
                        $out = $clean->run($args, $total);
                        $out['last_time'] = $this->_timer_stop();
                        $out['time'] = $total['time'] + $out['last_time'];

                        $process.= '<div class="sct-cleanup-box sct-processed">';
                        $process.= '<h3>'.$data['label'].'</h3>';
                        $process.= $out['last_display'].'<br/>';
                        $process.= __("Execution time", "smart-cleanup-tools").': <strong>'.$out['last_time'].' '.__("seconds", "smart-cleanup-tools").'</strong>';
                        $process.= '</div>';

                        $out['label'] = $data['label'];

                        if ($scope == 'network' || $scope == 'netreset') {
                            smart_sct_core()->network['statistics'][$scope][$code] = $out;
                        } else {
                            smart_sct_core()->settings['statistics'][$scope][$code] = $out;
                        }

                        $report['#'.$_tools.' '.$code.': result'] = $out['last_display'];
                        $report['#'.$_tools.' '.$code.': time'] = $out['last_time'].' '.__("seconds", "smart-cleanup-tools");

                        $log[$code] = $out['last'];
                    } else {
                        $report['#'.$_tools.' '.$code.': error'] = __("cleanup tool is missing", "smart-cleanup-tools");
                    }
                }
            }

            if ($scope == 'site' || $scope == 'reset') {
                $_after = sct_get_database_size();
            } else if ($scope == 'network' || $scope == 'netreset') {
                $_after = sct_get_database_size(true);
            }
        }

        $saved_records = 0;
        $saved_space = 0;
        $removed_overhead = 0;

        if (!is_null($_before) && !is_null($_after)) {
            $_totals = true;

            $saved_records = $_before['rows'] - $_after['rows'];
            $saved_space = $_before['size'] - $_after['size'];
            $removed_overhead = $_before['overhead'] - $_after['overhead'];

            if ($removed_overhead < 0) {
                $removed_overhead = 0;
            }

            if ($scope == 'site' || $scope == 'reset') {
                smart_sct_core()->settings['global']['counter']++;
                smart_sct_core()->settings['global']['tools']+= $_tools;
                smart_sct_core()->settings['global']['rows']+= $saved_records;
                smart_sct_core()->settings['global']['space']+= $saved_space;
                smart_sct_core()->settings['global']['overhead']+= $removed_overhead;
            } else if ($scope == 'network' || $scope == 'netreset') {
                smart_sct_core()->network['global']['counter']++;
                smart_sct_core()->network['global']['tools']+= $_tools;
                smart_sct_core()->network['global']['rows']+= $saved_records;
                smart_sct_core()->network['global']['space']+= $saved_space;
                smart_sct_core()->network['global']['overhead']+= $removed_overhead;
            }
        }

        if ($quick) {
            $render = '<div class="sct-front-quick-out"><div>'.__("Quick", "smart-cleanup-tools").'</div><div>'.__("Cleanup", "smart-cleanup-tools").'</div><div>'.__("Results", "smart-cleanup-tools").'</div></div>';

            if ($_totals) {
                $render.= '<div class="sct-front-quick-results">'.__("Records", "smart-cleanup-tools").': '.$saved_records.'</div>';
                $render.= '<div class="sct-front-quick-results">'.__("Overhead", "smart-cleanup-tools").': '.str_replace(' ', '', sct_size_format($removed_overhead)).'</div>';
                $render.= '<div class="sct-front-quick-results">'.__("Saved", "smart-cleanup-tools").': '.str_replace(' ', '', sct_size_format($saved_space)).'</div>';
            }
        } else {
            $render = '<div class="sct-cleanup-left"><p>'.$results.'</p>';

            if ($_totals) {
                $render.= '<div class="sct-cleanup-totals">';

                if ($scope == 'reset') {
                    $render.= '<h3>'.__("Reset Totals", "smart-cleanup-tools").'</h3>';
                } else {
                    $render.= '<h3>'.__("Cleanup Totals", "smart-cleanup-tools").'</h3>';
                }

                $render.= __("Records removed", "smart-cleanup-tools").': <strong>'.$saved_records.'</strong><br/>';
                $render.= __("Overhead removed", "smart-cleanup-tools").': <strong>'.sct_size_format($removed_overhead).'</strong><br/>';
                $render.= __("Space recovered", "smart-cleanup-tools").': <strong>'.sct_size_format($saved_space).'</strong><br/>';
                $render.= '</div>';

                $render.= '<div style="clear:both;"></div><p class="sct-left-info sct-top">';
                $render.= __("Summary results presented here are just an estimation. Database could be modified by some other process in the same time, and that can affect calculation of exact values", "smart-cleanup-tools");
                $render.= '</p>';
            }

            $render.= '</div>';
            $render.= '<div class="sct-cleanup-right" style="padding: 15px 0 15px 5px">';
            $render.= $process;
            $render.= '</div>';
        }

        $report['total: removed records'] = $saved_records;
        $report['total: overhead removed'] = sct_size_format($removed_overhead);
        $report['total: space recovered'] = sct_size_format($saved_space);
        $report['ended'] = date('r');

        smart_sct_core()->log('run_report', $real_scope, $report, 'cleanup - '.$scope, 'array_log');

        if ($scope == 'network' || $scope == 'netreset') {
            smart_sct_core()->save_network();
        } else {
            smart_sct_core()->save();
        }

        do_action('sct_request_ajax_cleanup_finish', $real_scope, $real_purpose);

        if (defined('SMART_SECURITY_TOOLS') && function_exists('set_log_event')) {
            $log['scope'] = $scope;
            $log['method'] = 'direct';
            $log['operation'] = 'cleanup';

            set_log_event('cleanup_job_run', array(), $log);
        }

        die($render);
    }

    public function ajax_preview() {
        check_ajax_referer('smart-cleanup-tools-removal');

        @ini_set('memory_limit', '255M');
        @set_time_limit(0);

        $data = $_POST['remove'];
        $filter = $data[$data['type']];
        $class = 'sct_removal_'.$data['type'];

        require_once(SCT_PATH.'remove/'.$data['type'].'/load.php');
        $obj = new $class();

        die($obj->check($filter));
    }

    public function ajax_removal() {
        check_ajax_referer('smart-cleanup-tools-removal');

        @ini_set('memory_limit', '255M');
        @set_time_limit(0);

        $data = $_POST['remove'];
        $filter = $data['args'];
        $class = 'sct_removal_'.$data['type'];

        require_once(SCT_PATH.'remove/'.$data['type'].'/load.php');
        $obj = new $class();
        $out = $obj->remove($filter);

        if (defined('SMART_SECURITY_TOOLS') && function_exists('set_log_event')) {
            $args = $data['args'];
            $args['type'] = $data['type'];
            $args['operation'] = 'removal';

            set_log_event('cleanup_job_run', array(), $args);
        }

        die($out);
    }

    public function run_tool($scope, $code, $args = array()) {
        smart_sct_core()->init_cleanup_methods($scope);

        $total = array(
            'label' => '', 'display' => '', 'total' => 0, 'counter' => 0, 'time' => 0,
            'last' => 0, 'last_display' => '', 'last_run' => 0, 'last_time' => 0);

        $real_scope = '';
        $scope = (array)$scope;

        if (in_array('network', $scope) || in_array('netreset', $scope)) {
            if (isset(smart_sct_core()->network['statistics'][$scope[0]][$code])) {
                $total = smart_sct_core()->network['statistics'][$scope[0]][$code];

                $real_scope = $scope[0];
            } else if (isset($scope[1]) && isset(smart_sct_core()->network['statistics'][$scope[1]][$code])) {
                $total = smart_sct_core()->network['statistics'][$scope[1]][$code];

                $real_scope = $scope[1];
            }
        } else {
            if (isset(smart_sct_core()->settings['statistics'][$scope[0]][$code])) {
                $total = smart_sct_core()->settings['statistics'][$scope[0]][$code];

                $real_scope = $scope[0];
            } else if (isset($scope[1]) && isset(smart_sct_core()->settings['statistics'][$scope[1]][$code])) {
                $total = smart_sct_core()->settings['statistics'][$scope[1]][$code];

                $real_scope = $scope[0];
            }
        }

        $data = smart_sct_core()->cleanup[$code];
        $class_name = $data['class'];
        $clean = new $class_name($real_scope, $code);

        $this->_timer_start();
        $out = $clean->run($args, $total);
        $out['last_time'] = $this->_timer_stop();
        $out['time'] = $total['time'] + $out['last_time'];

        $out['label'] = $data['label'];

        if ($real_scope == 'network' || $real_scope == 'netreset') {
            smart_sct_core()->network['statistics'][$real_scope][$code] = $out;
            smart_sct_core()->save_network();
        } else {
            smart_sct_core()->settings['statistics'][$real_scope][$code] = $out;
            smart_sct_core()->save();
        }

        return $out;
    }

    private function _timer_start() {
	$mtime = explode(' ', microtime());
	$this->timestart = $mtime[1] + $mtime[0];

	return true;
    }

    private function _timer_stop($precision = 6) {
	$mtime = explode(' ', microtime());

	$timeend = $mtime[1] + $mtime[0];
	$timetotal = $timeend - $this->timestart;

	return number_format($timetotal, $precision);
    }
}

?>