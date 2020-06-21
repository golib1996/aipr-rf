<?php

if (!defined('ABSPATH')) exit;

class sct_removal_posts extends sct_removal {
    public $scope = 'site';
    public $code = 'posts';

    public function get_notice($args = array()) {
        global $wpdb;

        $items = array(
            __("Tool will remove posts, their meta records and the terms relationships they might have.", "smart-cleanup-tools"),
            sprintf(__("Data will be removed from %s, %s and %s tables.", "smart-cleanup-tools"), $wpdb->posts, $wpdb->postmeta, $wpdb->term_relationships)
        );

        return $items;
    }

    public function get_title($args = array()) {
        $cpt = get_post_type_object($args['cpt']);

        $render = $cpt->label.', ';

        switch ($args['remove']) {
            case 'draft':
                $render.= __("remove drafts", "smart-cleanup-tools");
                break;
        }

        return $render;
    }

    public function check($args = array()) {
        global $wpdb;

        $render = $this->prepare_check_header($args);
        $render.= '<p style="font-weight: bold;">';

        switch ($args['remove']) {
            case 'draft':
                $sql_terms = sprintf("SELECT COUNT(*) as records FROM %s t WHERE t.object_id IN (SELECT p.ID FROM %s p WHERE p.post_type = '%s' and p.post_status = 'draft')", $wpdb->term_relationships, $wpdb->posts, $args['cpt']);
                $sql_meta = sprintf("SELECT COUNT(*) as records FROM %s m WHERE m.post_id IN (SELECT p.ID FROM %s p WHERE p.post_type = '%s' and p.post_status = 'draft')", $wpdb->postmeta, $wpdb->posts, $args['cpt']);
                $sql_posts = sprintf("SELECT COUNT(*) as records FROM %s p WHERE p.post_type = '%s' and p.post_status = 'draft'", $wpdb->posts, $args['cpt']);

                $posts = $wpdb->get_var($sql_posts);
                $total = $posts + $wpdb->get_var($sql_terms) + $wpdb->get_var($sql_meta);

                $render.= __("Total drafts found", "smart-cleanup-tools").': '.$posts.'<br/>';
                $render.= __("Total records for removal", "smart-cleanup-tools").': '.$total;
                break;
        }

        $args['__active__'] = $total;

        $render.= '</p>';
        $render.= $this->prepare_check_footer($args);

        return $render;
    }

    public function remove($args = array()) {
        global $wpdb;

        $render = $this->prepare_remove_header($args);

        switch ($args['remove']) {
            case 'draft':
                $sql = sprintf("DELETE p, t, m FROM %s p LEFT JOIN %s t ON t.object_id = p.ID LEFT JOIN %s m ON m.post_id = p.ID WHERE p.post_type = '%s' and p.post_status = 'draft'", $wpdb->posts, $wpdb->term_relationships, $wpdb->postmeta, $args['cpt']);
                break;
        }

        $wpdb->query($sql);

        $render.= '<p style="font-weight: bold;">'.__("Total records removed", "smart-cleanup-tools").': '.$wpdb->rows_affected.'</p>';

        $render.= $this->prepare_remove_footer($args);

        return $render;
    }
}

?>