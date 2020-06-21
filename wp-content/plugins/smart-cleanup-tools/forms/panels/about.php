<?php

require_once(SCT_PATH.'api/envato.api.php');

$api = new smart_envato_api_build('codecanyon');
$files = $api->data(172800)
             ->token('QfA259GREsWrBteDsH7Ny8LBjQ8LdonF')
             ->referrer('GDragoN')
             ->new_files_from_user('GDragoN', 'codecanyon');

function sct_render_item_about($item) {
    $max_width = 5 * 24;
    $current_width = $item->rating * 24;
    $upladed_on = strtotime($item->uploaded_on);
    $last_update = strtotime($item->last_update);

    $render = '<div class="sct-about-item sct-about-item-'.$item->id.'">';
        $render.= '<a href="'.$item->url().'" target="_blank"><img class="sct-about-item-image" src="'.$item->thumbnail.'" title="'.esc_attr($item->item).'" /></a>';
        $render.= '<div class="sct-about-item-content">';
            $render.= '<h4 class="sct-about-item-title"><a href="'.$item->url().'" target="_blank">'.$item->item.'</a></h4>';
            $render.= '<div class="sct-about-item-meta">';
                $render.= '<div class="sct-about-stars" style="width:'.$max_width.'px"><div class="sct-about-stars sct-about-stars-on" style="width:'.$current_width.'px"></div></div>';

                $render.= '<div class="sct-about-item-meta-price">';
                    $render.= '<span class="sct-about-item-meta-cost">$'.$item->cost.'</span>';
                    $render.= '<span class="sct-about-item-meta-sales">'.$item->sales.' '._n("sale", "sales", $item->sales, "smart-cleanup-tools").'</span>';
                $render.= '</div>';
            $render.= '</div>';
        $render.= '</div>';
        $render.= '<div class="sct-about-item-extra">';
            $render.= __("uploaded on", "smart-cleanup-tools").': <strong>'.date(get_option('date_format'), $upladed_on).'</strong><br/>';
            $render.= __("last update", "smart-cleanup-tools").': <strong>'.date(get_option('date_format'), $last_update).'</strong>';
        $render.= '</div>';
    $render.= '</div>';

    return $render;
}

?>
<div class="sct-cleanup-left">
    <p>
        <?php _e("Smart Cleanup Tools is powerful and simple to use plugin for cleaning the database from old and unused records, transient cache and overhead. Supports multisite mode.", "smart-cleanup-tools"); ?>
    </p>
    <div class="sct-about-plugin">
    <h3><?php _e("About Plugin", "smart-cleanup-tools"); ?></h3>
    <?php
        echo __("Version", "smart-cleanup-tools").': <strong>'.$about['__version__'].'</strong><br/>';
        echo __("Date", "smart-cleanup-tools").': <strong>'.$about['__date__'].'</strong><br/>';
        echo __("Build", "smart-cleanup-tools").': <strong>'.$about['__build__'].'</strong>';
    ?>
    </div>

    <div class="sct-about-plugin" style="margin-top: 15px;">
        <h3><?php _e("Plugin on the Web", "smart-cleanup-tools"); ?></h3>
        <a href="http://www.smartplugins.info/plugin/wordpress/smart-cleanup-tools/" target="_blank">Homepage on SMARTPlugins</a><br/>
        <a href="http://forum.smartplugins.info/forums/forum/smart/smart-cleanup-tools/" target="_blank">Support on SMARTPlugins</a><br/>
        <a href="http://d4p.me/ccsct" target="_blank">Homepage on CodeCanyon</a>
    </div>
</div>
<div class="sct-cleanup-right" style="padding: 15px 15px 15px 5px">
    <div class="sct-about-author">
        <a href="http://codecanyon.net/user/GDragoN/portfolio?ref=GDragoN" targe="_blank"><img src="https://s3.amazonaws.com/smartplugins/misc/smart_icon_224x224.png" alt="SMART Plugins" /></a>
        <p>
            <a href="http://www.millan.rs/" target="_blank">SMART Plugins Home</a><br/>
            and on <a href="http://codecanyon.net/user/GDragoN/portfolio?ref=GDragoN" target="_blank">CodeCanyon</a>
        </p>
    </div>
    <?php if (!is_wp_error($files)) { ?>
        <div class="sct-about-plugins">
            <?php

            foreach ($files as $file) {
                echo sct_render_item_about($file);
            }

            ?>
        </div>
    <?php } ?>
</div>
