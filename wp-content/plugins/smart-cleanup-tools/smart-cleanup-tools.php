<?php

/*
Plugin Name: Smart Cleanup Tools
Plugin URI: http://www.smartplugins.info/plugin/wordpress/smart-cleanup-tools/
Description: Powerful and easy to use plugin for cleaning the database from old and unused records, transient cache and overhead. Supports multisite mode.
Version: 4.5
Author: Milan Petrovic
Author URI: http://www.dev4press.com/

== Copyright ==
Copyright 2008 - 2016 Milan Petrovic (email: milan@gdragon.info)
*/

define('SCT_WP_CRON', defined('DOING_CRON') && DOING_CRON);

if (is_admin() || 
    is_network_admin() || 
    SCT_WP_CRON) {
        require_once('load.php');
}

?>