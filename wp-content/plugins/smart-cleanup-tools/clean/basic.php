<?php

if (!defined('ABSPATH')) exit;

abstract class sct_cleanup {
    public $scope;
    public $code;
    public $check;

    public $auto = true;

    function __construct($scope, $code) {
        $this->scope = $scope;
        $this->code = $code;
    }

    public function draw_checkbox_quick($value = 'on') {
        return '<input type="hidden" name="sct['.$this->scope.']['.$this->code.']" value="'.$value.'" />';
    }

    public function draw_checkbox($checked = false, $msg = '', $value = 'on') {
        $render = '<label for="sct_'.$this->scope.'_'.$this->code.'">';
        $render.= '<input'.($checked ? ' checked' : '').' type="checkbox" name="sct['.$this->scope.']['.$this->code.']" value="'.$value.'" id="sct_'.$this->scope.'_'.$this->code.'" class="widefat sct-checkbox" />';

        if ($msg == '') {
            $render.= __("Active", "smart-cleanup-tools");
        } else {
            $render.= $msg;
        }

        $render.= '</label>';

        return $render;
    }

    public function draw_posttypes($selected = array()) {
        $post_types = get_post_types(array(), 'objects');

        $render = '<label for="sct_args_'.$this->scope.'_'.$this->code.'_cpt">'.__("Post Types", "smart-cleanup-tools").': ';
        $render.= '<select multiple="multiple" name="sct_args['.$this->scope.']['.$this->code.'][cpt][]" id="sct_args_'.$this->scope.'_'.$this->code.'_cpt" class="sct-dropdown">';

        foreach ($post_types as $cpt => $data) {
            $sel = in_array($cpt, $selected) ? ' selected="selected"' : '';
            $render.= '<option value="'.$cpt.'"'.$sel.'>'.$data->labels->name.'</option>';
        }

        $render.= '</select></label>';

        return $render;
    }

    public function has_results() {
        if (isset($this->check) && is_array($this->check)) {
            return $this->check['records'] > 0;
        } else {
            return false;
        }
    }

    public function form_quick($args = array()) {
        return '';
    }

    public function help($args = array()) {
        return false;
    }

    public function details($args = array()) {
        return false;
    }

    abstract function form($args = array());
    abstract function run($args = array(), $stats = array());
    abstract function check($args = array());
}

?>