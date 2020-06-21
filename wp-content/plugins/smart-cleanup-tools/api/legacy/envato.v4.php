<?php

/*
Name:    Smart Envato API: V4
Version: 5.0
Author:  Milan Petrovic
Email:   milan@gdragon.info
Website: https://www.dev4press.com/

== Copyright ==
Copyright 2008 - 2015 Milan Petrovic (email: milan@gdragon.info)
*/

if (!defined('ABSPATH')) exit;

require_once('envato.core.php');

$root = dirname(dirname(__FILE__));

require_once($root.'/envato.functions.php');
require_once($root.'/envato.data.php');

if (!class_exists('smart_envato_api_core_v4')) {
    final class smart_envato_api_core_v4 extends smart_envato_api_core {
        private static $instance;

        public $base = 'http://marketplace.envato.com/api/edge/';
        public $sets = array(
            'public' => array(
                'active-threads' => array('ttl' => 180, 'format' => '%set%:%site%'),
                'number-of-files' => array('ttl' => 3600, 'format' => '%set%:%site%'),
                'forum_posts' => array('ttl' => 180, 'format' => '%user%'),
                'releases' => array('ttl' => 300, 'format' => '%set%'),
                'thread-status' => array('ttl' => 180, 'format' => '%set%:%id%'),
                'total-users' => array('ttl' => 3600, 'format' => '%set%'),
                'total-items' => array('ttl' => 3600, 'format' => '%set%'),
                'item-prices' => array('ttl' => 3600, 'format' => '%set%:%id%'),
                'user' => array('ttl' => 180, 'format' => '%set%:%user%'),
                'user-items-by-site' => array('ttl' => 180, 'format' => '%set%:%user%'),
                'search' => array('ttl' => 180, 'format' => '%set%:%site%,%category%,%search%', 'normalize' => true),
                'popular' => array('ttl' => 3600, 'format' => '%set%:%site%'),
                'categories' => array('ttl' => 3600, 'format' => '%set%:%site%', 'cache' => 604800),
                'item' => array('ttl' => 3600, 'format' => '%set%:%id%'),
                'collection' => array('ttl' => 180, 'format' => '%set%:%id%', 'normalize' => true),
                'features' => array('ttl' => 1, 'format' => '%set%:%site%'),
                'new-files' => array('ttl' => 3600, 'format' => '%set%:%site%,%category%', 'normalize' => true),
                'new-files-from-user' => array('ttl' => 900, 'format' => '%set%:%user%,%site%'),
                'random-new-files' => array('ttl' => 600, 'format' => '%set%:%site%', 'normalize' => true),
                'user-badges' => array('ttl' => 3600, 'format' => '%set%:%user%')
            ),
            'private' => array(
                'vitals' => array('ttl' => 60, 'format' => '%user%/%api_key%/%set%'),
                'earnings-and-sales-by-month' => array('ttl' => 600, 'format' => '%user%/%api_key%/%set%'),
                'statement' => array('ttl' => 600, 'format' => '%user%/%api_key%/%set%'),
                'download-purchase' => array('ttl' => 180, 'format' => '%user%/%api_key%/%set%:%id%'),
                'account' => array('ttl' => 60, 'format' => '%user%/%api_key%/%set%'),
                'recent-sales' => array('ttl' => 300, 'format' => '%user%/%api_key%/%set%'),
                'verify-purchase' => array('ttl' => 180, 'format' => '%user%/%api_key%/%set%:%id%')
            )
        );

        public $version = 'v4';

        public static function instance() {
            if (!isset(self::$instance)) {
                self::$instance = new smart_envato_api_core_v4();
            }

            return self::$instance;
	}

        public function is_legacy() {
            return true;
        }
    }
}

if (!function_exists('smart_envato_load')) {
    function smart_envato_load() {
        return smart_envato_api_core_v4::instance();
    }
}

?>