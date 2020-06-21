<?php

if (!defined('ABSPATH')) exit;

class sct_removal_comments extends sct_removal {
    public $scope = 'site';
    public $code = 'comments';

    public function get_notice($args = array()) {
        global $wpdb;

        $items = array();

        switch ($args['remove']) {
            case 'remove_pingbacks':
                $items[] = __("Tool will remove all comments marked as pinbacks.", "smart-cleanup-tools");
                break;
            case 'clean_useragent':
                $items[] = __("Tool will remove user agent data stored for each comment in the comments table.", "smart-cleanup-tools");
                break;
            case 'clean_akismet':
                $items[] = __("Tool will remove records from Akismet plugin, stored in the comments meta table.", "smart-cleanup-tools");
                break;
            case 'empty_commentmeta':
                $items[] = __("Tool will remove all commentmeta records with no value.", "smart-cleanup-tools");
                $items[] = __("Some plugins are known to use commentmeta records that only have name and no value, so using this tool can cause problems with some plugins.", "smart-cleanup-tools");
                break;
        }

        $items[] = sprintf(__("Data will be removed from %s and %s tables.", "smart-cleanup-tools"), $wpdb->comments, $wpdb->commentmeta);

        return $items;
    }

    public function get_title($args = array()) {
        $render = '';

        switch ($args['remove']) {
            case 'remove_pingbacks':
                $render.= __("Remove pingbacks", "smart-cleanup-tools");
                break;
            case 'clean_useragent':
                $render.= __("Remove user agent data", "smart-cleanup-tools");
                break;
            case 'clean_akismet':
                $render.= __("Remove Akismet data", "smart-cleanup-tools");
                break;
            case 'empty_commentmeta':
                $render.= __("Remove commentmeta records with no value", "smart-cleanup-tools");
                break;
        }

        return $render;
    }

    public function check($args = array()) {
        global $wpdb;

        $render = $this->prepare_check_header($args);

        switch ($args['remove']) {
            case 'remove_pingbacks':
                $sql = sprintf("SELECT COUNT(*) as records, SUM(LENGTH(comment_content) * 2 + LENGTH(comment_agent)) as size FROM %s c LEFT JOIN %s m ON c.comment_ID = m.comment_id WHERE comment_type = 'pingback'", $wpdb->comments, $wpdb->commentmeta);
                break;
            case 'clean_useragent':
                $sql = sprintf("SELECT COUNT(*) as records, SUM(LENGTH(comment_agent)) as size FROM %s WHERE comment_agent != ''", $wpdb->comments);
                break;
            case 'clean_akismet':
                $sql = sprintf("SELECT COUNT(*) as records, SUM(LENGTH(meta_value)) as size FROM %s WHERE meta_key in ('akismet_result', 'akismet_error', 'akismet_history', 'akismet_as_submitted', 'akismet_rechecking')", $wpdb->commentmeta);
                break;
            case 'empty_commentmeta':
                $sql = sprintf("SELECT COUNT(*) as records, SUM(LENGTH(meta_key)) as size FROM %s WHERE `meta_value` = ''", $wpdb->commentmeta);
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
            case 'remove_pingbacks':
                $sql = sprintf("DELETE c, m FROM %s c LEFT JOIN %s m ON c.comment_ID = m.comment_id WHERE comment_type = 'pingback'", $wpdb->comments, $wpdb->commentmeta);
                break;
            case 'clean_useragent':
                $sql = sprintf("UPDATE %s SET comment_agent = '' WHERE comment_agent != ''", $wpdb->comments);
                break;
            case 'clean_akismet':
                $sql = sprintf("DELETE FROM %s WHERE meta_key in ('akismet_result', 'akismet_error', 'akismet_history', 'akismet_as_submitted', 'akismet_rechecking')", $wpdb->commentmeta);
                break;
            case 'empty_commentmeta':
                $sql = sprintf("DELETE FROM %s WHERE `meta_value` = ''", $wpdb->commentmeta);
                break;
        }

        $wpdb->query($sql);

        $render.= '<p style="font-weight: bold;">'.__("Total records removed", "smart-cleanup-tools").': '.$wpdb->rows_affected.'</p>';

        $render.= $this->prepare_remove_footer($args);

        return $render;
    }
}

?>