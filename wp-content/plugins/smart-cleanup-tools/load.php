<?php

if (!defined('ABSPATH')) exit;

class sct_loader {
    public $log_files = array(
        'run_report' => 'sct_run_report.log',
        'cron_report' => 'sct_cron_report.log',
        'sql_run' => 'sct_sql_run.log',
        'sql_check' => 'sct_sql_check.log'
    );

    public $page_ids;

    public $timestart = 0;

    public $logs = array();
    public $cleanup = array();
    public $remove = array();
    public $settings = array();
    public $network = array();

    function __construct() {
        global $wp_version;

        define('SCT_WP_VERSION', intval(substr(str_replace('.', '', $wp_version), 0, 2)));

        $_dirname = trailingslashit(dirname(__FILE__));
        $_urlname = plugin_dir_url(__FILE__);

        define('SCT_PATH', $_dirname);
        define('SCT_URL', $_urlname);
        define('SCT_EOL', "\r\n");

        require_once(SCT_PATH.'clean/basic.php');
        require_once(SCT_PATH.'remove/basic.php');

        require_once(SCT_PATH.'core/defaults.php');
        require_once(SCT_PATH.'core/functions.php');
        require_once(SCT_PATH.'core/log.php');

        require_once(SCT_PATH.'core/admin.php');

        if (is_network_admin()) {
            require_once(SCT_PATH.'core/network.php');
        }

        add_action('plugins_loaded', array(&$this, 'init_plugin_settings'));
        add_action('plugins_loaded', array(&$this, 'init_translation'));

        add_action('stc_runcleanup_site', 'stc_cron_runcleanup');
        add_action('stc_runcleanup_network', 'stc_cron_runcleanup');

        add_filter('cron_schedules', array(&$this, 'add_cron_schedules'));
        add_action('ssect_register_new_log_events', array(&$this, 'security_events'));

        add_action('wp_ajax_smart_cleanup_run', array(&$this, 'ajax_run'));
        add_action('wp_ajax_smart_cleanup_quick', array(&$this, 'ajax_run'));
        add_action('wp_ajax_smart_removal_preview', array(&$this, 'ajax_preview'));
        add_action('wp_ajax_smart_removal_action', array(&$this, 'ajax_removal'));

        add_action('sct_init_plugin_settings', array(&$this, 'init_toolbar_menu'));
    }

    public function security_events() {
        set_register_log_event('cleanup_job_run', __("Cleanup Job Run", "smart-cleanup-tools"), 'Smart Cleanup Tools', false, 'both');
    }

