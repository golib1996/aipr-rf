<?php

if (!defined('ABSPATH')) exit;

abstract class sct_removal {
    public $scope;
    public $code;

    function __construct() { }

    public function prepare_remove_header($args = array()) {
        $render = '<h3 style="margin-top: 0;">'.__("Data Removal Results", "smart-cleanup-tools").'</h3>';
        $render.= '<h4>'.$this->get_title($args).'</h4>';

        return $render;
    }

    public function prepare_remove_footer($args = array()) {
        return '';
    }

    public function prepare_check_header($args = array()) {
        $render = '<div class="sct-preview-removal-left">';
        $render.= '<form method="POST" id="sct-removal-action"><h3 style="margin-top: 0;">'.$this->get_title($args).'</h3>';
        $render.= wp_nonce_field('smart-cleanup-tools-removal', '_wpnonce', true, false);
        $render.= '<input type="hidden" name="remove[type]" value="'.$this->code.'" />';

        foreach ($args as $key => $value) {
            $render.= '<input type="hidden" name="remove[args]['.$key.']" value="'.$value.'" />';
        }

        return $render;
    }

    public function prepare_check_footer($args = array()) {
        $render = '<input'.($args['__active__'] == 0 ? ' disabled' : '').' id="sct-removal-action-run" class="button-primary" type="submit" value="'.__("Remove Data", "smart-cleanup-tools").'" />';
        $render.= '</form>';
        $render.= '</div><div class="sct-preview-removal-right">';
        $render.= '<h4 style="margin-top: 0;">'.__("Important", "smart-cleanup-tools").':</h4><ul><li>';

        $render.= join('</li><li>', $this->get_notice($args));

        $render.= '</li><li style="font-weight: bold;">'.__("Backup your data before proceeding, this operation is not reversable!", "smart-cleanup-tools").'</li>';
        $render.= '</ul></div>';

        return $render;
    }

    abstract function get_notice($args = array());
    abstract function get_title($args = array());
    abstract function check($args = array());
    abstract function remove($args = array());
}

?>