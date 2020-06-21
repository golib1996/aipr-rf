<?php

/*
Name:    Smart Envato API: Core
Version: 5.0
Author:  Milan Petrovic
Email:   milan@gdragon.info
Website: https://www.dev4press.com/

== Copyright ==
Copyright 2008 - 2015 Milan Petrovic (email: milan@gdragon.info)
*/

if (!defined('ABSPATH')) exit;

if (!class_exists('smart_envato_api_storage')) {
    abstract class smart_envato_api_storage {
        function __construct() {}

        abstract public function get($name);
        abstract public function set($name, $value, $ttl = 0);
        abstract public function delete($name);

        abstract public function clear($base);
    }
}

if (!class_exists('smart_envato_obj')) {
    class smart_envato_obj {
        function __construct($args = array()) {
            if (is_array($args) && !empty($args)) {
                $this->from_array($args);
            }
        }

        function __clone() {
            foreach($this as $key => $val) {
                if(is_object($val)||(is_array($val))){
                    $this->{$key} = unserialize(serialize($val));
                }
            }
        }
        
        public function to_array() {
            return (array)$this;
        }

        public function from_array($args) {
            foreach ($args as $key => $value) {
                $this->$key = $value;
            }
        }
    }
}

if (!class_exists('smart_envato_api_core')) {
    abstract class smart_envato_api_core {
        private $call = array(
            'ttl' => 0,
            'cache' => true,
            'referrer' => '',
            'username' => '', 
            'api_key' => ''
        );

        private $errors = true;
        private $storage = false;
        private $cache = true;
        private $normalize = true;
        private $ua = '';
        public $log = '';

        public $base = '';
        public $version = '';
        public $sets = array(
            'public' => array(),
            'private' => array()
        );

        private $market = 'codecanyon';
        private $username = '';
        private $api_key = '';
        private $referrer = '';

        public $marketplaces = array(
            'themeforest' => 'ThemeForest',
            'graphicriver' => 'GraphicRiver',
            'videohive' => 'VideoHive',
            'audiojungle' => 'AudioJungle',
            'codecanyon' => 'CodeCanyon',
            'activeden' => 'ActiveDen',
            '3docean' => '3DOcean',
            'photodune' => 'PhotoDune'
        );

        public $marketplaces_search;

        public function __construct($market = '', $store = 'transient', $log_errors = true) {
            $this->marketplaces_search = $this->marketplaces;

            if ($market != '') {
                $this->market = $market;
            }

            if ($store != '') {
                if (is_multisite() && $store == 'transient') {
                    $store = 'site_transient';
                }

                $root = dirname(dirname(__FILE__));

                require_once($root.'/storage/store.'.$store.'.php');

                $storage_class = 'smart_envato_storage_'.$store;
                $this->storage = new $storage_class();
            } else {
                $this->cache = false;
            }

            $this->ua = 'SmartEnvatoAPI WordPress Library 3.1, API Version '.$this->version;

            $this->errors = $log_errors;

            $upload = wp_upload_dir();
            $this->log = $upload['basedir'].'/envato/error.log';
        }

        public function get_log_file_contents() {
            $this->_log_init();

            if (file_exists($this->log)) {
                return file_get_contents($this->log);
            } else {
                if (!is_writable($this->log)) {
                    return new WP_Error('log_error', "Log file can't be created.");
                } else {
                    return new WP_Error('log_error', "Log file not found.");
                }
            }
        }

        public function data($ttl = 0, $use_cache = true) {
            $this->call['ttl'] = $ttl;
            $this->call['cache'] = $use_cache;

            return $this;
        }

        public function protect($username = '', $api_key = '') {
            $this->call['username'] = $username;
            $this->call['api_key'] = $api_key;

            return $this;
        }

        public function referrer($referrer) {
            $this->call['referrer'] = $referrer;

            return $this;
        }

        public function category_name($market, $category) {
            $full = $this->data()->categories($market, false);
            $cats = explode('/', $category);

            return smart_drill_category_name($full, $cats);
        }

        public function item($id) {
            $item = $this->_data_public('item', array('id' => $id));
            $referrer = empty($this->call['referrer']) ? $this->referrer : $this->call['referrer'];

            if (is_wp_error($item)) {
                return $item;
            }

            $obj = new smart_envato_api_item($item, $referrer, $this);

            if (!isset($obj->id)) {
                $obj->id = $id;
            }

            return $obj;
        }

        public function items($ids, $sort = '') {
            $items = array();

            foreach ($ids as $id) {
                $item = $this->_data_public('item', array('id' => $id));

                if (!is_wp_error($item)) {
                    $items[] = $item;
                }
            }

            return $this->_data_items($items, $sort);
        }

        public function item_prices($id) {
            return $this->_data_public('item-prices', array('id' => $id));
        }

        public function collection($id, $sort = '') {
            $items = $this->_data_public('collection', array('id' => $id));

            if (is_wp_error($items)) {
                return $items;
            }

            return $this->_data_items($items, $sort);
        }

        public function user($user) {
            $user = $this->_data_public('user', array('user' => $user));

            if (is_wp_error($user)) {
                return $user;
            }

            $referrer = empty($this->call['referrer']) ? $this->referrer : $this->call['referrer'];

            return new smart_envato_api_user($user, $referrer);
        }

        public function user_items_by_site($user) {
            return $this->_data_public('user-items-by-site', array('user' => $user));
        }

        public function popular($market, $sort = '') {
            $items = $this->_data_public('popular', array('site' => $market));

            if (is_wp_error($items)) {
                return $items;
            }

            return new smart_envato_obj(array(
                'items_last_week' => $this->_data_items($items->items_last_week, $sort),
                'items_last_three_months' => $this->_data_items($items->items_last_three_months, $sort)
            ));
        }

        public function features($market) {
            $items = $this->_data_public('features', array('site' => $market));
            $referrer = empty($this->call['referrer']) ? $this->referrer : $this->call['referrer'];

            if (is_wp_error($items)) {
                return $items;
            }

            return new smart_envato_obj(array(
                'featured_file' => new smart_envato_api_item($items->featured_file, $referrer, $this),
                'featured_author' => new smart_envato_api_user($items->featured_author, $referrer),
                'free_file' => new smart_envato_api_item($items->free_file, $referrer, $this)
            ));
        }

        public function all_categories($flat = true) {
            $list = array();

            foreach (array_keys($this->marketplaces) as $market) {
                $list[$market] = $this->categories($market, $flat);
            }

            return $list;
        }

        public function categories($market, $flat = true) {
            $data = $this->run_public('categories', $market, array('site' => $market), $this->call['ttl'], $this->call['cache']);

            if (is_wp_error($data)) {
                return $data;
            }

            if ($flat) {
                return smart_hierarchy_to_flat($data);
            } else {
                return $data;
            }
        }

        public function new_files($market, $category, $sort = '') {
            $category = str_replace('/', '%2f', $category);

            $items = $this->_data_public('new-files', array('site' => $market, 'category' => $category));

            if (is_wp_error($items)) {
                return $items;
            }

            return $this->_data_items($items, $sort);
        }

        public function new_files_from_user($user, $market, $sort = '') {
            $items = $this->_data_public('new-files-from-user', array('site' => $market, 'user' => $user));

            if (is_wp_error($items)) {
                return $items;
            }

            return $this->_data_items($items, $sort);
        }

        public function random_new_files($market, $sort = '') {
            $items = $this->_data_public('random-new-files', array('site' => $market));

            if (is_wp_error($items)) {
                return $items;
            }

            return $this->_data_items($items, $sort);
        }

        public function search($query, $market = '', $category = '', $sort = '') {
            $category = str_replace('/', '%2f', $category);

            $items = $this->run_public('search', $market, array('category' => $category, 'search' => $query), $this->call['ttl'], $this->call['cache']);

            if (is_wp_error($items)) {
                return $items;
            }

            return $this->_data_items($items->search, $sort);
        }

        public function vitals() {
            return $this->_data_private('vitals');
        }

        public function account() {
            return $this->_data_private('account');
        }

        public function statement() {
            return $this->_data_private('statement');
        }

        public function recent_sales() {
            return $this->_data_private('recent-sales');
        }

        public function earnings_and_sales_by_month() {
            return $this->_data_private('earnings-and-sales-by-month');
        }

        public function download_purchase($purchase_id) {
            return $this->_data_private('download-purchase', array('id' => $purchase_id));
        }

        public function verify_purchase($purchase_id) {
            return $this->_data_private('verify-purchase', array('id' => $purchase_id));
        }

        public function run_public($set, $market = '', $args = array(), $ttl = 0, $use_cache = true) {
            $url = $this->api_url_public($set, $market, $args);
            $name = $this->get_storage_key_base().md5($url);

            $ttl = $this->get_ttl('public', $set, $ttl);

            return $this->_fetch($url, $name, $set, $ttl, $use_cache);
        }

        public function run_private($set, $username = '', $api_key = '', $args = array(), $ttl = 0, $use_cache = true) {
            $url = $this->api_url_private($set, $username, $api_key, $args);
            $name = $this->get_storage_key_base().md5($url);

            $ttl = $this->get_ttl('private', $set, $ttl);

            return $this->_fetch($url, $name, $set, $ttl, $use_cache);
        }

        public function set_market($market) {
            $this->market = $market;
        }

        public function set_username($username, $referrer = false) {
            $this->username = $username;

            if ($referrer) {
                $this->referrer = $username;
            }
        }

        public function set_api_key($api_key) {
            $this->api_key = $api_key;
        }

        public function set_referrer($referrer) {
            $this->referrer = $referrer;
        }

        public function get_market() {
            return $this->market;
        }

        public function get_username() {
            return $this->username;
        }

        public function get_api_key() {
            return $this->api_key;
        }

        public function get_referrer() {
            return $this->referrer;
        }

        public function get_version() {
            return $this->version;
        }

        public function clear_cache() {
            if ($this->storage !== false) {
                $this->storage->clear($this->get_storage_key_base());
            }
        }

        public function enable_normalization() {
            $this->normalize = true;
        }

        public function disable_normalization() {
            $this->normalize = false;
        }

        public function enable_cache() {
            $this->cache = true;
        }

        public function disable_cache() {
            $this->cache = false;
        }

        public function get_storage_key_base() {
            return 'envl41_'.$this->version.'_';
        }

        public function get_set($set, $type = 'public') {
            if (isset($this->sets[$type][$set])) {
                return $this->sets[$type][$set];
            } else {
                return null;
            }
        }

        public function get_ttl($scope, $set, $ttl) {
            $actual = 0;

            if (isset($this->sets[$scope][$set])) {
                if (isset($this->sets[$scope][$set]['cache'])) {
                    $actual = $this->sets[$scope][$set]['cache'];
                } else {
                    $actual = $this->sets[$scope][$set]['ttl'];
                }
            }

            return $ttl < $actual ? $actual : $ttl;
        }

        public function api_url($set, $type, $args) {
            $data = $this->get_set($set, $type);
            $url = $this->base.$data['format'].'.json';

            $args['set'] = $set;

            preg_match_all('(%.+?%)', $data['format'], $matches, PREG_PATTERN_ORDER);

            if (!empty($matches[0])) {
                $tags = array_unique($matches[0]);

                foreach ($tags as $tag) {
                    $item = trim($tag, '%');
                    $value = isset($args[$item]) ? $args[$item] : '';
                    $url = str_replace($tag, $value, $url);
                }
            }

            return $url;
        }

        public function api_url_public($set, $market = '', $args = array()) {
            if ($set != 'search') {
                $args['site'] = $market == '' ? $this->get_market() : $market;
            } else {
                $args['site'] = $market;
            }

            return $this->api_url($set, 'public', $args);
        }

        public function api_url_private($set, $username = '', $api_key = '', $args = array()) {
            $args['user'] = $username == '' ? $this->get_username() : $username;
            $args['api_key'] = $api_key == '' ? $this->get_api_key() : $api_key;

            return $this->api_url($set, 'private', $args);
        }

        private function _to_cache($name, $value, $ttl = 0) {
            $cached = apply_filters('smart_envato_api_to_cache', false, $name, $value, $ttl);

            if (!$cached) {
                if ($this->storage !== false) {
                    return $this->storage->set($name, $value, $ttl);
                } else {
                    return false;
                }
            } else {
                return $cached;
            }
        }

        private function _from_cache($name) {
            $cached = apply_filters('smart_envato_api_from_cache', false, $name);

            if (!$cached) {
                if ($this->storage !== false) {
                    return $this->storage->get($name);
                } else {
                    return false;
                }
            } else {
                return $cached;
            }
        }

        private function _fetch($url, $name, $set, $ttl = 0, $use_cache = true) {
            if ($this->cache && $use_cache) {
                $data = $this->_from_cache($name);

                if ($data === false || $data === '') {
                    $data = $this->_load($url, $set, $ttl);

                    if (!is_wp_error($data)) {
                        $this->_to_cache($name, $data, $ttl);
                    }
                }
            } else {
                $data = $this->_load($url, $set, $ttl);
            }

            return $data;
        }

        private function _load($url, $set, $ttl) {
            $output = '';

            $call = wp_remote_request($url, array('user-agent' => $this->ua, 'method' => 'GET', 'httpversion' => '1.1', 'timeout' => 30));

            if (is_wp_error($call)) {
                $output = $call;
            } else if ($call['response']['code'] != 200) {
                $output = json_decode($call['body']);

                if (is_object($output) && isset($output->error)) {
                    $output = new WP_Error($output->code, $output->error);
                } else {
                    $output = new WP_Error('request_error', $call['response']['code'].': '.$call['response']['message']);
                }
            } else {
                $output = json_decode($call['body']);

                if ($this->normalize) {
                    $output = $this->_normalize_items($output, $set, $ttl);
                }

                if ($set == 'categories') {
                    $output = $this->_normalize_categories($output);
                }
            }

            if (is_wp_error($output)) {
                $this->_log($output->get_error_message(), $output->get_error_code(), $url);
            }

            return $output;
        }

        private function _log($message, $title = '', $url = '') {
            if ($this->errors) {
                $this->_log_init();

                if ($this->errors) {
                    $f = fopen($this->log, 'a+');

                    fwrite ($f, sprintf("[%s] : %s - [legacy api]\r\n", date('Y-m-d h:i:s'), $title));

                    $trace = array_reverse(debug_backtrace());
                    array_pop($trace);

                    $caller = array();
                    foreach ($trace as $call) {
                        $caller[] = isset($call['class']) ? "{$call['class']}->{$call['function']}" : $call['function'];
                    }

                    if ($url != '') {
                        fwrite ($f, sprintf("[request url]         : %s\r\n", $url));
                    }

                    fwrite ($f, sprintf("[debug trace]         : %s\r\n", join(', ', $caller)));

                    fwrite ($f, sprintf("[error message]       : %s\r\n", $message));
                    fwrite ($f, "\r\n\r\n");

                    fclose($f);
                }
            }
        }

        private function _log_init() {
            if (!file_exists($this->log)) {
                $folder = wp_mkdir_p(dirname($this->log));

                if ($folder) {
                    file_put_contents($this->log, '');
                } else {
                    $this->errors = false;
                }
            }
        }

        private function _data_items($items, $sort = '') {
            $list = array();

            $referrer = empty($this->call['referrer']) ? $this->referrer : $this->call['referrer'];

            foreach ($items as $item) {
                $list[] = new smart_envato_api_item($item, $referrer, $this);
            }

            if ($sort != '') {
                return $this->_sort_items($list, $sort);
            } else {
                return $list;
            }
        }

        private function _data_public($name, $args = array(), $market = '') {
            $raw = $this->run_public($name, $market, $args, $this->call['ttl'], $this->call['cache']);

            if (!is_wp_error($raw)) {
                return $raw->{$name};
            }

            return $raw;
        }

        private function _data_private($name, $args = array()) {
            $username = empty($this->call['username']) ? $this->username : $this->call['username'];
            $api_key = empty($this->call['api_key']) ? $this->api_key : $this->call['api_key'];

            $raw = $this->run_private($name, $username, $api_key, $args, $this->call['ttl'], $this->call['cache']);

            if (!is_wp_error($raw)) {
                return $raw->{$name};
            }

            return $raw;
        }

        private function _normalize_categories($output) {
            $list = array();

            foreach ($output->categories as $cat) {
                $path = explode('/', $cat->path);
                $name = $cat->name;

                if (count($path) == 1) {
                    $list[$path[0]] = array('name' => $name, 'values' => array());
                } else if (count($path) == 2) {
                    $list[$path[0]]['values'][$path[1]] = array('name' => $name, 'values' => array());
                } else if (count($path) == 3) {
                    $list[$path[0]]['values'][$path[1]]['values'][$path[2]] = array('name' => $name, 'values' => array());
                } else if (count($path) == 4) {
                    $list[$path[0]]['values'][$path[1]]['values'][$path[2]]['values'][$path[3]] = array('name' => $name, 'values' => array());
                }
            }

            $list = smart_remove_empty_from_array($list);

            return $list;
        }

        private function _normalize_items($output, $set, $ttl = 0) {
            $normalize = false;
            $scope = 'public';

            if (isset($this->sets['public'][$set])) {
                if (isset($this->sets['public'][$set]['normalize']) && $this->sets['public'][$set]['normalize']) {
                    $normalize = true;
                }
            } else if (isset($this->sets['private'][$set])) {
                $scope = 'private';

                if (isset($this->sets['private'][$set]['normalize']) && $this->sets['private'][$set]['normalize']) {
                    $normalize = true;
                }
            }

            if ($normalize) {
                $ttl = $this->get_ttl($scope, $set, $ttl);

                $result = new stdClass();
                $result->{$set} = array();

                foreach ($output->{$set} as $item) {
                    $url = $this->api_url_public('item', '', array('id' => $item->id));
                    $new = $this->_fetch($url, '', 'item', $ttl, false);

                    if (!is_wp_error($new)) {
                        $result->{$set}[] = $new->item;
                    }
                }

                return $result;
            } else {
                return $output;
            }
        }

        private function _normalize_search_results($results) {
            $return = array();

            foreach ($results as $item) {
                if ($item->type == 'item') {
                    unset($item->type);

                    if (isset($item->item_info) && !is_null($item->item_info)) {
                        foreach ($item->item_info as $key => $value) {
                            if (!isset($item->$key)) {
                                $item->$key = $value;
                            }
                        }

                        unset($item->item_info);
                    }

                    if (isset($item->description)) {
                        unset($item->description);
                    }
                }

                $return[] = $item;
            }

            return $return;
        }

        private function _sort_items($items, $sort = '') {
            $i = 0;

            foreach ($items as $item) {
                $item->date_new = isset($item->uploaded_on) ? strtotime($item->uploaded_on) : $i;
                $item->date_updated = isset($item->last_update) ? strtotime($item->last_update) : $i;

                $i++;
            }

            switch ($sort) {
                case 'random':
                    shuffle($items);
                    break;
                case 'new':
                    $_obj = new smart_object_sorting($items, array(array('property' => 'date_new', 'order' => 'desc')));
                    $items = $_obj->sorted;
                    break;
                case 'old':
                    $_obj = new smart_object_sorting($items, array(array('property' => 'date_new', 'order' => 'asc')));
                    $items = $_obj->sorted;
                    break;
                case 'update':
                    $_obj = new smart_object_sorting($items, array(array('property' => 'date_updated', 'order' => 'desc')));
                    $items = $_obj->sorted;
                    break;
            }

            return $items;
        }
    }
}

?>