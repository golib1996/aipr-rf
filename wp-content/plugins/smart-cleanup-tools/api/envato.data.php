<?php

/*
Name:    Smart Envato API: Data Objects
Version: 5.0
Author:  Milan Petrovic
Email:   milan@gdragon.info
Website: https://www.dev4press.com/

== Copyright ==
Copyright 2008 - 2015 Milan Petrovic (email: milan@gdragon.info)
*/

if (!class_exists('smart_object_sorting')) {
    class smart_object_sorting {
        var $properties;
        var $sorted;

        function  __construct($objects_array, $properties = array()) {
            if (count($properties) > 0) {
                $this->properties = $properties;
                usort($objects_array, array(&$this, 'array_compare'));
            }

            $this->sorted = $objects_array;
        }

        function array_compare($one, $two, $i = 0) {
            $column = $this->properties[$i]['property'];
            $order = $this->properties[$i]['order'];

            if ($one->$column == $two->$column) {
                if ($i < count($this->properties) - 1) {
                    $i++;

                    return $this->array_compare($one, $two, $i);
                } else {
                    return 0;
                }
            }

            if (strtolower($order) == 'asc') {
                return ($one->$column < $two->$column) ? -1 : 1;
            } else {
                return ($one->$column < $two->$column) ? 1 : -1;
            }
        }
    }
}

if (!class_exists('smart_envato_api_user')) {
    class smart_envato_api_user {
        private $_referrer = '';

        public $badges = null;

        public function __construct($user, $referrer = '', $badges = null) {
            foreach ($user as $key => $value) {
                $this->$key = $value;
            }

            $this->_referrer = $referrer;
            $this->badges = $badges;
        }

        public function url($market) {
            $author = 'http://'.strtolower($market).'.net/user/'.$this->username;

            return $this->add_referrer($author);
        }

        public function portfolio_url($market) {
            $author = 'http://'.strtolower($market).'.net/user/'.$this->username.'/portfolio';

            return $this->add_referrer($author);
        }

        private function add_referrer($url) {
            if ($this->_referrer != '') {
                return $url.'?ref='.$this->_referrer;
            } else {
                return $url;
            }
        }
    }
}

if (!class_exists('smart_envato_api_item')) {
    class smart_envato_api_item {
        private $_referrer = '';
        public $slug = '';

        public $uploaded_on = '';
        public $last_update = '';
        public $cost = '';
        public $sales = '';
        public $user = '';
        public $item = '';
        public $market = '';
        public $thumbnail = '';
        public $live_preview_url = '';
        public $video_preview_url = '';
        public $category = '';
        public $categories = '';

        public function __construct($item, $referrer = '', $core = null) {
            if (is_array($item) || is_object($item)) {
                foreach ($item as $key => $value) {
                    $this->$key = $value;
                }
            }

            if (is_object($this->rating)) {
                $this->rating_count = $this->rating->count;
                $this->rating = $this->rating->rating;
            }

            $this->cost = $this->price_cents / 100;
            $this->sales = $this->number_of_sales;
            $this->user = $this->author_username;
            $this->market = substr($this->site, 0, -4);
            $this->item = $this->name;
            $this->uploaded_on = $this->published_at;
            $this->last_update = $this->updated_at;

            if (isset($this->url)) {
                $path = parse_url($this->url, PHP_URL_PATH);
                $path_parts = explode('/', $path);
                $this->slug = $path_parts[2];
            }

            $this->category = $this->classification;
            if (isset($this->market) && isset($this->category) && !is_null($core)) {
                $this->categories = $core->data()->category_name($this->market, $this->category);
            }

            if (isset($this->previews->icon_preview)) {
                $this->thumbnail = $this->previews->icon_preview->icon_url;
            } else if (isset($this->previews->icon_with_thumbnail_preview)) {
                $this->thumbnail = $this->previews->icon_with_thumbnail_preview->icon_url;
            } else if (isset($this->previews->icon_with_audio_preview)) {
                $this->thumbnail = $this->previews->icon_with_audio_preview->icon_url;
            } else if (isset($this->previews->icon_with_landscape_preview)) {
                $this->thumbnail = $this->previews->icon_with_landscape_preview->icon_url;
            } else if (isset($this->previews->icon_with_video_preview)) {
                $this->thumbnail = $this->previews->icon_with_video_preview->icon_url;
            }

            if (isset($this->previews->landscape_preview)) {
                $this->live_preview_url = $this->previews->landscape_preview->landscape_url;
            } else if (isset($this->previews->icon_with_thumbnail_preview)) {
                $this->thumbnail = $this->previews->icon_with_thumbnail_preview->thumbnail_url;
            } else if (isset($this->previews->icon_with_landscape_preview)) {
                $this->live_preview_url = $this->previews->icon_with_landscape_preview->landscape_url;
            } else if (isset($this->previews->icon_with_video_preview)) {
                $this->live_preview_url = $this->previews->icon_with_video_preview->landscape_url;
            }

            if (isset($this->previews->icon_with_video_preview)) {
                $this->video_preview_url = $this->previews->icon_with_video_preview->video_url;
            }

            if (isset($this->live_preview_url)) {
                $this->preview = $this->live_preview_url;
            }

            $this->_referrer = $referrer;
        }

        public function url() {
            return $this->add_referrer($this->url);
        }

        public function author_url() {
            return $this->add_referrer($this->author_url);
        }

        public function author_portfolio_url() {
            return $this->add_referrer($this->author_url.'/portfolio');
        }

        public function preview_url() {
            if (isset($this->previews->live_site)) {
                $preview = 'http://'.$this->site.'/item/'.$this->slug.'/full_screen_preview/'.$this->id;

                return $this->add_referrer($preview);
            } else {
                return false;
            }
        }

        public function screenshots_url() {
            $preview = 'http://'.$this->site.'/item/'.$this->slug.'/screenshots/'.$this->id;

            return $this->add_referrer($preview);
        }

        private function add_referrer($url) {
            if ($this->_referrer != '') {
                return $url.'?ref='.$this->_referrer;
            } else {
                return $url;
            }
        }
    }
}
