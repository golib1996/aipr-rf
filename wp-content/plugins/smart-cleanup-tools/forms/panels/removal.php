<?php

foreach (smart_sct_core()->remove as $key => $data) {
    require_once(SCT_PATH.'remove/'.$key.'/load.php');
}

if (!isset($settings['disabled']['removal'])) {
    $settings['disabled']['removal'] = array();
}

$disabled = apply_filters('sct_disable_removal_tools_'.$scope, $settings['disabled']['removal']);

$list = array();

foreach (smart_sct_core()->remove as $key => $data) { 
    if (!in_array($key, $disabled)) {
        $list[] = '<option value="'.$key.'">'.$data['label'].'</option>';
    }
}

?>
<div class="sct-cleanup-left">
    <p>
        <?php _e("Select what type of data you want to remove. Based on that, you will see available options on the right side.", "smart-cleanup-tools"); ?><br/>
    </p>
    <?php if (!empty($list)) { ?>
        <p style="border-bottom: none; margin-bottom: 0;">
            <label><?php _e("Removal Tool", "smart-cleanup-tools"); ?>:</label>
            <select class="widefat" id="sct-remove-type">
                <option value="nothing" selected="selected">&nbsp;</option>
                <?php echo join('', $list); ?>
            </select>
        </p>
        <input id="sct-remove-load" class="button-primary" type="button" value="<?php _e("Load", "smart-cleanup-tools"); ?>" />
        <p class="sct-basic-info">
            <?php _e("These tools will delete usable data from the database and files for attachments. Make sure you have database and files backup before proceeding. The responsibility of creating backup is up to you!", "smart-cleanup-tools"); ?>
        </p>
    <?php } ?>
</div>
<div class="sct-cleanup-right sct-normal">
    <?php if (empty($list)) { 
        echo '<h3 style="margin-top: 0;">'.__("All available removal tools are disabled.", "smart-cleanup-tools").'</h3>';
    } else { ?>
    <form method="POST" id="sct-removal-preview">
        <?php wp_nonce_field('smart-cleanup-tools-removal'); ?>
        <input type="hidden" id="remove-type" name="remove[type]" value="nothing" />

        <div id="remove-nothing" class="sct-remove-panel" style="display: block;">
            <h3 style='margin-top: 0;'><?php _e("Nothing Selected", "smart-cleanup-tools"); ?></h3>
            <p><?php _e("Load removal tool from the left to proceed", "smart-cleanup-tools"); ?></p>
        </div>

        <?php foreach (smart_sct_core()->remove as $key => $data) { if (!in_array($key, $disabled)) { ?>
            <div id="remove-<?php echo $key; ?>" class="sct-remove-panel" style="display: none;">
            <?php include(SCT_PATH.'remove/'.$key.'/form.php') ?>
            </div>
        <?php } } ?>

        <input style="display: none;" id="sct-removal-preview-run" class="button-primary" type="submit" value="<?php _e("Prepare Data For Removal", "smart-cleanup-tools"); ?>" />
    </form>

    <div id="sct-removal-block">
        
    </div>
    <?php } ?>
</div>