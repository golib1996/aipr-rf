<?php

if (!defined('ABSPATH')) exit;

final class sct_log {
    var $log_file;
    var $active = false;

    function __construct($log_path = '') {
        $this->log_file = $log_path;

        if ($this->log_file != '') {
            if (file_exists($this->log_file) && is_writable($this->log_file)) {
                $this->active = true;
            }
        }
    }

    private function prepare_array($info) {
        $wr = '';

        $len = 0;
        $keys = array_keys($info);
        foreach ($keys as $key) {
            if (strlen($key) > $len) {
                $len = strlen($key);
            }
        }

        foreach ($info as $name => $value) {
            $wr.= sct_log_fill_length($name, $len).' :: '.$value."\r\n";
        }

        return $wr;
    }

    public function truncate() {
        if ($this->active) {
            $f = fopen($this->log_file, 'w+');

            fclose($f);
        }
    }

    public function log($data, $title = '', $mode = 'a+') {
        if ($this->active) {
            $f = fopen($this->log_file, $mode);

            fwrite ($f, sprintf("[%s] : %s\r\n", date('Y-m-d h:i:s.u'), $title));
            fwrite ($f, "$data");
            fwrite ($f, "\r\n\r\n");

            fclose($f);
        }
    }

    public function array_log($data, $title = '', $mode = 'a+') {
        if ($this->active) {
            $info = $this->prepare_array($data);
            $f = fopen($this->log_file, $mode);

            fwrite ($f, sprintf("[%s] : %s\r\n", date('Y-m-d h:i:s.u'), $title));
            fwrite ($f, "$info");
            fwrite ($f, "\r\n\r\n");

            fclose($f);
        }
    }

    public function simple_log($data, $mode = 'a+') {
        if ($this->active) {
            $f = fopen($this->log_file, $mode);

            fwrite ($f, "$data");
            fwrite ($f, "\r\n\r\n");

            fclose($f);
        }
    }

    public function dump($data, $title = '', $mode = 'a+') {
        if ($this->active) {
            $obj = print_r($data, true);
            $f = fopen($this->log_file, $mode);

            fwrite ($f, sprintf("[%s] : %s\r\n", date('Y-m-d h:i:s.u'), $title));
            fwrite ($f, "$obj");
            fwrite ($f, "\r\n");

            fwrite ($f, "-------------------------------------------------------- \r\n");

            fclose($f);
        }
    }

    public function simple_dump($data, $mode = 'a+') {
        if ($this->active) {
            $obj = print_r($data, true);
            $f = fopen($this->log_file, $mode);

            fwrite ($f, "$obj");
            fwrite ($f, "\r\n");

            fclose($f);
        }
    }
}

function sct_log_fill_length($text, $len, $character = ' ', $before = false) {
    $count = strlen($text);
    $zeros = '';

    for ($i = 0; $i < $len - $count; $i++) {
        $zeros.= $character;
    }

    if ($before) {
        return $zeros.$text;
    } else {
        return $text.$zeros;
    }
}

?>