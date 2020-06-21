<?php

if (!defined('ABSPATH')) exit;

class sct_toolbar_menu {
    public function __construct() {
        add_action('admin_bar_menu', array(&$this, 'admin_bar_menu'), 100);

        if (SCT_WP_VERSION > 37) {
            add_action('admin_head', array(&$this, 'admin_bar_icon'));
            add_action('wp_head', array(&$this, 'admin_bar_icon'));
        } else {
            add_action('admin_head', array(&$this, 'admin_bar_image'));
            add_action('wp_head', array(&$this, 'admin_bar_image'));
        }
    }

    public function admin_bar_image() { ?>
        <style type="text/css">
            #wpadminbar .ab-top-menu > li.menupop.icon-sct-toolbar > .ab-item {
                background-image: url('<?php echo plugins_url('smart-cleanup-tools/gfx/menu.png'); ?>');
                background-repeat: no-repeat;
                background-position: 0.85em 50%;
                padding-left: 32px;
            }
        </style>
    <?php }

    public function admin_bar_icon() { ?>
        <style type="text/css">
            #wpadminbar #wp-admin-bar-sct-toolbar .ab-icon:before {
                content: "\f182";
                top: 1px;
            }

            @media screen and ( max-width: 782px ) {
                #wpadminbar li#wp-admin-bar-sct-toolbar {
                    display: block;
                }
            }
        </style>
    <?php }

    public function admin_bar_menu() {
        global $wp_admin_bar;

        if (SCT_WP_VERSION > 37) {
            $icon = '<span class="ab-icon"></span>';
            $title = $icon.'<span class="ab-label">'.__("Cleanup", "smart-cleanup-tools").'</span>';
        }

        $quick = 'admin.php?page=smart-cleanup-tools-front&_nonce='.wp_create_nonce('sct-toolbar-option').'&action=';

        $wp_admin_bar->add_menu(array(
            'id'     => 'sct-toolbar',
            'title'  => $title,
            'href'   => is_network_admin() ? 
                        network_admin_url('admin.php?page=smart-cleanup-tools-front') : 
                        admin_url('admin.php?page=smart-cleanup-tools-front'),
            'meta'   => array('class' => 'icon-sct-toolbar')
        ));

        $wp_admin_bar->add_group(array(
            'parent' => 'sct-toolbar',
            'id'     => 'sct-toolbar-public'
        ));
        $wp_admin_bar->add_menu(array(
            'parent' => 'sct-toolbar-public',
            'id'     => 'sct-toolbar-public-quick',
            'title'  => __("Quick Cleanup", "smart-cleanup-tools")
        ));
        $wp_admin_bar->add_group(array(
            'parent' => 'sct-toolbar-public-quick',
            'id'     => 'sct-toolbar-public-quick-links',
            'meta'   => array('class' => 'ab-sub-secondary')
        ));
        $wp_admin_bar->add_menu(array(
            'parent' => 'sct-toolbar-public-quick-links',
            'id'     => 'sct-toolbar-public-quick-links-quick',
            'title'  => __("Full Quick Cleanup", "smart-cleanup-tools"),
            'href'   => is_network_admin() ? 
                        network_admin_url('admin.php?page=smart-cleanup-tools-front') : 
                        admin_url('admin.php?page=smart-cleanup-tools-front')
        ));
        $wp_admin_bar->add_menu(array(
            'parent' => 'sct-toolbar-public-quick-links',
            'id'     => 'sct-toolbar-public-quick-links-transients',
            'title'  => __("Delete all Transients", "smart-cleanup-tools"),
            'href'   => is_network_admin() ? 
                        network_admin_url($quick.'transients') : 
                        admin_url($quick.'transients')
        ));

        if (!is_network_admin()) {
            $wp_admin_bar->add_menu(array(
                'parent' => 'sct-toolbar-public-quick-links',
                'id'     => 'sct-toolbar-public-quick-links-rewrite',
                'title'  => __("Reset Rewrite Rules", "smart-cleanup-tools"),
                'href'   => admin_url($quick.'rewrite')
            ));
        }
        
        $wp_admin_bar->add_menu(array(
            'parent' => 'sct-toolbar-public',
            'id'     => 'sct-toolbar-public-plugin',
            'title'  => __("Plugin Panels", "smart-cleanup-tools")
        ));
        $wp_admin_bar->add_group(array(
            'parent' => 'sct-toolbar-public-plugin',
            'id'     => 'sct-toolbar-public-plugin-links',
            'meta'   => array('class' => 'ab-sub-secondary')
        ));
        $wp_admin_bar->add_menu(array(
            'parent' => 'sct-toolbar-public-plugin-links',
            'id'     => 'sct-toolbar-public-plugin-links-intro',
            'title'  => __("Intro", "smart-cleanup-tools"),
            'href'   => is_network_admin() ? 
                        network_admin_url('admin.php?page=smart-cleanup-tools-front') : 
                        admin_url('admin.php?page=smart-cleanup-tools-front')
        ));
        $wp_admin_bar->add_menu(array(
            'parent' => 'sct-toolbar-public-plugin-links',
            'id'     => 'sct-toolbar-public-plugin-links-about',
            'title'  => __("About", "smart-cleanup-tools"),
            'href'   => is_network_admin() ? 
                        network_admin_url('admin.php?page=smart-cleanup-tools-about') : 
                        admin_url('admin.php?page=smart-cleanup-tools-about')
        ));
        $wp_admin_bar->add_menu(array(
            'parent' => 'sct-toolbar-public-plugin-links',
            'id'     => 'sct-toolbar-public-plugin-links-cleanup',
            'title'  => __("Cleanup Tools", "smart-cleanup-tools"),
            'href'   => is_network_admin() ? 
                        network_admin_url('admin.php?page=smart-cleanup-tools-cleanup') : 
                        admin_url('admin.php?page=smart-cleanup-tools-cleanup')
        ));
        $wp_admin_bar->add_menu(array(
            'parent' => 'sct-toolbar-public-plugin-links',
            'id'     => 'sct-toolbar-public-plugin-links-reset',
            'title'  => __("Reset Tools", "smart-cleanup-tools"),
            'href'   => is_network_admin() ? 
                        network_admin_url('admin.php?page=smart-cleanup-tools-reset') : 
                        admin_url('admin.php?page=smart-cleanup-tools-reset')
        ));

        if (!is_network_admin()) {
            $wp_admin_bar->add_menu(array(
                'parent' => 'sct-toolbar-public-plugin-links',
                'id'     => 'sct-toolbar-public-plugin-links-removal',
                'title'  => __("Removal Tools", "smart-cleanup-tools"),
                'href'   => is_network_admin() ? 
                            network_admin_url('admin.php?page=smart-cleanup-tools-removal') : 
                            admin_url('admin.php?page=smart-cleanup-tools-removal')
            ));
        }

        $wp_admin_bar->add_menu(array(
            'parent' => 'sct-toolbar-public-plugin-links',
            'id'     => 'sct-toolbar-public-plugin-links-scheduler',
            'title'  => __("Scheduler", "smart-cleanup-tools"),
            'href'   => is_network_admin() ? 
                        network_admin_url('admin.php?page=smart-cleanup-tools-scheduler') : 
                        admin_url('admin.php?page=smart-cleanup-tools-scheduler')
        ));

        $wp_admin_bar->add_menu(array(
            'parent' => 'sct-toolbar-public-plugin-links',
            'id'     => 'sct-toolbar-public-plugin-links-statistics',
            'title'  => __("Statistics", "smart-cleanup-tools"),
            'href'   => is_network_admin() ? 
                        network_admin_url('admin.php?page=smart-cleanup-tools-statistics') : 
                        admin_url('admin.php?page=smart-cleanup-tools-statistics')
        ));
        $wp_admin_bar->add_menu(array(
            'parent' => 'sct-toolbar-public-plugin-links',
            'id'     => 'sct-toolbar-public-plugin-links-logs',
            'title'  => __("View Logs", "smart-cleanup-tools"),
            'href'   => is_network_admin() ? 
                        network_admin_url('admin.php?page=smart-cleanup-tools-logs') : 
                        admin_url('admin.php?page=smart-cleanup-tools-logs')
        ));
        $wp_admin_bar->add_menu(array(
            'parent' => 'sct-toolbar-public-plugin-links',
            'id'     => 'sct-toolbar-public-plugin-links-settings',
            'title'  => __("Settings", "smart-cleanup-tools"),
            'href'   => is_network_admin() ? 
                        network_admin_url('admin.php?page=smart-cleanup-tools-settings') : 
                        admin_url('admin.php?page=smart-cleanup-tools-settings')
        ));
        $wp_admin_bar->add_menu(array(
            'parent' => 'sct-toolbar-public-plugin-links',
            'id'     => 'sct-toolbar-public-plugin-links-impexp',
            'title'  => __("Export / Import", "smart-cleanup-tools"),
            'href'   => is_network_admin() ? 
                        network_admin_url('admin.php?page=smart-cleanup-tools-impexp') : 
                        admin_url('admin.php?page=smart-cleanup-tools-impexp')
        ));

        $wp_admin_bar->add_group(array(
            'parent' => 'sct-toolbar',
            'id'     => 'sct-toolbar-info',
            'meta'   => array('class' => 'ab-sub-secondary')
        ));
        $wp_admin_bar->add_menu(array(
            'parent' => 'sct-toolbar-info',
            'id'     => 'sct-toolbar-info-links',
            'title'  => __("Information", "smart-cleanup-tools")
        ));
        $wp_admin_bar->add_menu(array(
            'parent' => 'sct-toolbar-info',
            'id'     => 'sct-toolbar-info-more',
            'title'  => __("More Plugins", "smart-cleanup-tools"),
            'href'   => 'http://d4p.me/ccp',
            'meta'   => array('target' => '_blank')
        ));
        $wp_admin_bar->add_group(array(
            'parent' => 'sct-toolbar-info-links',
            'id'     => 'sct-toolbar-info-links-home',
            'meta'   => array('class' => 'ab-sub-secondary')
        ));
        $wp_admin_bar->add_group(array(
            'parent' => 'sct-toolbar-info-links',
            'id'     => 'sct-toolbar-info-links-support',
            'meta'   => array('class' => 'ab-sub-secondary')
        ));
        $wp_admin_bar->add_menu(array(
            'parent' => 'sct-toolbar-info-links-home',
            'id'     => 'sct-toolbar-bbp-home',
            'title'  => __("on CodeCanyon", "smart-cleanup-tools"),
            'href'   => 'http://d4p.me/ccsct',
            'meta'   => array('target' => '_blank')
        ));
        $wp_admin_bar->add_menu(array(
            'parent' => 'sct-toolbar-info-links-home',
            'id'     => 'sct-toolbar-d4p-home',
            'title'  => __("on SMARTPlugins", "smart-cleanup-tools"),
            'href'   => 'http://www.smartplugins.info/plugin/wordpress/smart-cleanup-tools/',
            'meta'   => array('target' => '_blank')
        ));
        $wp_admin_bar->add_menu(array(
            'parent' => 'sct-toolbar-info-links-support',
            'id'     => 'sct-toolbar-toolbox-home',
            'title'  => __("Support Forum", "smart-cleanup-tools"),
            'href'   => 'http://forum.smartplugins.info/forums/forum/smart/smart-cleanup-tools/',
            'meta'   => array('target' => '_blank')
        ));
    }
}

global $sct_core_toolbar;
$sct_core_toolbar = new sct_toolbar_menu();

?>