    public function get_tools_site() {
        require_once(SCT_PATH.'clean/parent.php');
        require_once(SCT_PATH.'clean/site.php');

        $tools = array(
            'posts_trash' => array('scope' => 'site', 'cron' => true, 'label' => __("Trashed Posts Records", "smart-cleanup-tools"), 'class' => 'sct_cleanup_site_posts_trash'),
            'posts_spam' => array('scope' => 'site', 'cron' => true, 'label' => __("Spammed Posts Records", "smart-cleanup-tools"), 'class' => 'sct_cleanup_site_posts_spam'),
            'posts_auto_draft' => array('scope' => 'site', 'cron' => true, 'label' => __("Auto Draft Posts Records", "smart-cleanup-tools"), 'class' => 'sct_cleanup_site_posts_auto_draft'),
            'posts_revisions' => array('scope' => 'site', 'cron' => true, 'label' => __("Published Posts Revisions Records", "smart-cleanup-tools"), 'class' => 'sct_cleanup_site_posts_revisions'),
            'orphaned_revisions' => array('scope' => 'site', 'cron' => true, 'label' => __("Orphaned Posts Revisions Records", "smart-cleanup-tools"), 'class' => 'sct_cleanup_site_orphaned_revisions'),
            'orphaned_relationships' => array('scope' => 'site', 'cron' => true, 'label' => __("Orphaned Relationships Records", "smart-cleanup-tools"), 'class' => 'sct_cleanup_site_orphaned_relationships'),
            'orphaned_terms' => array('scope' => 'site', 'cron' => true, 'label' => __("Orphaned Terms Records", "smart-cleanup-tools"), 'class' => 'sct_cleanup_site_orphaned_terms'),
            'comments_trash' => array('scope' => 'site', 'cron' => true, 'label' => __("Trashed Comments Records", "smart-cleanup-tools"), 'class' => 'sct_cleanup_site_comments_trash'),
            'comments_spam' => array('scope' => 'site', 'cron' => true, 'label' => __("Spammed Comments Records", "smart-cleanup-tools"), 'class' => 'sct_cleanup_site_comments_spam'),
            'comments_unapproved' => array('scope' => 'site', 'cron' => true, 'label' => __("Unapproved Comments Records", "smart-cleanup-tools"), 'class' => 'sct_cleanup_site_comments_unapproved'),
            'postmeta_orphans' => array('scope' => 'site', 'cron' => true, 'label' => __("Orphaned Postmeta Records", "smart-cleanup-tools"), 'class' => 'sct_cleanup_site_postmeta_orphans'),
            'postmeta_oembeds' => array('scope' => 'site', 'cron' => true, 'label' => __("oEmbed Postmeta Records", "smart-cleanup-tools"), 'class' => 'sct_cleanup_site_postmeta_oembeds'),
            'commentmeta_orphans' => array('scope' => 'site', 'cron' => true, 'label' => __("Orphaned Commentmeta Records", "smart-cleanup-tools"), 'class' => 'sct_cleanup_site_commentmeta_orphans'),
            'comments_orphans' => array('scope' => 'site', 'cron' => true, 'label' => __("Orphaned Comments Records", "smart-cleanup-tools"), 'class' => 'sct_cleanup_site_comments_orphans'),
            'expired_transient' => array('scope' => 'site', 'cron' => true, 'label' => __("Expired Transient Records", "smart-cleanup-tools"), 'class' => 'sct_cleanup_site_expired_transient'),
            'transient' => array('scope' => 'site', 'cron' => true, 'label' => __("Transient Records", "smart-cleanup-tools"), 'class' => 'sct_cleanup_site_transient'),
            'rss_cache' => array('scope' => 'site', 'cron' => true, 'label' => __("RSS Feeds Cache", "smart-cleanup-tools"), 'class' => 'sct_cleanup_site_rss_cache'),
            'woocommerce_options_sessions' => array('scope' => 'site', 'cron' => true, 'label' => __("WooCommerce Transient Sessions", "smart-cleanup-tools"), 'class' => 'sct_cleanup_site_woocommerce_options_sessions')
        );

        if (!is_multisite()) {
            $tools['usermeta_empty'] = array('scope' => 'site', 'cron' => true, 'label' => __("Empty Usermeta Records", "smart-cleanup-tools"), 'class' => 'sct_cleanup_site_usermeta_empty');
        }

        if (sct_is_plugin_active('gravityforms/gravityforms.php') && function_exists('gravity_form')) {
            $tools['gravityforms_leads_trash'] = array('scope' => 'site', 'cron' => true, 'label' => __("Trashed GravityForms Records", "smart-cleanup-tools"), 'class' => 'sct_cleanup_site_gravityforms_leads_trash');
            $tools['gravityforms_leads_spam'] = array('scope' => 'site', 'cron' => true, 'label' => __("Spammed GravityForms Records", "smart-cleanup-tools"), 'class' => 'sct_cleanup_site_gravityforms_leads_spam');
        }

        $tools['db_overhead'] = array('scope' => 'site', 'cron' => true, 'label' => __("Database Overhead", "smart-cleanup-tools"), 'class' => 'sct_cleanup_site_db_overhead');

        return $tools;
    }

    public function get_tools_reset() {
        require_once(SCT_PATH.'clean/reset.php');

        return array(
            'reset_widgets' => array('scope' => 'reset', 'cron' => false, 'label' => __("Reset all sidebars", "smart-cleanup-tools"), 'class' => 'sct_cleanup_reset_sidebars'),
            'post_locks' => array('scope' => 'reset', 'cron' => true, 'label' => __("Remove post edit locks", "smart-cleanup-tools"), 'class' => 'sct_cleanup_reset_post_locks'),
            'rewrite_rules' => array('scope' => 'reset', 'cron' => true, 'label' => __("Refresh rewrite rules", "smart-cleanup-tools"), 'class' => 'sct_cleanup_reset_rewrite_rules'),
            'sct_plugin' => array('scope' => 'reset', 'cron' => false, 'label' => __("Reset this plugin data", "smart-cleanup-tools"), 'class' => 'sct_cleanup_reset_sct_plugin')
        );
    }

    public function get_tools_network() {
        require_once(SCT_PATH.'clean/network.php');

        return array(
            'transient' => array('scope' => 'network', 'cron' => true, 'label' => __("Transient Records", "smart-cleanup-tools"), 'class' => 'sct_cleanup_network_transient'),
            'expired_transient' => array('scope' => 'network', 'cron' => true, 'label' => __("Expired Transient Records", "smart-cleanup-tools"), 'class' => 'sct_cleanup_network_expired_transient'),
            'usermeta_empty' => array('scope' => 'network', 'cron' => true, 'label' => __("Empty Usermeta Records", "smart-cleanup-tools"), 'class' => 'sct_cleanup_network_usermeta_empty'),
            'db_overhead' => array('scope' => 'network', 'cron' => true, 'label' => __("Database Overhead", "smart-cleanup-tools"), 'class' => 'sct_cleanup_network_db_overhead')
        );
    }

