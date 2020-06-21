<?php

if (!defined('ABSPATH')) exit;

class sct_cleanup_site_woocommerce_options_sessions extends sct_cleanup {
    public function form($args = array()) {
        return __("Older WooCommerce versions used own modified transient records methods to store sessions data into options table. This tool will delete all these sessions.", "smart-cleanup-tools");
    }

    public function run($args = array(), $stats = array()) {
        $defaults = array();
        $args = wp_parse_args($args, $defaults);

        global $wpdb;

        $sql = sprintf("DELETE FROM %soptions WHERE option_name LIKE '%s' or option_name LIKE '%s'", $wpdb->prefix, '_wc_session_%', '_wc_session_expires_%');

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

        $sql = sprintf("SELECT COUNT(*) as records, SUM(LENGTH(option_name) + LENGTH(option_value) + LENGTH(autoload) + LENGTH(option_id)) as size FROM %soptions WHERE option_name LIKE '%s' or option_name LIKE '%s'", $wpdb->prefix, '_wc_session_%', '_wc_session_expires_%');
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
        $help.= '<li>'.__("This removes all sessions found in the 'wp_options' database tables.", "smart-cleanup-tools").'</li>';
        $help.= '<li>'.__("If you use older WooCommerce versions, this means that even the active shopping carts will be removed.", "smart-cleanup-tools").'</li>';
        $help.= '</ul>';

        return $help;
    }
}

class sct_cleanup_site_gravityforms_leads_trash extends sct_cleanup_gravityforms_leads {
    public $lead_status = 'trash';
}

class sct_cleanup_site_gravityforms_leads_spam extends sct_cleanup_gravityforms_leads {
    public $lead_status = 'spam';
}

class sct_cleanup_site_posts_auto_draft extends sct_cleanup_posts {
    public $post_status = 'auto-draft';

    public function help($args = array()) {
        $help = '<ul>';
        $help.= '<li>'.__("Auto drafts are made when creating new posts, before it is saved for the first time.", "smart-cleanup-tools").'</li>';
        $help.= '</ul>';

        return $help;
    }
}

class sct_cleanup_site_posts_trash extends sct_cleanup_posts {
    public $post_status = 'trash';
}

class sct_cleanup_site_posts_spam extends sct_cleanup_posts {
    public $post_status = 'spam';
}

class sct_cleanup_site_comments_trash extends sct_cleanup_comments {
    public $comment_status = 'trash';
}

class sct_cleanup_site_comments_spam extends sct_cleanup_comments {
    public $comment_status = 'spam';
}

class sct_cleanup_site_comments_unapproved extends sct_cleanup_comments {
    public $comment_status = '0';

    public function form($args = array()) {
        $return = __("This will remove all comments and commentmeta records that are not yet approved.", "smart-cleanup-tools");

        return $return;
    }
}

