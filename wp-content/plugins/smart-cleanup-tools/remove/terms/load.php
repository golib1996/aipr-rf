<?php

if (!defined('ABSPATH')) exit;

class sct_removal_terms extends sct_removal {
    public $scope = 'site';
    public $code = 'terms';

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
        $taxonomy = get_taxonomy($args['tax']);

        $render = $taxonomy->labels->name.', ';

        switch ($args['scope']) {
            case 'unassigned':
                $render.= __("remove unassigned terms", "smart-cleanup-tools");
                break;
            case 'all':
                $render.= __("remove all available terms", "smart-cleanup-tools");
                break;
        }

        return $render;
    }

    public function check($args = array()) {
        global $wpdb;

        $render = $this->prepare_check_header($args);

        $taxonomy = $args['tax'];
        $scope = $args['scope'];

        switch ($scope) {
            case 'unassigned':
                $sql = sprintf("SELECT 0 as assigned, count(*) as terms FROM %s tt WHERE tt.taxonomy = '%s' AND tt.term_taxonomy_id NOT IN (SELECT term_taxonomy_id FROM %s)", $wpdb->term_taxonomy, $taxonomy, $wpdb->term_relationships);
                break;
            case 'all':
                $sql = sprintf("SELECT tt.count > 0 as assigned, count(*) as terms FROM %s tt WHERE tt.taxonomy = '%s' GROUP BY tt.count > 0", $wpdb->term_taxonomy, $taxonomy);
                break;
        }

        $messages = array();
        $args['__active__'] = 0;
        $results = $wpdb->get_results($sql);

        foreach ($results as $row) {
            $args['__active__']+= $row->terms;

            if ($row->assigned == 0) {
                $messages[] = __("Unassigned terms", "smart-cleanup-tools").': '.$row->terms;
            } else if ($row->assigned == 1) {
                $messages[] = __("Assigned terms", "smart-cleanup-tools").': '.$row->terms;
            }
        }

        $render.= '<p>'.join('<br/>', $messages).'</p>';

        $render.= '<p style="font-weight: bold;">'.__("Total terms for removal", "smart-cleanup-tools").': '.$args['__active__'].'</p>';

        $render.= $this->prepare_check_footer($args);

        return $render;
    }

    public function remove($args = array()) {
        global $wpdb;

        $render = $this->prepare_remove_header($args);

        $taxonomy = $args['tax'];
        $scope = $args['scope'];

        switch ($scope) {
            case 'unassigned':
                $sql = sprintf("DELETE tt FROM %s tt WHERE tt.taxonomy = '%s' AND tt.term_taxonomy_id NOT IN (SELECT term_taxonomy_id FROM %s)", $wpdb->term_taxonomy, $taxonomy, $wpdb->term_relationships);
                break;
            case 'all':
                $sql = sprintf("DELETE tt, tr FROM %s tt LEFT JOIN %s tr ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE tt.taxonomy = '%s'", $wpdb->term_taxonomy, $wpdb->term_relationships, $taxonomy);
                break;
        }

        $wpdb->query($sql);

        $render.= '<p style="font-weight: bold;">'.__("Total records removed", "smart-cleanup-tools").': '.$wpdb->rows_affected.'</p>';

        $render.= $this->prepare_remove_footer($args);

        return $render;
    }
}

?>