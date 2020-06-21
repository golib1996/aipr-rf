<?php

if (!defined('ABSPATH')) exit;

function stc_cron_runcleanup($args) {
    require_once(SCT_PATH.'core/worker.php');

    $worker = new sct_worker();
    $worker->cron_run($args);
}

function sct_is_plugin_active($plugin) {
    return in_array($plugin, (array)get_option('active_plugins', array())) || sct_is_plugin_active_for_network($plugin);
}

function sct_is_plugin_active_for_network($plugin) {
    if (!is_multisite()) {
        return false;
    }

    $plugins = get_site_option( 'active_sitewide_plugins');

    return isset($plugins[$plugin]);
}

function sct_size_format($size) {
    $size = intval($size);

    if (strlen($size) <= 9 && strlen($size) >= 7) {
        $size = number_format($size / 1048576, 1);
        return $size.' MB';
    } else if (strlen($size) >= 10) {
        $size = number_format($size / 1073741824, 1);
        return $size.' GB';
    } else if (strlen($size) <= 6 && strlen($size) >= 4) {
        $size = number_format($size / 1024, 1);
        return $size.' KB';
    } else {
        return $size.' B';
    }
}

function sct_get_database_size($network = false) {
    $tables = sct_get_list_of_affected_tables($network);

    $data = array('rows' => 0, 'size' => 0, 'overhead' => 0);

    foreach ($tables as $t) {
        $data['rows']+= intval($t->Rows);
        $data['size']+= intval($t->Data_length);
    }

    $tables = sct_get_list_of_tables($network);

    foreach ($tables as $t) {
        if ($t->Engine != 'InnoDB') {
            $data['overhead']+= intval($t->Data_free);
        }
    }
    
    return $data;
}

function sct_get_list_of_affected_tables($network = false) {
    global $wpdb;

    $prefix = $network ? $wpdb->base_prefix : $wpdb->prefix;

    $tables = array();

    if ($network) {
        foreach ($wpdb->ms_global_tables as $tpl) {
            $result = sct_get_table_status($prefix.$tpl);

            if (!is_null($result)) {
                $tables[] = $result;
            }
        }
    } else {
        $extra = array('rg_lead_detail', 'rg_lead', 'rg_lead_detail_long', 'rg_lead_meta', 'rg_lead_notes');

        foreach ($wpdb->tables as $tpl) {
            $tables[] = sct_get_table_status($prefix.$tpl);
        }

        foreach ($extra as $tpl) {
            $result = sct_get_table_status($prefix.$tpl);

            if (!is_null($result)) {
                $tables[] = $result;
            }
        }
    }

    $tables[] = sct_get_table_status($wpdb->usermeta);
    $tables[] = sct_get_table_status($wpdb->users);

    return $tables;
}

function sct_get_list_of_tables($network = false) {
    global $wpdb;

    $prefix = $network ? $wpdb->base_prefix : $wpdb->prefix;

    $sql = "SHOW TABLE STATUS LIKE '".str_replace("_", "\_", $prefix)."%'";
    $tables = $wpdb->get_results($sql);

    if ($wpdb->usermeta != $wpdb->prefix.'usermeta') {
        $tables[] = sct_get_table_status($wpdb->usermeta);
    }

    if ($wpdb->users != $wpdb->prefix.'users') {
        $tables[] = sct_get_table_status($wpdb->users);
    }

    return $tables;
}

function sct_get_table_status($tbl) {
    global $wpdb;
    $sql = "SHOW TABLE STATUS LIKE '".$tbl."'";
    return $wpdb->get_row($sql);
}

function sct_post_types_filtered($post_types) {
    $list = array();

    foreach ($post_types as $post_type) {
        if (post_type_exists($post_type)) {
            $list[] = $post_type;
        }
    }

    return $list;
}

function sct_local_to_server_timestamp($local) {
    $server = $local - get_option('gmt_offset') * 3600;
    return $server;
}

function sct_server_to_local_timestamp($server) {
    $local = $server + get_option('gmt_offset') * 3600;
    return $local;
}

function sct_get_scheduled_job($hook, $timestamp, $id) {
    $crons = _get_cron_array();

    return $crons[$timestamp][$hook][$id];
}

function sct_unschedule_job($hook, $timestamp, $id) {
    $crons = _get_cron_array();

    if (isset($crons[$timestamp][$hook][$id])) {
        unset($crons[$timestamp][$hook][$id]);

        if (empty($crons[$timestamp][$hook])) {
            unset($crons[$timestamp][$hook]);
        }

        if (empty($crons[$timestamp])) {
            unset($crons[$timestamp]);
        }

        _set_cron_array($crons);
    }
}

function sct_cache_flush($cache = true, $queries = true) {
    if ($cache) {
        wp_cache_flush();
    }

    if ($queries) {
        global $wpdb;

        if (is_array($wpdb->queries) && !empty($wpdb->queries)) {
            unset($wpdb->queries);
            $wpdb->queries = array();
        }
    }
}

?>