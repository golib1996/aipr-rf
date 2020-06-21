<?php

if (!defined('ABSPATH')) exit;

class sct_cleanup_reset_post_locks extends sct_cleanup {
    public function form($args = array()) {
         return __("To control who edits which post, WordPress uses lock and last user editor meta fields. This tool allows you to remove all lock values from all posts.", "smart-cleanup-tools");
    }

    public function run($args = array(), $stats = array()) {
        $defaults = array();
        $args = wp_parse_args($args, $defaults);

        global $wpdb;

        $sql = sprintf("DELETE FROM %spostmeta WHERE `meta_key` in ('_edit_last', '_edit_lock')", $wpdb->prefix);

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

        $sql = sprintf("SELECT COUNT(*) as records, SUM(LENGTH(meta_key)) as size FROM %spostmeta WHERE `meta_key` in ('_edit_last', '_edit_lock')", $wpdb->prefix);
        $data = $wpdb->get_row($sql);
        sct_log('sql_check', 'site', $sql, get_class($this), 'log');

        $this->check = array(
            'display' => __("Found", "smart-cleanup-tools").': <strong>'.$data->records.'</strong> | '.
                         __("Estimated size", "smart-cleanup-tools").': <strong>'.sct_size_format($data->size).'</strong>',
            'checked' => false,
            'records' => $data->records,
            'size' => $data->size
        );

        return $this->check;
    }

    public function help($args = array()) {
        $help = '<ul>';
        $help.= '<li>'.__("This tool is useful for websites that have many different authors and editors that make frequent changes to posts.", "smart-cleanup-tools").'</li>';
        $help.= '</ul>';

        return $help;
    }
}

class sct_cleanup_reset_rewrite_rules extends sct_cleanup {
    public function form($args = array()) {
         return __("In some cases it can happen that permalinks rewrite rules are not updated properly. Using this, rewrite rules definitions will be removed, forcing WordPress to regenerate them.", "smart-cleanup-tools");
    }

    public function run($args = array(), $stats = array()) {
        $defaults = array();
        $args = wp_parse_args($args, $defaults);

        global $wpdb;

        $sql = sprintf("DELETE FROM %soptions WHERE option_name = 'rewrite_rules'", $wpdb->prefix);

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

        $this->check = array(
            'display' => '<strong>rewrite_rules</strong> '.__("in", "smart-cleanup-tools").' <strong>'.$wpdb->options.'</strong>',
            'checked' => false,
            'records' => 1
        );

        return $this->check;
    }

    public function help($args = array()) {
        $help = '<ul>';
        $help.= '<li>'.__("Removing this will force WordPress to regenerate all rewrite rules on page load.", "smart-cleanup-tools").'</li>';
        $help.= '<li>'.__("In some cases, rules can get broken because of the bugs in plugins that modify rewrite rules.", "smart-cleanup-tools").'</li>';
        $help.= '</ul>';

        return $help;
    }
}

class sct_cleanup_reset_sidebars extends sct_cleanup {
    public function form($args = array()) {
        return __("When switching themes with different sidebars configuration, you might need to empty all sidebars. All widgets will be moved from sidebars into Inactive widgets area.", "smart-cleanup-tools");
    }

    public function run($args = array(), $stats = array()) {
        $defaults = array();
        $args = wp_parse_args($args, $defaults);

        global $wpdb;

        $new_sidebars = array();
        $inactive = array();
        $sidebars = get_option('sidebars_widgets');

        foreach ($sidebars as $bar => $values) {
            if ((is_array($values) && empty($values)) || !is_array($values)) {
                $new_sidebars[$bar] = $values;
            } else {
                $inactive = array_merge($inactive, $values);
                $new_sidebars[$bar] = array();
            }
        }

        $new_sidebars['wp_inactive_widgets'] = $inactive;
        update_option('sidebars_widgets', $new_sidebars);

        $data = array(
            'display' => __("Records Reseted", "smart-cleanup-tools").': <strong>'.($stats['total'] + 1).'</strong>',
            'total' => $stats['total'] + 1,
            'counter' => $stats['counter'] + 1,
            'last' => 1,
            'last_display' => __("Records Reseted", "smart-cleanup-tools").': <strong>1</strong>',
            'last_run' => time()
        );

        return $data;
    }

    public function check($args = array()) {
        global $wpdb;

        $this->check = array(
            'display' => '<strong>sidebars_widgets</strong> '.__("in", "smart-cleanup-tools").' <strong>'.$wpdb->options.'</strong>',
            'checked' => false,
            'records' => 1
        );

        return $this->check;
    }
}

class sct_cleanup_reset_sct_plugin extends sct_cleanup {
    public function form($args = array()) {
        return __("This will reset Smart Cleanup Tools settings and remove all gathered statistics.", "smart-cleanup-tools");
    }

    public function run($args = array(), $stats = array()) {
        $defaults = array();
        $args = wp_parse_args($args, $defaults);

        smart_sct_core()->settings['statistics'] = array('site' => array(), 'reset' => array());

        delete_option('smart-cleanup-tools');
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
            'display' => '<strong>smart-cleanup-tools</strong> '.__("in", "smart-cleanup-tools").' <strong>'.$wpdb->options.'</strong>',
            'checked' => false,
            'records' => 1
        );

        return $this->check;
    }
}

?>