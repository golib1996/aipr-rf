<?php

if (!defined('ABSPATH')) exit;

class sct_removal_postmeta extends sct_removal {
    public $scope = 'site';
    public $code = 'postmeta';

    public function get_notice($args = array()) {
        global $wpdb;

        $items = array();

        switch ($args['remove']) {
            case 'empty':
                $items[] = __("This tool will remove all postmeta records with no value.", "smart-cleanup-tools");
                $items[] = __("Some plugins are known to use postmeta records that only have name and no value, so using this tool can cause problems with some plugins.", "smart-cleanup-tools");
                break;
        }

        $items[] = sprintf(__("Data will be removed from %stable.", "smart-cleanup-tools"), $wpdb->postmeta);

        return $items;
    }

    public function get_title($args = array()) {
        $render = '';

        switch ($args['remove']) {
            case 'empty':
                $render.= __("Remove postmeta records with no value", "smart-cleanup-tools");
                break;
        }

        return $render;
    }

    public function check($args = array()) {
        global $wpdb;

        $render = $this->prepare_check_header($args);

        switch ($args['remove']) {
            case 'empty':
                $sql = sprintf("SELECT COUNT(*) as records, SUM(LENGTH(meta_key)) as size FROM %s WHERE `meta_value` = ''", $wpdb->postmeta);
                break;
        }

        $results = $wpdb->get_row($sql);

        $args['__active__'] = $results->records;

        $render.= '<p style="font-weight: bold;">'.__("Total records found", "smart-cleanup-tools").': '.$args['__active__'];
        $render.= '<br/>'.__("Estimated data size", "smart-cleanup-tools").': '.sct_size_format($results->size).'</p>';

        $render.= $this->prepare_check_footer($args);

        return $render;
    }

    public function remove($args = array()) {
        global $wpdb;

        $render = $this->prepare_remove_header($args);

        switch ($args['remove']) {
            case 'empty':
                $sql = sprintf("DELETE FROM %s WHERE `meta_value` = ''", $wpdb->postmeta);
                break;
        }

        $wpdb->query($sql);

        $render.= '<p style="font-weight: bold;">'.__("Total records removed", "smart-cleanup-tools").': '.$wpdb->rows_affected.'</p>';

        $render.= $this->prepare_remove_footer($args);

        return $render;
    }
}
