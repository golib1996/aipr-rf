<?php

if (!defined('ABSPATH')) exit;

class sct_admin_network {
    public $page_ids = array();

    function __construct() {
        add_action('network_admin_menu', array(&$this, 'network_admin_menu'));
    }

    public function network_admin_menu() {
        global $sct_core_admin;

        $icon = SCT_WP_VERSION > 37 ? 'dashicons-trash' : SCT_URL.'gfx/menu.png';

        $this->page_ids[] = add_menu_page(__("Smart Cleanup Tools", "smart-cleanup-tools"), __("Smart Cleanup", "smart-cleanup-tools"), 'activate_plugins', 'smart-cleanup-tools-front', array($sct_core_admin, 'menu_front'), $icon);
        $this->page_ids[] = add_submenu_page('smart-cleanup-tools-front', __("Smart Cleanup Tools", "smart-cleanup-tools").': '.__("Intro", "smart-cleanup-tools"), __("Intro", "smart-cleanup-tools"), 'activate_plugins', 'smart-cleanup-tools-front', array($sct_core_admin, 'menu_front'));
        $this->page_ids[] = add_submenu_page('smart-cleanup-tools-front', __("Smart Cleanup Tools", "smart-cleanup-tools").': '.__("About", "smart-cleanup-tools"), __("About", "smart-cleanup-tools"), 'activate_plugins', 'smart-cleanup-tools-about', array($sct_core_admin, 'menu_about'));
        $this->page_ids[] = add_submenu_page('smart-cleanup-tools-front', __("Smart Cleanup Tools", "smart-cleanup-tools").': '.__("Cleanup Tools", "smart-cleanup-tools"), __("Cleanup Tools", "smart-cleanup-tools"), 'activate_plugins', 'smart-cleanup-tools-cleanup', array($sct_core_admin, 'menu_cleanup'));
        $this->page_ids[] = add_submenu_page('smart-cleanup-tools-front', __("Smart Cleanup Tools", "smart-cleanup-tools").': '.__("Reset Tools", "smart-cleanup-tools"), __("Reset Tools", "smart-cleanup-tools"), 'activate_plugins', 'smart-cleanup-tools-reset', array($sct_core_admin, 'menu_reset'));
        // $this->page_ids[] = add_submenu_page('smart-cleanup-tools-front', __("Smart Cleanup Tools", "smart-cleanup-tools").': '.__("Removal Tools", "smart-cleanup-tools"), __("Removal Tools", "smart-cleanup-tools"), 'activate_plugins', 'smart-cleanup-tools-removal', array($sct_core_admin, 'menu_removal'));
        $this->page_ids[] = add_submenu_page('smart-cleanup-tools-front', __("Smart Cleanup Tools", "smart-cleanup-tools").': '.__("Scheduler", "smart-cleanup-tools"), __("Scheduler", "smart-cleanup-tools"), 'activate_plugins', 'smart-cleanup-tools-scheduler', array($sct_core_admin, 'menu_scheduler'));
        $this->page_ids[] = add_submenu_page('smart-cleanup-tools-front', __("Smart Cleanup Tools", "smart-cleanup-tools").': '.__("Statistics", "smart-cleanup-tools"), __("Statistics", "smart-cleanup-tools"), 'activate_plugins', 'smart-cleanup-tools-statistics', array($sct_core_admin, 'menu_statistics'));
        $this->page_ids[] = add_submenu_page('smart-cleanup-tools-front', __("Smart Cleanup Tools", "smart-cleanup-tools").': '.__("View Logs", "smart-cleanup-tools"), __("View Logs", "smart-cleanup-tools"), 'activate_plugins', 'smart-cleanup-tools-logs', array($sct_core_admin, 'menu_log'));
        $this->page_ids[] = add_submenu_page('smart-cleanup-tools-front', __("Smart Cleanup Tools", "smart-cleanup-tools").': '.__("Settings", "smart-cleanup-tools"), __("Settings", "smart-cleanup-tools"), 'activate_plugins', 'smart-cleanup-tools-settings', array($sct_core_admin, 'menu_settings'));
        $this->page_ids[] = add_submenu_page('smart-cleanup-tools-front', __("Smart Cleanup Tools", "smart-cleanup-tools").': '.__("Export / Import", "smart-cleanup-tools"), __("Export / Import", "smart-cleanup-tools"), 'activate_plugins', 'smart-cleanup-tools-impexp', array($sct_core_admin, 'menu_impexp'));

        if (SCT_WP_VERSION > 32) {
            foreach ($this->page_ids as $id) {
                add_action('load-'.$id, array($sct_core_admin, 'load_admin_page_shared'));
            }
        }
    }
}

global $sct_core_admin_network;
$sct_core_admin_network = new sct_admin_network();

?>