<?php

if (!defined('ABSPATH')) exit;

class sct_removal_terms_missing_tax extends sct_removal {
    public $scope = 'site';
    public $code = 'terms_missing_tax';

    public function get_notice($args = array()) {
        global $wpdb;

        $items = array(
            sprintf(__("Data will be removed only from %s and %s tables.", "smart-cleanup-tools"), $wpdb->term_taxonomy, $wpdb->term_relationships),
            sprintf(__("Terms in %s table might be used by other taxonomies and they are not removed.", "smart-cleanup-tools"), $wpdb->terms),
            sprintf(__("You can use cleanup tool to remove orphaned terms in %s table after this process ends.", "smart-cleanup-tools"), $wpdb->terms)
            
        );

        return $items;
    }

    public function get_title($args = array()) {
        $render = __("Taxonomy", "smart-cleanup-tools").' "'.$args['tax'].'"';

        return $render;
    }

    public function check($args = array()) {
        global $wpdb;

        $render = $this->prepare_check_header($args);

        $sql = sprintf("SELECT COUNT(*) as records FROM %s tt LEFT JOIN %s tr ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE tt.taxonomy = '%s'", $wpdb->term_taxonomy, $wpdb->term_relationships, $args['tax']);

        $args['__active__'] = $wpdb->get_var($sql);

        $render.= '<p style="font-weight: bold;">'.__("Total records for removal", "smart-cleanup-tools").': '.$args['__active__'].'</p>';

        $render.= $this->prepare_check_footer($args);

        return $render;
    }

    public function remove($args = array()) {
        global $wpdb;

        $render = $this->prepare_remove_header($args);

        $sql = sprintf("DELETE tt, tr FROM %s tt LEFT JOIN %s tr ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE tt.taxonomy = '%s'", $wpdb->term_taxonomy, $wpdb->term_relationships, $args['tax']);

        $wpdb->query($sql);

        $render.= '<p style="font-weight: bold;">'.__("Total records removed", "smart-cleanup-tools").': '.$wpdb->rows_affected.'</p>';

        $render.= $this->prepare_remove_footer($args);

        return $render;
    }

    public function list_taxonomies() {
        global $wpdb;

        $sql = "select taxonomy, count(*) as terms from ".$wpdb->prefix."term_taxonomy
                where taxonomy not in ('category', 'post_tag', 'nav_menu', 'link_category', 'post_format')
                group by taxonomy order by terms DESC";
        $raw = $wpdb->get_results($sql);

        $list = array();
        foreach ($raw as $r) {
            if (!taxonomy_exists($r->taxonomy)) {
                $list[$r->taxonomy] = $r;
            }
        }

        return $list;
    }
}

?>