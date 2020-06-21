<?php

/*
Name:    Smart Envato API: Storage - Site Transient
Version: 5.0
Author:  Milan Petrovic
Email:   milan@gdragon.info
Website: https://www.dev4press.com/

== Copyright ==
Copyright 2008 - 2015 Milan Petrovic (email: milan@gdragon.info)
*/

if (!defined('ABSPATH')) exit;

if (!class_exists('smart_envato_storage_site_transient')) {
    final class smart_envato_storage_site_transient extends smart_envato_api_storage {
        public function get($name) {
            return get_site_transient($name);
        }

        public function set($name, $value, $ttl = 0) {
            return set_site_transient($name, $value, $ttl);
        }

        public function delete($name) {
            return delete_site_transient($name);
        }

        public function clear($base) {
            global $wpdb;

            if (is_multisite()) {
                $sql = sprintf("DELETE FROM %ssitemeta WHERE meta_key LIKE '%s' OR meta_key LIKE '%s'", $wpdb->base_prefix, '_transient_'.$base.'%', '_transient_timeout_'.$base.'%');
            } else {
                $sql = sprintf("DELETE FROM %soptions WHERE option_name LIKE '%s' OR option_name LIKE '%s'", $wpdb->prefix, '_transient_'.$base.'%', '_transient_timeout_'.$base.'%');
            }

            $wpdb->query($sql);
        }
    }
}

?>