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

require_once('envato.functions.php');
require_once('envato.data.php');

if (!class_exists('smart_envato_api_build')) {
    class smart_envato_api_build {
        public $library_version = '5.0';

        private $call = array(
            'ttl' => 0,
            'cache' => true,
            'referrer' => '',
            'token' => ''
        );

        protected $timeout = 30;
        protected $base = 'https://api.envato.com/';

        private $token = '';
        private $market = 'codecanyon';
        private $username = '';
        private $referrer = '';

        private $errors = true;
        private $storage = false;
        private $cache = true;
        private $normalize = true;
        private $ua = '';

        public $log = '';

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

        public $marketplaces_search = array(
            'themeforest.net' => 'ThemeForest',
            'photodune.net' => 'PhotoDune',
            'codecanyon.net' => 'CodeCanyon',
            'videohive.net' => 'VideoHive',
            'audiojungle.net' => 'AudioJungle',
            'graphicriver.net' => 'GraphicRiver'
        );

        public $sets = array(
            'search/item' => array('api' => 'v1', 'node' => 'discovery/search', 'public' => true, 'ttl' => 7200, 'format' => '%set%'),
            'search/comment' => array('api' => 'v1', 'node' => 'discovery/search', 'public' => true, 'ttl' => 7200, 'format' => '%set%'),
            'search/more_like_this' => array('api' => 'v1', 'node' => 'discovery/search', 'public' => true, 'ttl' => 7200, 'format' => '%set%'),

            'active-threads' => array('api' => 'v1', 'node' => 'market', 'public' => true, 'ttl' => 1800, 'format' => '%set%:%site%.json'),
            'number-of-files' => array('api' => 'v1', 'node' => 'market', 'public' => true, 'format' => '%set%:%site%.json'),
            'forum_posts' => array('api' => 'v1', 'node' => 'market', 'public' => true, 'ttl' => 1800, 'format' => '%set%:%user%.json'),
            'thread-status' => array('api' => 'v1', 'node' => 'market', 'public' => true, 'ttl' => 1800, 'format' => '%set%:%thread%.json'),

            'total-users' => array('api' => 'v1', 'node' => 'market', 'public' => true, 'ttl' => 86400, 'format' => '%set%.json'),
            'total-items' => array('api' => 'v1', 'node' => 'market', 'public' => true, 'ttl' => 86400, 'format' => '%set%.json'),

            'item-prices' => array('api' => 'v1', 'node' => 'market', 'public' => true, 'ttl' => 86400, 'format' => '%set%:%id%.json'),

            'user' => array('api' => 'v1', 'node' => 'market', 'public' => true, 'ttl' => 86400, 'format' => '%set%:%user%.json'),
            'user-items-by-site' => array('api' => 'v1', 'node' => 'market', 'public' => true, 'ttl' => 86400, 'format' => '%set%:%user%.json'),
            'new-files-from-user' => array('api' => 'v1', 'node' => 'market', 'public' => true, 'format' => '%set%:%user%,%site%.json', 'normalize' => true),
            'user-badges' => array('api' => 'v1', 'node' => 'market', 'public' => true, 'ttl' => 86400, 'format' => '%set%:%user%.json'),

            'categories' => array('api' => 'v1', 'node' => 'market', 'public' => true, 'ttl' => 86400, 'format' => '%set%:%site%.json', 'cache' => 604800),
            'popular' => array('api' => 'v1', 'node' => 'market', 'public' => true, 'ttl' => 86400, 'format' => '%set%:%site%.json'),
            'features' => array('api' => 'v1', 'node' => 'market', 'public' => true, 'ttl' => 43200, 'format' => '%set%:%site%.json'),
            'new-files' => array('api' => 'v1', 'node' => 'market', 'public' => true, 'ttl' => 43200, 'format' => '%set%:%site%,%category%.json', 'normalize' => true),
            'random-new-files' => array('api' => 'v1', 'node' => 'market', 'public' => true, 'ttl' => 43200, 'format' => '%set%:%site%.json', 'normalize' => true),

            'account' => array('api' => 'v1', 'node' => 'market/private/user', 'public' => false, 'ttl' => 300, 'format' => '%set%.json'),
            'earnings-and-sales-by-month' => array('api' => 'v1', 'node' => 'market/private/user', 'public' => false, 'ttl' => 86400, 'format' => '%set%.json'),
            'statement' => array('api' => 'v1', 'node' => 'market/private/user', 'public' => false, 'ttl' => 60, 'format' => '%set%.json'),
            'download-purchase' => array('api' => 'v1', 'node' => 'market/private/user', 'public' => false, 'ttl' => 300, 'format' => '%set%:%code%.json'),
            'verify-purchase' => array('api' => 'v1', 'node' => 'market/private/user', 'public' => false, 'ttl' => 14400, 'format' => '%set%:%code%.json'),
            'username' => array('api' => 'v1', 'node' => 'market/private/user', 'public' => false, 'ttl' => 300, 'format' => '%set%.json'),
            'email' => array('api' => 'v1', 'node' => 'market/private/user', 'public' => false, 'ttl' => 300, 'format' => '%set%.json'),
            
            'author/sale' => array('api' => 'v3', 'node' => 'market', 'public' => true, 'ttl' => 300, 'format' => '%set%'),
            'author/sales' => array('api' => 'v3', 'node' => 'market', 'public' => true, 'ttl' => 300, 'format' => '%set%'),
            
            'buyer/list-purchases' => array('api' => 'v3', 'node' => 'market', 'public' => true, 'ttl' => 14400, 'format' => '%set%'),
            'buyer/download' => array('api' => 'v3', 'node' => 'market', 'public' => true, 'ttl' => 14400, 'format' => '%set%'),
            'buyer/purchase' => array('api' => 'v3', 'node' => 'market', 'public' => true, 'ttl' => 14400, 'format' => '%set%'),
            'buyer/purchases' => array('api' => 'v3', 'node' => 'market', 'public' => true, 'ttl' => 14400, 'format' => '%set%'),

            'catalog/collection' => array('api' => 'v3', 'node' => 'market', 'public' => true, 'ttl' => 7200, 'format' => '%set%'),
            'catalog/item' => array('api' => 'v3', 'node' => 'market', 'public' => true, 'ttl' => 7200, 'format' => '%set%'),
            'user/collection' => array('api' => 'v3', 'node' => 'market', 'public' => true, 'ttl' => 7200, 'format' => '%set%'),
            'user/collections' => array('api' => 'v3', 'node' => 'market', 'public' => true, 'ttl' => 7200, 'format' => '%set%')
        );

        public function __construct($market = '', $store = 'transient', $log_errors = true) {
            if ($market != '') {
                $this->market = $market;
            }

            if ($store != '') {
                if (is_multisite() && $store == 'transient') {
                    $store = 'site_transient';
                }

                require_once('storage/store.'.$store.'.php');

                $storage_class = 'smart_envato_storage_'.$store;
                $this->storage = new $storage_class();
            } else {
                $this->cache = false;
            }

            $this->ua = 'SmartEnvatoAPI WordPress Library '.$this->library_version;

            $this->errors = $log_errors;

            $upload = wp_upload_dir();
            $this->log = $upload['basedir'].'/envato/error.log';
        }

        public static function instance() {
            if (!isset(self::$instance)) {
                self::$instance = new smart_envato_api_build();
            }

            return self::$instance;
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

        public function set_token($token) {
            $this->token = $token;
        }

        public function set_timeout($timeout) {
            $this->timeout = $timeout;
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

        public function get_token() {
            return $this->token;
        }

        public function get_timeout() {
            return $this->timeout;
        }

        public function get_referrer() {
            return $this->referrer;
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

        public function is_legacy() {
            return false;
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

        public function token($token) {
            $this->call['token'] = $token;

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

        public function categories($market, $flat = true) {
            if (strpos($market, '.net') !== false) {
                $market = substr($market, 0, strlen($market) - 4);
            }

            $data = $this->api('categories', array(), $market);

            if (is_wp_error($data)) {
                return $data;
            }

            if ($flat) {
                return smart_hierarchy_to_flat($data);
            } else {
                return $data;
            }
        }

        public function all_categories($flat = true) {
            $list = array();

            foreach (array_keys($this->marketplaces) as $market) {
                $list[$market] = $this->categories($market, $flat);
            }

            return $list;
        }

        public function items($ids, $sort = '') {
            $items = array();

            foreach ($ids as $id) {
                $item = $this->item($id);

                if (!is_wp_error($item)) {
                    $items[] = $item;
                }
            }

            if ($sort != '') {
                return $this->_sort_items($items, $sort);
            } else {
                return $items;
            }
        }

        public function catalog_item($id) {
            return $this->item($id);
        }

        public function item($id) {
            $item = $this->api('catalog/item', array('id' => $id));
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

        public function item_prices($id) {
            return $this->api('item-prices', array('id' => $id));
        }

        public function catalog_collection($id) {
            return $this->collection($id);
        }

        public function collection($id, $sort = '') {
            $items = $this->api('catalog/collection', array('id' => $id));

            if (is_wp_error($items)) {
                return $items;
            }

            $items->items = $this->_data_items($items->items, $sort);

            return $items;
        }

        public function user($user, $badges = true) {
            $user_core = $this->api('user', array('user' => $user));

            if (is_wp_error($user_core)) {
                return $user_core;
            }

            $user_badges = $badges ? $this->api('user-badges', array('user' => $user)) : null;

            $referrer = empty($this->call['referrer']) ? $this->referrer : $this->call['referrer'];

            return new smart_envato_api_user($user_core, $referrer, $user_badges);
        }

        public function user_items_by_site($user) {
            return $this->api('user-items-by-site', array('user' => $user));
        }

        public function popular($market, $sort = '') {
            $items = $this->api('popular', array('site' => $market));

            if (is_wp_error($items)) {
                return $items;
            }

            return new smart_envato_obj(array(
                'items_last_week' => $this->_data_items($items->items_last_week, $sort, true),
                'items_last_three_months' => $this->_data_items($items->items_last_three_months, $sort, true)
            ));
        }

        public function features($market) {
            $items = $this->api('features', array('site' => $market));
            $referrer = empty($this->call['referrer']) ? $this->referrer : $this->call['referrer'];

            if (is_wp_error($items)) {
                return $items;
            }

            return new smart_envato_obj(array(
                'featured_file' => $this->item($items->featured_file->id),
                'featured_author' => new smart_envato_api_user($items->featured_author, $referrer),
                'free_file' => $this->item($items->free_file->id)
            ));
        }

        public function new_files($market, $category, $sort = '') {
            $category = str_replace('/', '%2f', $category);

            $items = $this->api('new-files', array('site' => $market, 'category' => $category));

            if (is_wp_error($items)) {
                return $items;
            }

            return $this->_data_items($items, $sort);
        }

        public function new_files_from_user($user, $market, $sort = '') {
            $items = $this->api('new-files-from-user', array('site' => $market, 'user' => $user));

            if (is_wp_error($items)) {
                return $items;
            }

            return $this->_data_items($items, $sort);
        }

        public function random_new_files($market, $sort = '') {
            $items = $this->api('random-new-files', array('site' => $market));

            if (is_wp_error($items)) {
                return $items;
            }

            return $this->_data_items($items, $sort);
        }

        public function account() {
            return $this->api('account');
        }

        public function vitals() {
            return $this->api('account');
        }

        public function statement() {
            return $this->api('statement');
        }

        public function recent_sales() {
            return $this->api('recent-sales');
        }

        public function earnings_and_sales_by_month() {
            return $this->api('earnings-and-sales-by-month');
        }

        public function download_purchase($purchase_id) {
            return $this->api('download-purchase', array('code' => $purchase_id));
        }

        public function verify_purchase($purchase_id) {
            return $this->api('verify-purchase', array('code' => $purchase_id));
        }

        public function search($term = '', $market = '', $category = '', $sort = '', $page_size = 50) {
            if (!empty($market) && strpos($market, '.') === false) {
                $market.= '.net';
            }

            $args = array(
                'page_size' => $page_size,
                'term' => $term,
                'site' => $market,
                'category' => str_replace('/', '%2f', $category)
            );

            $items = $this->api('search/item', $args);

            if (is_wp_error($items)) {
                return $items;
            }

            $items->matches = $this->_data_items($items->matches, $sort);

            return $items;
        }

        public function api($set, $args = array(), $market = '') {
            return $this->_data($set, $args, $market);
        }

        public function clear_cache() {
            if ($this->storage !== false) {
                $this->storage->clear($this->get_storage_key_base());
            }
        }

        public function get_storage_key_base() {
            return 'env50_key_';
        }

        public function get_set($set) {
            if (isset($this->sets[$set])) {
                return $this->sets[$set];
            } else {
                return null;
            }
        }

        public function get_ttl($set, $ttl) {
            $actual = 60;

            if (isset($this->sets[$set])) {
                if (isset($this->sets[$set]['cache'])) {
                    $actual = $this->sets[$set]['cache'];
                } else if (isset($this->sets[$set]['ttl'])) {
                    $actual = $this->sets[$set]['ttl'];
                } else {
                    $actual = DAY_IN_SECONDS;
                }
            }

            return $ttl < $actual ? $actual : $ttl;
        }

        public function url($set, $market = '', $args = array()) {
            if ($set != 'search/item') {
                $args['site'] = $market == '' ? $this->get_market() : $market;
            }

            $data = $this->get_set($set);

            $format = isset($data['format']) ? $data['format'] : '%set%';
            $node = isset($data['node']) ? $data['node'] : 'market';
            $version = isset($data['api']) ? $data['api'] : 'v3';

            $url = $this->base.$version.'/'.$node.'/'.$format;

            $args['set'] = $set;

            preg_match_all('(%.+?%)', $format, $matches, PREG_PATTERN_ORDER);

            $tags = array();
            if (!empty($matches[0])) {
                $tags = array_unique($matches[0]);

                foreach ($tags as $tag) {
                    $item = trim($tag, '%');
                    $value = isset($args[$item]) ? $args[$item] : '';
                    $url = str_replace($tag, $value, $url);
                }
            }

            if (substr($format, -4) != 'json') {
                foreach ($args as $key => $val) {
                    $tag = '%'.$key.'%';

                    if (!in_array($tag, $tags) && $key != 'set' && !empty($val)) {
                        $url = add_query_arg($key, $val, $url);
                    }
                }
            }

            return $url;
        }

        public function run($set, $market = '', $args = array(), $ttl = 0, $use_cache = true) {
            $url = $this->url($set, $market, $args);
            $name = $this->get_storage_key_base().md5($this->token.' => '.$url);

            $ttl = $this->get_ttl($set, $ttl);

            return $this->_fetch($url, $name, $set, $ttl, $use_cache);
        }

        private function _data($name, $args = array(), $market = '') {
            $raw = $this->run($name, $market, $args, $this->call['ttl'], $this->call['cache']);

            $name = str_replace('/', '-', $name);

            if (!is_wp_error($raw) && isset($raw->{$name})) {
                return $raw->{$name};
            }

            return $raw;
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

        private function _data_items($items, $sort = '', $fetch = false) {
            $list = array();

            $referrer = empty($this->call['referrer']) ? $this->referrer : $this->call['referrer'];

            foreach ($items as $item) {
                if ($fetch) {
                    $list[] = $this->item($item->id);
                } else {
                    $list[] = new smart_envato_api_item($item, $referrer, $this);
                }
            }

            if ($sort != '') {
                return $this->_sort_items($list, $sort);
            } else {
                return $list;
            }
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

        private function _load($url, $set, $ttl) {
            $output = '';

            $token = empty($this->call['token']) ? $this->token : $this->call['token'];

            $call = wp_remote_get($url, array(
                'user-agent' => $this->ua, 
                'httpversion' => '1.1',
                'timeout' => $this->timeout, 
                'headers' => array(
                    'Content-type' => 'application/json',
                    'Authorization' => 'bearer '.$token
                )
            ));

            if (is_wp_error($call)) {
                $output = $call;
            } else if ($call['response']['code'] != 200) {
                $error = json_decode($call['body']);

                $output = new WP_Error('request_error', $call['response']['code'].': '.$call['response']['message']);

                if (is_object($error) && isset($error->error)) {
                    $description = isset($error->error_description) ? $error->error_description : "Server Error";

                    $output->add($error->error, $description);
                }
            } else {
                $output = json_decode($call['body']);
 
                if ($this->normalize) {
                    $force = false;

                    $output = $this->_normalize_items($output, $set, $ttl, $force);
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

                    fwrite ($f, sprintf("[%s] : %s - [new api]\r\n", date('Y-m-d h:i:s'), $title));

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

        private function _normalize_items($output, $set, $ttl = 0, $force = false) {
            $normalize = false;

            if ($force) {
                $normalize = true;
            } else {
                if (isset($this->sets[$set])) {
                    if (isset($this->sets[$set]['normalize']) && $this->sets[$set]['normalize']) {
                        $normalize = true;
                    }
                }
            }

            if ($normalize) {
                $ttl = $this->get_ttl($set, $ttl);

                $result = new stdClass();
                $result->{$set} = array();

                foreach ($output->{$set} as $item) {
                    $url = $this->url('catalog/item', '', array('id' => intval($item->id)));
                    $new = $this->_fetch($url, '', 'catalog/item', $ttl, false);

                    if (!is_wp_error($new)) {
                        $result->{$set}[] = $new;
                    }
                }

                return $result;
            } else {
                return $output;
            }
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

            $output->categories = smart_remove_empty_from_array($list);

            return $output;
        }
    }
}

if (!function_exists('smart_envato_load')) {
    function smart_envato_load() {
        return smart_envato_api_build::instance();
    }
}

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
