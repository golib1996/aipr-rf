<?php

if (!defined('ABSPATH')) exit;

final class sct_defaults {
    public $settings = array(
        '__version__' => '4.5',
        '__date__' => '2016.07.13.',
        '__build__' => 3988,
        '__status__' => 'stable',
        '__product_id__' => 'smart-cleanup-tools',
        'toolbar_menu_active' => true,
        'log_enabled' => false,
        'log_run_report' => true,
        'log_cron_report' => true,
        'log_sql_run' => false,
        'log_sql_check' => false,
        'cleanup_summary' => true,
        'cleanup_show_inactive' => true,
        'disabled' => array(
            'site' => array(),
            'reset' => array(),
            'removal' => array()
        ),
        'cron' => array(
            'counter' => 0,
            'tools' => 0,
            'rows' => 0,
            'space' => 0,
            'overhead' => 0
        ),
        'global' => array(
            'counter' => 0,
            'tools' => 0,
            'rows' => 0,
            'space' => 0,
            'overhead' => 0
        ),
        'statistics' => array(
            'site' => array(),
            'reset' => array()
        )
    );

    public $network = array(
        '__version__' => '4.5',
        '__date__' => '2016.07.13.',
        '__build__' => 3988,
        '__status__' => 'stable',
        '__product_id__' => 'smart-cleanup-tools',
        'toolbar_menu_active' => true,
        'log_enabled' => false,
        'log_run_report' => true,
        'log_cron_report' => true,
        'log_sql_run' => false,
        'log_sql_check' => false,
        'cleanup_summary' => true,
        'cleanup_show_inactive' => true,
        'disabled' => array(
            'network' => array(),
            'netreset' => array(),
            'removal' => array()
        ),
        'cron' => array(
            'counter' => 0,
            'tools' => 0,
            'rows' => 0,
            'space' => 0,
            'overhead' => 0
        ),
        'global' => array(
            'counter' => 0,
            'tools' => 0,
            'rows' => 0,
            'space' => 0,
            'overhead' => 0
        ),
        'statistics' => array(
            'network' => array(),
            'netreset' => array()
        )
    );

    function __construct() { }

    public function upgrade($old, $scope = 'site') {
        $_settings = $scope == 'site' ? $this->settings : $this->network;

        foreach ($_settings as $key => $value) {
            if (!isset($old[$key])) {
                $old[$key] = $value;
            }
        }

        $unset = array();
        foreach ($old as $key => $value) {
            if (!isset($_settings[$key])) {
                $unset[] = $key;
            }
        }

        if (!empty($unset)) {
            foreach ($unset as $key) {
                unset($old[$key]);
            }
        }

        foreach ($_settings as $key => $value) {
            if (substr($key, 0, 2) == '__') {
                $old[$key] = $value;
            }
        }

        return $old;
    }
}

?>