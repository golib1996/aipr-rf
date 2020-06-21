<?php

if (!defined('ABSPATH')) exit;

class sct_cleanup_netreset_sct_plugin extends sct_cleanup {
    public function form($args = array()) {
        return __("This will reset this plugin and remove all gathered statistics.", "smart-cleanup-tools");
    }

    public function run($args = array(), $stats = array()) {
        $defaults = array();
        $args = wp_parse_args($args, $defaults);

        smart_sct_core()->network['statistics'] = array('network' => array(), 'netreset' => array());

        delete_site_option('smart-cleanup-tools');
        $records = 1;

        $data = array(
            'display' => __("Records Reseted", "smart-cleanup-tools").': <strong>'.($stats['total'] + $records).'</strong>',
            'total' => $stats['total'] + $records,
            'counter' => $stats['counter'] + 1,
            'last' => $records,
            'last_display' => __("Records Reseted", "smart-cleanup-tools").': <strong>'.$records.'</strong>',
            'last_run' => time()
        );

        return $data;
    }

    public function check($args = array()) {
        global $wpdb;

        $this->check = array(
            'display' => '<strong>smart-cleanup-tools</strong> '.__("in", "smart-cleanup-tools").' <strong>'.$wpdb->sitemeta.'</strong>',
            'checked' => false,
            'records' => 1
        );

        return $this->check;
    }
}

?>