    public function get_tools_netreset() {
        require_once(SCT_PATH.'clean/netreset.php');

        return array(
            'sct_plugin' => array('scope' => 'netreset', 'cron' => false, 'label' => __("Reset this plugin data", "smart-cleanup-tools"), 'class' => 'sct_cleanup_netreset_sct_plugin')
        );
    }

    public function get_removal_site() {
        return array(
            'comments' => array('scope' => 'site', 'cron' => false, 'label' => __("Comments", "smart-cleanup-tools"), 'class' => 'sct_removal_comments'),
            'attachments' => array('scope' => 'site', 'cron' => false, 'label' => __("Attachments", "smart-cleanup-tools"), 'class' => 'sct_removal_attachments'),
            'postmeta' => array('scope' => 'site', 'cron' => false, 'label' => __("Postmeta", "smart-cleanup-tools"), 'class' => 'sct_removal_postmeta_empty'),
            'posts' => array('scope' => 'site', 'cron' => false, 'label' => __("Posts", "smart-cleanup-tools"), 'class' => 'sct_removal_posts'),
            'terms' => array('scope' => 'site', 'cron' => false, 'label' => __("Terms", "smart-cleanup-tools"), 'class' => 'sct_removal_terms'),
            'posts_missing_cpt' => array('scope' => 'site', 'cron' => false, 'label' => __("Missing Post Types Posts", "smart-cleanup-tools"), 'class' => 'sct_removal_posts_missing_cpt'),
            'terms_missing_tax' => array('scope' => 'site', 'cron' => false, 'label' => __("Missing Taxonomies Terms", "smart-cleanup-tools"), 'class' => 'sct_removal_terms_missing_tax')
        );
    }

    public function get_removal_network() {
        return array();
    }

    public function init_toolbar_menu() {
        $load = false;

        if (is_network_admin()) {
            $load = $this->network['toolbar_menu_active'];
        } else {
            $load = $this->settings['toolbar_menu_active'];
        }

        if ($load) {
            require_once(SCT_PATH.'core/toolbar.php');
        }
    }
    
    public function init_translation() {
        $this->l = get_locale();

        if(!empty($this->l)) {
            load_plugin_textdomain('smart-cleanup-tools', false, 'smart-cleanup-tools/languages');
        }
    }

    public function init_removal_methods($scope = 'site') {
        $scope = (array)$scope;

        $this->remove = array();
        if (in_array('site', $scope)) {
            $this->remove+= $this->get_removal_site();
        }

        if (in_array('network', $scope)) {
            $this->remove+= $this->get_removal_network();
        }
    }

    public function init_cleanup_methods($scope = 'site') {
        $scope = (array)$scope;

        $this->cleanup = array();
        if (in_array('site', $scope)) {
            $this->cleanup+= $this->get_tools_site();
        }

        if (in_array('reset', $scope)) {
            $this->cleanup+= $this->get_tools_reset();
        }

        if (in_array('network', $scope)) {
            $this->cleanup+= $this->get_tools_network();
        }

        if (in_array('netreset', $scope)) {
            $this->cleanup+= $this->get_tools_netreset();
        }
    }

    public function init_plugin_settings() {
        $_d = new sct_defaults();

        $this->settings = get_option('smart-cleanup-tools');
        $this->network = get_site_option('smart-cleanup-tools-network');

        if (!is_array($this->settings)) {
            $this->settings = $_d->settings;
            update_option('smart-cleanup-tools', $this->settings);
        } else if ($this->settings['__build__'] != $_d->settings['__build__']) {
            $this->settings = $_d->upgrade($this->settings);
            update_option('smart-cleanup-tools', $this->settings);
        }

        if (!is_array($this->network)) {
            $this->network = $_d->network;
            update_site_option('smart-cleanup-tools-network', $this->network);
        } else if ($this->network['__build__'] != $_d->network['__build__']) {
            $this->network = $_d->upgrade($this->network, 'network');
            update_site_option('smart-cleanup-tools-network', $this->network);
        }

        define('SMART_CLEANUP_TOOLS', $this->settings['__version__']);

        do_action('sct_init_plugin_settings');
    }

    public function get($name) {
        return $this->settings[$name];
    }

    public function set($name, $value) {
        $this->settings[$name] = $value;
    }

    public function save() {
        update_option('smart-cleanup-tools', $this->settings);
    }

    public function save_network() {
        update_site_option('smart-cleanup-tools-network', $this->network);
    }

    public function ajax_run() {
        require_once(SCT_PATH.'core/worker.php');

        $worker = new sct_worker();
        $worker->ajax_run();
    }

