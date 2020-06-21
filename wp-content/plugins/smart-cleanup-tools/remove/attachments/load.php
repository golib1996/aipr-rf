<?php

if (!defined('ABSPATH')) exit;

class sct_removal_attachments extends sct_removal {
    public $scope = 'site';
    public $code = 'attachments';

    public function get_notice($args = array()) {
        global $wpdb;

        $items = array(
            __("Tool will remove attachments, their meta records and the terms relationships they might have.", "smart-cleanup-tools"),
            __("Also, files for these attachments will be removed from disc.", "smart-cleanup-tools"),
            sprintf(__("Data will be removed from %s, %s and %s tables.", "smart-cleanup-tools"), $wpdb->posts, $wpdb->postmeta, $wpdb->term_relationships)
        );

        return $items;
    }

    public function get_title($args = array()) {
        $render = '';

        switch ($args['remove']) {
            case 'missing_parent':
                $render.= __("Missing parent posts", "smart-cleanup-tools");
                break;
            case 'missing_file':
                $render.= __("Missing file from disk", "smart-cleanup-tools");
                break;
            case 'unattached':
                $render.= __("Unattached", "smart-cleanup-tools");
                break;
        }

        if ($args['mime'] != '') {
            $render.= ' ('.$args['mime'].')';
        }

        return $render;
    }

    public function check($args = array()) {
        global $wpdb;

        $render = $this->prepare_check_header($args);

        switch ($args['remove']) {
            case 'missing_parent':
                $sql = "SELECT count(*) FROM ".$wpdb->prefix."posts WHERE post_type = 'attachment'
                        AND post_parent > 0 AND post_parent NOT IN (
                                SELECT ID FROM ".$wpdb->prefix."posts WHERE post_type != 'attachment')";
                break;
            case 'missing_file':
                $sql = "SELECT ID FROM ".$wpdb->prefix."posts WHERE post_type = 'attachment'";
                break;
            case 'unattached':
                $sql = "SELECT count(*) FROM ".$wpdb->prefix."posts WHERE post_type = 'attachment'
                        AND post_parent = 0";
                break;
        }

        if ($args['mime'] != '') {
            $sql.= " AND post_mime_type = '".$args['mime']."'";
        }

        if ($args['remove'] == 'missing_file') {
            $args['__active__'] = 0;
            $raw = $wpdb->get_results($sql);

            foreach ($raw as $attachment) {
                $file_path = get_attached_file($attachment->ID, true);

                if (!file_exists($file_path)){
                    $args['__active__']++;
                }
            }
        } else {
            $args['__active__'] = $wpdb->get_var($sql);
        }

        $render.= '<p style="font-weight: bold;">'.__("Total attachments found", "smart-cleanup-tools").': '.$args['__active__'].'</p>';

        $render.= $this->prepare_check_footer($args);

        return $render;
    }

    public function remove($args = array()) {
        global $wpdb;

        $render = $this->prepare_remove_header($args);

        switch ($args['remove']) {
            case 'missing_parent':
                $sql = "SELECT ID FROM ".$wpdb->prefix."posts WHERE post_type = 'attachment'
                        AND post_parent > 0 AND post_parent NOT IN (
                                SELECT ID FROM ".$wpdb->prefix."posts WHERE post_type != 'attachment')";
                break;
            case 'missing_file':
                $sql = "SELECT ID FROM ".$wpdb->prefix."posts WHERE post_type = 'attachment'";
                break;
            case 'unattached':
                $sql = "SELECT ID FROM ".$wpdb->prefix."posts WHERE post_type = 'attachment'
                        AND post_parent = 0";
                break;
        }

        if ($args['mime'] != '') {
            $sql.= " AND post_mime_type = '".$args['mime']."'";
        }

        $count = 0;
        $cache = 0;
        $raw = $wpdb->get_results($sql);

        foreach ($raw as $attachment) {
            $delete = false;

            if ($args['remove'] == 'missing_file') {
                $file_path = get_attached_file($attachment->ID, true);

                if (!file_exists($file_path)) {
                    $delete = true;
                }
            } else {
                $delete = true;
            }

            if ($delete) {
                $deleted = wp_delete_attachment($attachment->ID, true);

                if ($deleted !== false) {
                    $count++;
                }
            }

            $cache++;

            if ($cache == 128) {
                $cache = 0;

                sct_cache_flush();
            }
        }

        $render.= '<p style="font-weight: bold;">'.__("Total attachments removed", "smart-cleanup-tools").': '.$count.'</p>';

        $render.= $this->prepare_remove_footer($args);

        return $render;
    }

    public function list_mime_types() {
        global $wpdb;

        $sql = "SELECT post_mime_type, count(*) AS posts FROM ".$wpdb->prefix."posts
                WHERE post_type = 'attachment' GROUP BY post_mime_type ORDER BY posts DESC";
        return $wpdb->get_results($sql);
    }
}

?>