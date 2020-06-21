<?php

/*
Name:    Smart Envato API: Functions
Version: 5.0
Author:  Milan Petrovic
Email:   milan@gdragon.info
Website: https://www.dev4press.com/

== Copyright ==
Copyright 2008 - 2015 Milan Petrovic (email: milan@gdragon.info)
*/

if (!function_exists('smart_remove_empty_from_array')) {
    function smart_remove_empty_from_array($start) {
        foreach ($start as $key => $value) {
            if (is_array($value)) {
                $start[$key] = smart_remove_empty_from_array($start[$key]);
            }

            if (empty($start[$key])) {
                unset($start[$key]);
            }
        }

        return $start;
    }
}

if (!function_exists('smart_hierarchy_to_flat')) {
    function smart_hierarchy_to_flat($start, $pad = 0, $prefix = '', $output = array()) {
        foreach ($start as $key => $value) {
            $code = $prefix.$key;

            $padding = '';

            if ($pad > 0) {
                $real_pad = $pad - 1;
                $padding = '- ';

                if ($real_pad > 0) {
                    $padding = str_repeat('--', $real_pad).$padding;
                }
            }

            $output[$code] = $padding.$value['name'];

            if (isset($value['values'])) {
                $output = array_merge($output, smart_hierarchy_to_flat($value['values'], $pad + 1, $code.'/', $output));
            }
        }

        return $output;
    }
}

if (!function_exists('smart_drill_category_name')) {
    function smart_drill_category_name($tree, $path, $name = array()) {
        $cat = $path[0];

        if (isset($tree[$cat])) {
            $name[] = $tree[$cat]['name'];

            if (count($path) > 1 && isset($tree[$cat]['values'])) {
                $new_path = $path;
                unset($new_path[0]);

                $new_path = array_values($new_path);

                $name = array_merge($name, smart_drill_category_name($tree[$cat]['values'], $new_path, array()));
            }
        }

        return $name;
    }
}

if (!function_exists('smart_envato_item')) {
    function smart_envato_item($item_id, $referrer = '', $ttl = 0) {
        return smart_envato_load()->data($ttl)->referrer($referrer)->item($item_id);
    }
}

if (!function_exists('smart_envato_user')) {
    function smart_envato_user($username, $referrer = '', $ttl = 0) {
        return smart_envato_load()->data($ttl)->referrer($referrer)->user($username);
    }
}

?>