    public function ajax_preview() {
        require_once(SCT_PATH.'core/worker.php');

        $worker = new sct_worker();
        $worker->ajax_preview();
    }

    public function ajax_removal() {
        require_once(SCT_PATH.'core/worker.php');

        $worker = new sct_worker();
        $worker->ajax_removal();
    }

    public function import_scheduled_jobs($jobs) {
        foreach ($jobs as $job) {
            $cron = $job['args'];
            $time = $job['real_time'];
            $task = $job['job'];
            $intr = $job['interval'];
            $schd = $job['schedule'];

            if ($job['run_once']) {
                wp_schedule_single_event(time() + 3600, $task, array($cron));
            } else {
                while ($time < time()) {
                    $time+= $intr;
                }

                wp_schedule_event($cron['first_run'], $schd, $task, array($cron));
            }
        }
    }

    public function handle_menu_cron($scope = 'site') {
        $task_name = $scope == 'site' ? 'stc_runcleanup_site' : 'stc_runcleanup_network';

        $return = array(
            'cron' => array(
                        'job_scope' => $scope,
                        'job_title' => __("New Job", "smart-cleanup-tools"),
                        'job_method' => 'run__sct_weekly',
                        'first_run' => '',
                        'labels' => array(),
                        'tools' => array(),
                        'args' => array()
                    ),
            'job' => ''
        );

        $job = isset($_GET['job']) ? $_GET['job'] : '';

        if ($job != 'new' && $job != '') {
            $task = isset($_GET['task']) ? $_GET['task'] : 'edit';
            $parts = explode('-', $job);

            if ($task == 'edit') {
                $cron = sct_get_scheduled_job($task_name, $parts[0], $parts[1]);
                $time = sct_server_to_local_timestamp($parts[0]);

                $return['cron'] = $cron['args'][0];
                $return['cron']['first_run'] = date('m/d/Y H:i', $time);
            } else if ($task == 'delete') {
                sct_unschedule_job($task_name, $parts[0], $parts[1]);

                $job = '';
            }

            $return['job'] = $job;
        } else if ($job == 'new') {
            $return['job'] = 'new';
        }
        
        return $return;
    }

    public function add_cron_schedules($list) {
        $list['sct_weekly'] = array('interval' => 7 * 24 * 3600, 'display' => __("Once Weekly", "smart-cleanup-tools"));
        $list['sct_monthly'] = array('interval' => 30 * 24 * 3600, 'display' => __("Once Monthly", "smart-cleanup-tools"));

        return $list;
    }

    public function log_truncate($name, $scope) {
        if (!isset($this->logs[$name])) {
            $path = $this->_get_log_file($name, $scope);
            $this->logs[$name] = new sct_log($path);
            $this->logs[$name]->truncate();
        }
    }

    public function log($name, $scope, $data, $title = '', $method = 'dump', $mode = 'a+') {
        $enabled = false;

        if ($scope == 'site') {
            if ($this->settings['log_enabled']) {
                if ($this->settings['log_'.$name]) {
                    $enabled = true;
                }
            }
        } else {
            if ($this->network['log_enabled']) {
                if ($this->network['log_'.$name]) {
                    $enabled = true;
                }
            }
        }

        if ($enabled) {
            if (!isset($this->logs[$name])) {
                $path = $this->_get_log_file($name, $scope);
                $this->logs[$name] = new sct_log($path);
            }

            switch ($method) {
                case 'log':
                    $this->logs[$name]->log($data, $title, $mode);
                    break;
                case 'array_log':
                    $this->logs[$name]->array_log($data, $title, $mode);
                    break;
                case 'simple_log':
                    $this->logs[$name]->simple_log($data, $mode);
                    break;
                case 'dump':
                    $this->logs[$name]->dump($data, $title, $mode);
                    break;
                case 'simple_dump':
                    $this->logs[$name]->simple_dump($data, $mode);
                    break;
            }
        }
    }

    public function _get_log_root() {
        $uploads = wp_upload_dir();
        return trailingslashit($uploads['basedir']);
    }

    public function _get_log_file($name, $scope) {
        $root = $this->_get_log_root();
        $path = $root.($scope == 'network' ? 'network.' : '').$this->log_files[$name];

        if (!file_exists($path)) {
            if (is_writable($root)) {
                $handler = fopen($path, 'w');

                if ($handler === false) {
                    return false;
                } else {
                    fclose($handler);
                    return $path;
                }
            }
        }

        return $path;
    }
}

global $sct_core_loader;
$sct_core_loader = new sct_loader();

function smart_sct_core() {
    global $sct_core_loader;
    return $sct_core_loader;
}

function sct_log($name, $scope, $data, $title = '', $method = 'dump', $mode = 'a+') {
    smart_sct_core()->log($name, $scope, $data, $title, $method, $mode);
}

?>