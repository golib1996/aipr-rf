<?php

if (!defined('ABSPATH')) exit;

class sct_cleanup_gravityforms_leads extends sct_cleanup {
    public $lead_status = '';

    public function form($args = array()) {
        $return = __("This will remove all GravityForms submissions (leads) with status set to", "smart-cleanup-tools");
        $return.= " '".$this->lead_status."'. ";
        $return.= __("This will affect data in 5 GravityForms tables.", "smart-cleanup-tools");

        return $return;
    }

    public function run($args = array(), $stats = array()) {
        global $wpdb;

        $sql = sprintf("DELETE l, ld, ldl, lm, ls FROM %srg_lead l LEFT JOIN %srg_lead_detail ld ON l.id = ld.lead_id LEFT JOIN %srg_lead_detail_long ldl ON ld.id = ldl.lead_detail_id LEFT JOIN %srg_lead_meta lm ON l.id = lm.lead_id LEFT JOIN %srg_lead_notes ls ON l.id = ls.lead_id WHERE l.`status` = '%s'", $wpdb->prefix, $wpdb->prefix, $wpdb->prefix, $wpdb->prefix, $wpdb->prefix, $this->lead_status);

        $wpdb->query($sql);
        sct_log('sql_run', 'site', $sql, get_class($this), 'log');
        $records = $wpdb->rows_affected;

        $data = array(
            'display' => __("Records Removed", "smart-cleanup-tools").': <strong>'.($stats['total'] + $records).'</strong>',
            'total' => $stats['total'] + $records,
            'counter' => $stats['counter'] + 1,
            'last' => $records,
            'last_display' => __("Records Removed", "smart-cleanup-tools").': <strong>'.$records.'</strong>',
            'last_run' => time()
        );

        return $data;
    }

    public function check($args = array()) {
        global $wpdb;

        $sql = sprintf("SELECT COUNT(*) as records, SUM(LENGTH(ldl.value) + LENGTH(ld.value) + LENGTH(l.user_agent) + LENGTH(l.source_url) + 128) as size FROM %srg_lead l LEFT JOIN %srg_lead_detail ld ON l.id = ld.lead_id LEFT JOIN %srg_lead_detail_long ldl ON ld.id = ldl.lead_detail_id LEFT JOIN %srg_lead_meta lm ON l.id = lm.lead_id LEFT JOIN %srg_lead_notes ls ON l.id = ls.lead_id WHERE l.`status` = '%s'", $wpdb->prefix, $wpdb->prefix, $wpdb->prefix, $wpdb->prefix, $wpdb->prefix, $this->lead_status);
        $data = $wpdb->get_row($sql);
        sct_log('sql_check', 'site', $sql, get_class($this), 'log');

        $this->check = array(
            'display' => __("Found", "smart-cleanup-tools").': <strong>'.$data->records.'</strong> | '.
                         __("Estimated size", "smart-cleanup-tools").': <strong>'.sct_size_format($data->size).'</strong>',
            'checked' => $data->records > 128,
            'records' => $data->records,
            'size' => $data->size
        );

        return $this->check;
    }
}

class sct_cleanup_posts extends sct_cleanup {
    public $post_status = '';

    public function form($args = array()) {
        $defaults = array('cpt' => array());
        $args = wp_parse_args($args, $defaults);

        $args['cpt'] = sct_post_types_filtered($args['cpt']);

        $return = '<div class="sct-tool-info-block">';
        $return.= __("This will remove all posts and postmetas with post status set to", "smart-cleanup-tools");
        $return.= " '".$this->post_status."'.";
        $return.= '</div>';
        $return.= '<div class="sct-tool-extra-option">';
        $return.= $this->draw_posttypes($args['cpt']);
        $return.= '</div>';

        return $return;
    }