class sct_cleanup_site_posts_revisions extends sct_cleanup {
    public function form($args = array()) {
        $defaults = array('cpt' => array());
        $args = wp_parse_args($args, $defaults);
        $args['cpt'] = sct_post_types_filtered($args['cpt']);

        $return = '<div class="sct-tool-info-block">';
        $return.= __("This will remove revisions (and postmeta for them) for all published posts.", "smart-cleanup-tools");
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

        $sql = sprintf("DELETE p, m, t FROM %s p INNER JOIN %s r ON p.post_parent = r.ID LEFT JOIN %s t ON t.object_id = p.ID LEFT JOIN %s m ON m.post_id = p.ID WHERE p.post_type = 'revision' AND r.post_status in ('publish', 'closed')", $wpdb->posts, $wpdb->posts, $wpdb->term_relationships, $wpdb->postmeta);

        if (!empty($args['cpt'])) {
            $sql.= " AND r.post_type in ('".join("', '", $args['cpt'])."')";
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

        $sql = sprintf("SELECT COUNT(*) as records, SUM(LENGTH(p.post_content) + LENGTH(p.post_title) + LENGTH(p.post_excerpt) + LENGTH(p.post_name) + LENGTH(p.guid) + LENGTH(p.post_mime_type) + LENGTH(p.post_type) + LENGTH(p.post_status) + 160) as size FROM %s p INNER JOIN %s r ON p.post_parent = r.ID LEFT JOIN %s t ON t.object_id = p.ID LEFT JOIN %s m ON m.post_id = p.ID WHERE p.post_type = 'revision' AND r.post_status in ('publish', 'closed')", $wpdb->posts, $wpdb->posts, $wpdb->term_relationships, $wpdb->postmeta);
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

    public function details($args = array()) {
        if ($this->has_results()) {
            global $wpdb;

            $post_types = get_post_types(array(), 'objects');

            $sql = sprintf("SELECT r.post_type, COUNT(*) as records, SUM(LENGTH(p.post_content) + LENGTH(p.post_title)) as size FROM %s p INNER JOIN %s r ON p.post_parent = r.ID LEFT JOIN %s t ON t.object_id = p.ID LEFT JOIN %s m ON m.post_id = p.ID WHERE p.post_type = 'revision' AND r.post_status in ('publish', 'closed') GROUP BY r.post_type", $wpdb->posts, $wpdb->posts, $wpdb->term_relationships, $wpdb->postmeta);
            $data = $wpdb->get_results($sql);
            sct_log('sql_check', 'site', $sql, get_class($this), 'log');

            $display = array();

            if (!empty($data)) {
                $display['__header__'] = array(__("Post Type", "smart-cleanup-tools"), __("Records", "smart-cleanup-tools"), __("Size", "smart-cleanup-tools"));

                foreach ($data as $row) {
                    $display[$row->post_type] = array(
                        'label' => isset($post_types[$row->post_type]) ? $post_types[$row->post_type]->label : $row->post_type, 
                        'records' => $row->records, 
                        'size' => sct_size_format($row->size)
                    );
                }
            }

            return empty($display) ? false : $display;
        }

        return false;
    }
}

class sct_cleanup_site_orphaned_revisions extends sct_cleanup {
    public function form($args = array()) {
        $return = __("This will remove all revisions (and postmeta for them) that are not connected to any current posts. Orphaned revisions are common issue caused by deletion problems, or auto drafts saving.", "smart-cleanup-tools");
        return $return;
    }

    public function run($args = array(), $stats = array()) {
        $defaults = array();
        $args = wp_parse_args($args, $defaults);

        global $wpdb;

        $sql = sprintf("DELETE p, m, t FROM %s p LEFT JOIN %s t ON t.object_id = p.ID LEFT JOIN %s m ON m.post_id = p.ID WHERE p.post_type = 'revision' AND p.post_parent NOT IN (SELECT ID FROM (SELECT ID FROM %s) AS _tmp)", $wpdb->posts, $wpdb->term_relationships, $wpdb->postmeta, $wpdb->posts);

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

        $sql = sprintf("SELECT COUNT(*) as records, SUM(LENGTH(p.post_content) + LENGTH(p.post_title) + LENGTH(p.post_excerpt) + LENGTH(p.post_name) + LENGTH(p.guid) + LENGTH(p.post_mime_type) + LENGTH(p.post_type) + LENGTH(p.post_status) + 160) as size FROM %s p LEFT JOIN %s t ON t.object_id = p.ID LEFT JOIN %s m ON m.post_id = p.ID WHERE p.post_type = 'revision' AND p.post_parent NOT IN (SELECT ID FROM %s)", $wpdb->posts, $wpdb->term_relationships, $wpdb->postmeta, $wpdb->posts);
        $data = $wpdb->get_row($sql);
        sct_log('sql_check', 'site', $sql, get_class($this), 'log');

        $this->check = array(
            'display' => __("Found", "smart-cleanup-tools").': <strong>'.$data->records.'</strong> | '.
                         __("Estimated size", "smart-cleanup-tools").': <strong>'.sct_size_format($data->size).'</strong>',
            'checked' => $data->records > 32,
            'records' => $data->records,
            'size' => $data->size
        );

        return $this->check;
    }
}

class sct_cleanup_site_postmeta_orphans extends sct_cleanup {
    public function form($args = array()) {
        return __("This will remove all records from postmeta table not associated with existing posts.", "smart-cleanup-tools");
    }

    public function run($args = array(), $stats = array()) {
        $defaults = array();
        $args = wp_parse_args($args, $defaults);

        global $wpdb;

        $sql = sprintf("DELETE FROM %s WHERE post_id NOT IN (SELECT ID FROM %s)", $wpdb->postmeta, $wpdb->posts);

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

        $sql = sprintf("SELECT COUNT(*) as records, SUM(LENGTH(meta_key) + LENGTH(meta_value) + 16) as size FROM %s WHERE post_id NOT IN (SELECT ID FROM %s)", $wpdb->postmeta, $wpdb->posts);
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

class sct_cleanup_site_postmeta_oembeds extends sct_cleanup {
    public function form($args = array()) {
        return __("This will remove all oembed records from postmeta table. In some cases WordPress can save multiple copies of same oembed records.", "smart-cleanup-tools");
    }

    public function run($args = array(), $stats = array()) {
        $defaults = array();
        $args = wp_parse_args($args, $defaults);

        global $wpdb;

        $sql = sprintf("DELETE FROM %s WHERE meta_key LIKE '%s'", $wpdb->postmeta, "_oembed_%");

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

        $sql = sprintf("SELECT COUNT(*) as records, SUM(LENGTH(meta_key) + LENGTH(meta_value) + 16) as size FROM %s WHERE meta_key LIKE '%s'", $wpdb->postmeta, "_oembed_%");
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

class sct_cleanup_site_commentmeta_orphans extends sct_cleanup {
    public function form($args = array()) {
        return __("This will remove all records from commentmeta table not associated with existing comments.", "smart-cleanup-tools");
    }

    public function run($args = array(), $stats = array()) {
        $defaults = array();
        $args = wp_parse_args($args, $defaults);

        global $wpdb;

        $sql = sprintf("DELETE FROM %s WHERE `comment_id` NOT IN (SELECT comment_ID FROM %s)", $wpdb->commentmeta, $wpdb->comments);

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

        $sql = sprintf("SELECT COUNT(*) as records, SUM(LENGTH(meta_key) + LENGTH(meta_value) + 16) as size FROM %s WHERE `comment_id` NOT IN (SELECT comment_ID FROM %s)", $wpdb->commentmeta, $wpdb->comments);
        $data = $wpdb->get_row($sql);
        sct_log('sql_check', 'site', $sql, get_class($this), 'log');

        $this->check = array(
            'display' => __("Found", "smart-cleanup-tools").': <strong>'.$data->records.'</strong> | '.
                         __("Estimated size", "smart-cleanup-tools").': <strong>'.sct_size_format($data->size).'</strong>',
            'checked' => $data->records > 8,
            'records' => $data->records,
            'size' => $data->size
        );

        return $this->check;
    }
}

class sct_cleanup_site_comments_orphans extends sct_cleanup {
    public function form($args = array()) {
        return __("This will remove all records from comments table not associated with existing posts. Removes commentmeta records also.", "smart-cleanup-tools");
    }

    public function run($args = array(), $stats = array()) {
        $defaults = array();
        $args = wp_parse_args($args, $defaults);

        global $wpdb;

        $sql = sprintf("DELETE c, m FROM %s c LEFT JOIN %s m ON c.comment_ID = m.comment_id WHERE c.comment_post_ID NOT IN (SELECT ID FROM %s)", $wpdb->comments, $wpdb->commentmeta, $wpdb->posts);

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

        $sql = sprintf("SELECT COUNT(*) as records, SUM(LENGTH(c.comment_content) + LENGTH(c.comment_author) + LENGTH(c.comment_author_email) + LENGTH(c.comment_author_url) + LENGTH(c.comment_agent) + LENGTH(c.comment_author_IP) + 64) as size FROM %s c LEFT JOIN %s m ON c.comment_ID = m.comment_id WHERE c.comment_post_ID NOT IN (SELECT ID FROM %s)", $wpdb->comments, $wpdb->commentmeta, $wpdb->posts);
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

class sct_cleanup_site_usermeta_empty extends sct_cleanup {
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

class sct_cleanup_site_transient extends sct_cleanup {
    public function form($args = array()) {
        return __("Transient records are temporary data stored by WordPress and plugins. They have expiration data, and if they are missing, they will be regenerated on first use.", "smart-cleanup-tools");
    }

    public function run($args = array(), $stats = array()) {
        $defaults = array();
        $args = wp_parse_args($args, $defaults);

        global $wpdb;

        $sql = sprintf("DELETE FROM %soptions WHERE option_name LIKE '%s' or option_name LIKE '%s' or option_name LIKE '%s' or option_name LIKE '%s'", $wpdb->prefix, "_transient_%", "_site_transient_%", "_transient_timeout_%", "_site_transient_timeout_%");

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

        $sql = sprintf("SELECT COUNT(*) as records, SUM(LENGTH(option_name) + LENGTH(option_value) + LENGTH(autoload) + LENGTH(option_id)) as size FROM %soptions WHERE option_name LIKE '%s' or option_name LIKE '%s' or option_name LIKE '%s' or option_name LIKE '%s'", $wpdb->prefix, "_transient_%", "_site_transient_%", "_transient_timeout_%", "_site_transient_timeout_%");
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

class sct_cleanup_site_rss_cache extends sct_cleanup {
    public function form($args = array()) {
         return __("Various RSS feeds added to WordPress (dashboard, or from plugins) can take up a lot of space in the database, and if you don't use them, it is recommended to remove them.", "smart-cleanup-tools");
    }

    public function run($args = array(), $stats = array()) {
        $defaults = array();
        $args = wp_parse_args($args, $defaults);

        global $wpdb;

        $sql = sprintf("DELETE FROM %soptions WHERE option_name LIKE '%s' and LENGTH(option_name) IN (36, 39)", $wpdb->prefix, "rss_%");

        $wpdb->query($sql);
        sct_log('sql_run', 'site', $sql, get_class($this), 'log');
        $records = $wpdb->rows_affected;

        $sql = sprintf("DELETE FROM %soptions WHERE option_name LIKE '%s' or option_name LIKE '%s' or option_name LIKE '%s' or option_name LIKE '%s' or option_name LIKE '%s' or option_name LIKE '%s' or option_name LIKE '%s' or option_name LIKE '%s'", $wpdb->prefix, "_site_transient_feed_%", "_site_transient_rss_%", "_site_transient_timeout_feed_%", "_site_transient_timeout_rss_%", "_transient_feed_%", "_transient_rss_%", "_transient_timeout_feed_%", "_transient_timeout_rss_%");

        $wpdb->query($sql);
        sct_log('sql_run', 'site', $sql, get_class($this), 'log');
        $records+= $wpdb->rows_affected;

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

        $sql = sprintf("SELECT COUNT(*) as records, SUM(LENGTH(option_name) + LENGTH(option_value)) as size FROM %soptions WHERE option_name LIKE '%s' and LENGTH(option_name) IN (36, 39)", $wpdb->prefix, "rss_%");
        $data = $wpdb->get_row($sql);
        sct_log('sql_check', 'site', $sql, get_class($this), 'log');

        $sql = sprintf("SELECT COUNT(*) as records, SUM(LENGTH(option_name) + LENGTH(option_value)) as size FROM %soptions WHERE option_name LIKE '%s' or option_name LIKE '%s' or option_name LIKE '%s' or option_name LIKE '%s' or option_name LIKE '%s' or option_name LIKE '%s' or option_name LIKE '%s' or option_name LIKE '%s'", $wpdb->prefix, "_site_transient_feed_%", "_site_transient_rss_%", "_site_transient_timeout_feed_%", "_site_transient_timeout_rss_%", "_transient_feed_%", "_transient_rss_%", "_transient_timeout_feed_%", "_transient_timeout_rss_%");
        $data_2 = $wpdb->get_row($sql);
        sct_log('sql_check', 'site', $sql, get_class($this), 'log');

        $data->size+= $data_2->size;
        $data->records+= $data_2->records;

        $this->check = array(
            'display' => __("Found", "smart-cleanup-tools").': <strong>'.$data->records.'</strong> | '.
                         __("Estimated size", "smart-cleanup-tools").': <strong>'.sct_size_format($data->size).'</strong>',
            'checked' => false,
            'records' => $data->records,
            'size' => $data->size
        );

        return $this->check;
    }
}

class sct_cleanup_site_expired_transient extends sct_cleanup {
    public function form($args = array()) {
        return __("Transient records are temporary data stored by WordPress and plugins. They have expiration data, and if they are missing, they will be regenerated on first use.", "smart-cleanup-tools");
    }

    public function run($args = array(), $stats = array()) {
        $defaults = array();
        $args = wp_parse_args($args, $defaults);

        global $wpdb;

        $sql = sprintf("SELECT option_name FROM %soptions WHERE (option_name LIKE '%s' OR option_name LIKE '%s') AND option_value < %s", $wpdb->prefix, "_transient_timeout_%", "_site_transient_timeout_%", time());
        sct_log('sql_run', 'site', $sql, get_class($this), 'log');
        $raw = $wpdb->get_results($sql);

        $records = 0;
        foreach ($raw as $r) {
            if (substr($r->option_name, 0, 19) == '_transient_timeout_') {
                $name = substr($r->option_name, 19);
                delete_transient($name);
                $records++; $records++;
            } else if (substr($r->option_name, 0, 24) == '_site_transient_timeout_') {
                $name = substr($r->option_name, 24);
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

        $sql = sprintf("SELECT COUNT(*) FROM %soptions WHERE (option_name LIKE '%s' OR option_name LIKE '%s') AND option_value < %s", $wpdb->prefix, "_transient_timeout_%", "_site_transient_timeout_%", time());
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

class sct_cleanup_site_orphaned_relationships extends sct_cleanup {
    public function form($args = array()) {
        return __("Relationships table connecting posts and terms can have relationships between non existing posts and terms. This tool can find and remove all such orphaned relationship records.", "smart-cleanup-tools");
    }

    public function run($args = array(), $stats = array()) {
        $defaults = array();
        $args = wp_parse_args($args, $defaults);

        global $wpdb;

        $sql = sprintf("DELETE tr FROM %s tr INNER JOIN %s tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE tt.taxonomy != 'link_category' AND tr.object_id NOT IN (SELECT ID FROM %s);", $wpdb->term_relationships, $wpdb->term_taxonomy, $wpdb->posts);

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

        $sql = sprintf("SELECT count(*) as records, SUM(LENGTH(tt.taxonomy)) * 3 as size FROM %s tr INNER JOIN %s tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE tt.taxonomy != 'link_category' AND tr.object_id NOT IN (SELECT ID FROM %s);", $wpdb->term_relationships, $wpdb->term_taxonomy, $wpdb->posts);
        $data = $wpdb->get_row($sql);
        sct_log('sql_check', 'site', $sql, get_class($this), 'log');

        $this->check = array(
            'display' => __("Found", "smart-cleanup-tools").': <strong>'.$data->records.'</strong> | '.
                         __("Estimated size", "smart-cleanup-tools").': <strong>'.sct_size_format($data->size).'</strong>',
            'checked' => $data->records > 8,
            'records' => $data->records,
            'size' => $data->size
        );

        return $this->check;
    }
}

class sct_cleanup_site_orphaned_terms extends sct_cleanup {
    public function form($args = array()) {
        return __("Terms table can contain terms that are not connected to any taxonomy.", "smart-cleanup-tools");
    }

    public function run($args = array(), $stats = array()) {
        $defaults = array();
        $args = wp_parse_args($args, $defaults);

        global $wpdb;

        $sql = sprintf("DELETE t FROM %s t WHERE t.term_id NOT IN (SELECT term_id FROM %s);", $wpdb->terms, $wpdb->term_taxonomy);

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

        $sql = sprintf("SELECT count(*) as records, SUM(LENGTH(t.name) + LENGTH(t.slug) + LENGTH(t.term_id)) as size FROM %s t WHERE t.term_id NOT IN (SELECT term_id FROM %s);", $wpdb->terms, $wpdb->term_taxonomy);
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
}

class sct_cleanup_site_db_overhead extends sct_cleanup {
    public function form($args = array()) {
         return __("Over time, data in the database due to constant update and deletion of records gets fragmented, creating overhead. Overhead can be removed and it can speed up database queries.", "smart-cleanup-tools");
    }

    public function run($args = array(), $stats = array()) {
        $defaults = array();
        $args = wp_parse_args($args, $defaults);

        global $wpdb;

        $tables = sct_get_list_of_tables();
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
        $tables = sct_get_list_of_tables();

       $overhead = 0;
        foreach ($tables as $t) {
            if ($t->Engine != 'InnoDB') {
                $overhead+= $t->Data_free;
            }
        }

        $this->check = array(
            'display' => __("Overhead", "smart-cleanup-tools").': <strong>'.sct_size_format($overhead).'</strong>',
            'checked' => true,
            'records' => 1,
            'size' => 0
        );

        return $this->check;
    }
}

?>