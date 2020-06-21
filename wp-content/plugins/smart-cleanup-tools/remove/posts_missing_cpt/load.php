<?php

if (!defined('ABSPATH')) exit;

class sct_removal_posts_missing_cpt extends sct_removal {
    public $scope = 'site';
    public $code = 'posts_missing_cpt';

    public function get_notice($args = array()) {
        global $wpdb;

        $items = array(
            __("Tool will remove posts, their meta records and the terms relationships they might have.", "smart-cleanup-tools"),
            sprintf(__("Data will be removed from %s, %s and %s tables.", "smart-cleanup-tools"), $wpdb->posts, $wpdb->postmeta, $wpdb->term_relationships)
        );

        return $items;
    }

    public function get_title($args = array()) {
        $render = __("Post Type", "smart-cleanup-tools").' "'.$args['cpt'].'"';

        return $render;
    }

    public function check($args = array()) {
        global $wpdb;

        $render = $this->prepare_check_header($args);

        $sql = sprintf("SELECT COUNT(*) as records FROM %s p LEFT JOIN %s t ON t.object_id = p.ID LEFT JOIN %s m ON m.post_id = p.ID WHERE p.post_type = '%s'", $wpdb->posts, $wpdb->term_relationships, $wpdb->postmeta, $args['cpt']);

        $args['__active__'] = $wpdb->get_var($sql);

        $render.= '<p style="font-weight: bold;">'.__("Total records for removal", "smart-cleanup-tools").': '.$args['__active__'].'</p>';

        $render.= $this->prepare_check_footer($args);

        return $render;
    }

    public function remove($args = array()) {
        global $wpdb;

        $render = $this->prepare_remove_header($args);

        $sql = sprintf("DELETE p, t, m FROM %s p LEFT JOIN %s t ON t.object_id = p.ID LEFT JOIN %s m ON m.post_id = p.ID WHERE p.post_type = '%s'", $wpdb->posts, $wpdb->term_relationships, $wpdb->postmeta, $args['cpt']);

        $wpdb->query($sql);

        $render.= '<p style="font-weight: bold;">'.__("Total records removed", "smart-cleanup-tools").': '.$wpdb->rows_affected.'</p>';

        $render.= $this->prepare_remove_footer($args);

        return $render;
    }

    public function list_post_types() {
        global $wpdb;

        $sql = "select post_type, count(*) as posts from ".$wpdb->prefix."posts
                where post_type not in ('post', 'page', 'attachment', 'revision', 'nav_menu_item')
                group by post_type order by posts DESC";
        $raw = $wpdb->get_results($sql);

        $list = array();
        foreach ($raw as $r) {
            if (!post_type_exists($r->post_type)) {
                $list[$r->post_type] = $r;
            }
        }

        return $list;
    }
}

?>