    public function run($args = array(), $stats = array()) {
        $defaults = array('cpt' => array());
        $args = wp_parse_args($args, $defaults);
        $args['cpt'] = sct_post_types_filtered($args['cpt']);

        global $wpdb;

        $sql = sprintf("DELETE p, t, m FROM %s p LEFT JOIN %s t ON t.object_id = p.ID LEFT JOIN %s m ON m.post_id = p.ID WHERE p.post_status = '%s'", $wpdb->posts, $wpdb->term_relationships, $wpdb->postmeta, $this->post_status);

        if (!empty($args['cpt'])) {
            $sql.= " AND p.post_type in ('".join("', '", $args['cpt'])."')";
        }

        $wpdb->query($sql);
        sct_log('sql_run', 'site', $sql, get_class($this), 'log');
        $records = $wpdb->rows_affected;

        $data = array(
            'display' => __("Records Removed", "smart-cleanup-tools").': <strong>'.($stats['total'] + $records).'</strong>',
            'total' => $stats['total'] + $records,
            'counter' => $stats['counter'] + 1,
            'last' => $records,
            'last_display' => __("Records Removed", "smart-cleanup-tools").': <strong>'.$records.'</strong>',
            'last_run' => time()
        );

        return $data;
    }

    public function check($args = array()) {
        global $wpdb;

        $sql_terms = sprintf("SELECT COUNT(*) as records, SUM(LENGTH(t.object_id) + LENGTH(t.term_taxonomy_id) + LENGTH(t.term_order)) as size FROM %s t WHERE t.object_id IN (SELECT p.ID FROM %s p WHERE p.post_status = '%s')", $wpdb->term_relationships, $wpdb->posts, $this->post_status);
        $sql_meta = sprintf("SELECT COUNT(*) as records, SUM(LENGTH(m.meta_id) + LENGTH(m.post_id) + LENGTH(m.meta_key) + LENGTH(m.meta_value)) as size FROM %s m WHERE m.post_id IN (SELECT p.ID FROM %s p WHERE p.post_status = '%s')", $wpdb->postmeta, $wpdb->posts, $this->post_status);
        $sql_posts = sprintf("SELECT COUNT(*) as records, SUM(LENGTH(p.post_content) + LENGTH(p.post_title) + LENGTH(p.post_excerpt) + LENGTH(p.post_name) + LENGTH(p.guid) + LENGTH(p.post_mime_type) + LENGTH(p.post_type) + LENGTH(p.post_status) + 160) as size FROM %s p WHERE p.post_status = '%s'", $wpdb->posts, $this->post_status);
        
        $data_terms = $wpdb->get_row($sql_terms);
        $data_meta = $wpdb->get_row($sql_meta);
        $data_posts = $wpdb->get_row($sql_posts);

        sct_log('sql_check', 'site', $sql_terms.SCT_EOL.$sql_meta.SCT_EOL.$sql_posts, get_class($this), 'log');

        $records = $data_terms->records + $data_meta->records + $data_posts->records;
        $size = is_null($data_terms->size) ? 0 : $data_terms->size + is_null($data_meta->size) ? 0 : $data_meta->size + is_null($data_posts->size) ? 0 : $data_posts->size;

        $this->check = array(
            'display' => __("Found", "smart-cleanup-tools").': <strong>'.$records.'</strong> | '.
                         __("Estimated size", "smart-cleanup-tools").': <strong>'.sct_size_format($size).'</strong>',
            'checked' => $this->auto ? $records > 32 : false,
            'records' => $records,
            'size' => $size
        );

        return $this->check;
    }

