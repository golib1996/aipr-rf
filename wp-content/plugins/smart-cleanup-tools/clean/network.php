<?php

if (!defined('ABSPATH')) exit;

class sct_cleanup_network_transient extends sct_cleanup {
    public function form($args = array()) {
        return __("Transient records are temporary data stored by WordPress and plugins. They have expiration data, and if they are missing, they will be regenerated on first use.", "smart-cleanup-tools");
    }

    public function run($args = array(), $stats = array()) {
        $defaults = array();
        $args = wp_parse_args($args, $defaults);

        global $wpdb;

        $sql = sprintf("DELETE FROM %ssitemeta WHERE meta_key LIKE '%s' or meta_key LIKE '%s' or meta_key LIKE '%s' or meta_key LIKE '%s'", $wpdb->base_prefix, "\_transient\_%", "\_site\_transient\_%", "\_transient\_timeout\_%", "\_site\_transient\_timeout\_%");

        $wpdb->query($sql);
        sct_log('sql_run', 'network', $sql, get_class($this), 'log');
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

        $sql = sprintf("SELECT COUNT(*) as records, SUM(LENGTH(meta_key) + LENGTH(meta_value) + LENGTH(site_id) + LENGTH(meta_id)) as size FROM %ssitemeta WHERE meta_key LIKE '%s' or meta_key LIKE '%s' or meta_key LIKE '%s' or meta_key LIKE '%s'", $wpdb->base_prefix, "\_transient\_%", "\_site\_transient\_%", "\_transient\_timeout\_%", "\_site\_transient\_timeout\_%");
        $data = $wpdb->get_row($sql);
        sct_log('sql_check', 'site', $sql, get_class($this), 'log');

        $this->check = array(
            'display' => __("Found", "smart-cleanup-tools").': <strong>'.$data->records.'</strong> | '.
                         __("Estimated size", "smart-cleanup-tools").': <strong>'.sct_size_format($data->size).'</strong>',
            'checked' => $data->records > 64,
            'records' => $data->records,
            'size' => $data->size
        );

        return $this->check;
    }
}

class sct_cleanup_network_db_overhead extends sct_cleanup {
    public function form($args = array()) {
         return __("Over time, data in the database due to constant update and deletion of records gets fragmented, creating overhead. Overhead can be removed and it can speed up database queries.", "smart-cleanup-tools");
    }

    public function run($args = array(), $stats = array()) {
        $defaults = array();
        $args = wp_parse_args($args, $defaults);

        global $wpdb;

        $tables = sct_get_list_of_tables(true);
        $overhead = 0;

        foreach ($tables as $t) {
            if ($t->Engine != 'InnoDB') {
                $overhead+= $t->Data_free;
            }
        }

        foreach ($tables as $t) {
            $wpdb->query("OPTIMIZE TABLE `".$t->Name."`");
        }

        $data = array(
            'display' => __("Overhead Removed", "smart-cleanup-tools").': <strong>'.sct_size_format($stats['total'] + $overhead).'</strong>',
            'total' => $stats['total'] + $overhead,
            'counter' => $stats['counter'] + 1,
            'last' => $overhead,
            'last_display' => __("Overhead Removed", "smart-cleanup-tools").': <strong>'.sct_size_format($overhead).'</strong>',
            'last_run' => time()
        );

        return $data;
    }

    public function check($args = array()) {
        $tables = sct_get_list_of_tables(true);

       $overhead = 0;
        foreach ($tables as $t) {
            if ($t->Engine != 'InnoDB') {
                $overhead+= $t->Data_free;
            }
        }

        $this->check = array(
            'display' => __("Overhead", "smart-cleanup-tools").': <strong>'.sct_size_format($overhead).'</strong>',
            'checked' => $overhead,
            'records' => 1,
            'size' => 0
        );

        return $this->check;
    }
}

class sct_cleanup_network_usermeta_empty extends sct_cleanup {
    public function form($args = array()) {
        return __("This will remove all records from usermeta table that are empty.", "smart-cleanup-tools");
    }

    public function run($args = array(), $stats = array()) {
        $defaults = array();
        $args = wp_parse_args($args, $defaults);

        global $wpdb;

        $sql = sprintf("DELETE FROM %s WHERE `meta_value` = ''", $wpdb->usermeta);

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

        $sql = sprintf("SELECT COUNT(*) as records, SUM(LENGTH(meta_key) + LENGTH(umeta_id) + LENGTH(user_id)) as size FROM %s WHERE `meta_value` = ''", $wpdb->usermeta);
        $data = $wpdb->get_row($sql);
        sct_log('sql_check', 'site', $sql, get_class($this), 'log');

        $this->check = array(
            'display' => __("Found", "smart-cleanup-tools").': <strong>'.$data->records.'</strong> | '.
                         __("Estimated size", "smart-cleanup-tools").': <strong>'.sct_size_format($data->size).'</strong>',
            'checked' => $data->records > 64,
            'records' => $data->records,
            'size' => $data->size
        );

        return $this->check;
    }
}

class sct_cleanup_network_expired_transient extends sct_cleanup {
    public function form($args = array()) {
        return __("Transient records are temporary data stored by WordPress and plugins. They have expiration data, and if they are missing, they will be regenerated on first use.", "smart-cleanup-tools");
    }

    public function run($args = array(), $stats = array()) {
        $defaults = array();
        $args = wp_parse_args($args, $defaults);

        global $wpdb;

        $sql = sprintf("SELECT meta_key FROM %ssitemeta WHERE meta_key LIKE '%s' AND meta_value < %s", $wpdb->base_prefix, "\_site\_transient\_timeout\_%", time());
        sct_log('sql_run', 'site', $sql, get_class($this), 'log');
        $raw = $wpdb->get_results($sql);

        $records = 0;
        foreach ($raw as $r) {
            if (substr($r->meta_key, 0, 24) == '_site_transient_timeout_') {
                $name = substr($r->meta_key, 24);
                delete_site_transient($name);
                $records++; $records++;
            }
        }

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

        $sql = sprintf("SELECT COUNT(*) FROM %ssitemeta WHERE meta_key LIKE '%s' AND meta_value < %s", $wpdb->base_prefix, "\_site\_transient\_timeout\_%", time());
        $records = $wpdb->get_var($sql) * 2;
        sct_log('sql_check', 'site', $sql, get_class($this), 'log');

        $this->check = array(
            'display' => __("Found", "smart-cleanup-tools").': <strong>'.$records.'</strong>',
            'checked' => $records > 0,
            'records' => $records,
            'size' => 0
        );

        return $this->check;
    }
}

?>