    public function details($args = array()) {
        if ($this->has_results()) {
            global $wpdb;

            $display = array();
            $post_types = get_post_types(array(), 'objects');

            foreach ($post_types as $cpt => $obj) {
                $sql_terms = sprintf("SELECT COUNT(*) as records, SUM(LENGTH(t.object_id) + LENGTH(t.term_taxonomy_id) + LENGTH(t.term_order)) as size FROM %s t WHERE t.object_id IN (SELECT p.ID FROM %s p WHERE p.post_status = '%s' and p.post_type = '%s')", $wpdb->term_relationships, $wpdb->posts, $this->post_status, $cpt);
                $sql_meta = sprintf("SELECT COUNT(*) as records, SUM(LENGTH(m.meta_id) + LENGTH(m.post_id) + LENGTH(m.meta_key) + LENGTH(m.meta_value)) as size FROM %s m WHERE m.post_id IN (SELECT p.ID FROM %s p WHERE p.post_status = '%s' and p.post_type = '%s')", $wpdb->postmeta, $wpdb->posts, $this->post_status, $cpt);
                $sql_posts = sprintf("SELECT COUNT(*) as records, SUM(LENGTH(p.post_content) + LENGTH(p.post_title) + LENGTH(p.post_excerpt) + LENGTH(p.post_name) + LENGTH(p.guid) + LENGTH(p.post_mime_type) + LENGTH(p.post_type) + LENGTH(p.post_status) + 160) as size FROM %s p WHERE p.post_status = '%s' and p.post_type = '%s'", $wpdb->posts, $this->post_status, $cpt);

                $data_terms = $wpdb->get_row($sql_terms);
                $data_meta = $wpdb->get_row($sql_meta);
                $data_posts = $wpdb->get_row($sql_posts);

                sct_log('sql_check', 'site', $sql_terms.SCT_EOL.$sql_meta.SCT_EOL.$sql_posts, get_class($this), 'log');

                $records = $data_terms->records + $data_meta->records + $data_posts->records;
                $size = is_null($data_terms->size) ? 0 : $data_terms->size + is_null($data_meta->size) ? 0 : $data_meta->size + is_null($data_posts->size) ? 0 : $data_posts->size;

                if ($records > 0) {
                    $display['__header__'] = array(__("Post Type", "smart-cleanup-tools"), __("Records", "smart-cleanup-tools"), __("Size", "smart-cleanup-tools"));
                    
                    $display[$cpt] = array(
                        'label' => $obj->label, 
                        'records' => $records, 
                        'size' => sct_size_format($size)
                    );
                }
            }

            return empty($display) ? false : $display;
        }

        return false;
    }
}

class sct_cleanup_comments extends sct_cleanup {
    public $comment_status = '';

    public function form($args = array()) {
        $return = __("This will remove all comments and commentmeta records with comment status set to", "smart-cleanup-tools");
        $return.= " '".$this->comment_status."'.";

        return $return;
    }

    public function run($args = array(), $stats = array()) {
        $defaults = array();
        $args = wp_parse_args($args, $defaults);

        global $wpdb;

        $sql = sprintf("DELETE c, m FROM %s c LEFT JOIN %s m ON c.comment_ID = m.comment_id WHERE comment_approved = '%s'", $wpdb->comments, $wpdb->commentmeta, $this->comment_status);

        $wpdb->query($sql);
        sct_log('sql_run', 'site', $sql, get_class($this), 'log');
        $records = $wpdb->rows_affected;

        $data = array(
            'display' => __("Records Removed", "smart-cleanup-tools").': <strong>'.($stats['total'] + $records).'</strong>',
            'total' => $stats['total'] + $records,
            'counter' => $stats['counter'] + 1,
            'last' => $records,
            'last_display' => __("Records Removed", "smart-cleanup-tools").': <strong>'.$records.'</strong>',
            'last_run' => time()
        );

        return $data;
    }

    public function check($args = array()) {
        global $wpdb;

        $sql = sprintf("SELECT COUNT(*) as records, SUM(LENGTH(c.comment_content) + LENGTH(c.comment_author) + LENGTH(c.comment_author_email) + LENGTH(c.comment_author_url) + LENGTH(c.comment_agent) + LENGTH(c.comment_author_IP) + 64) as size FROM %s c LEFT JOIN %s m ON c.comment_ID = m.comment_id WHERE comment_approved = '%s'", $wpdb->comments, $wpdb->commentmeta, $this->comment_status);
        $data = $wpdb->get_row($sql);
        sct_log('sql_check', 'site', $sql, get_class($this), 'log');

        $this->check = array(
            'display' => __("Found", "smart-cleanup-tools").': <strong>'.$data->records.'</strong> | '.
                         __("Estimated size", "smart-cleanup-tools").': <strong>'.sct_size_format($data->size).'</strong>',
            'checked' => $data->records > 16,
            'records' => $data->records,
            'size' => $data->size
        );

        return $this->check;
    }
}

?>