<?php require(SCT_PATH.'forms/shared/dimer.php'); ?>
<div class="wrap">
    <h2 class="sct-panel-title">
        <?php echo $panel_title; ?>
        <a href="#" class="sct-quick-links-panel-toggle">
            <?php if (SCT_WP_VERSION > 37) { ?>
            <i class="dashicons dashicons-trash"></i> 
            <?php } ?>
            <?php _e("Smart Cleanup Tools", "smart-cleanup-tools");
            if (is_network_admin()) { echo ': '.__("Network Mode", "smart-cleanup-tools"); } ?>
            | <?php echo smart_sct_core()->get('__version__'); ?>
        </a>
    </h2>

    <div id="sct-quick-links-panel" style="display: none;">
        <a href="admin.php?page=smart-cleanup-tools-front"><?php _e("Intro", "smart-cleanup-tools"); ?></a>
        <a href="admin.php?page=smart-cleanup-tools-cleanup"><?php _e("Cleanup", "smart-cleanup-tools"); ?></a>
        <a href="admin.php?page=smart-cleanup-tools-reset"><?php _e("Reset", "smart-cleanup-tools"); ?></a>
        <?php if (!is_network_admin()) { ?>
        <a href="admin.php?page=smart-cleanup-tools-removal"><?php _e("Removal", "smart-cleanup-tools"); ?></a>
        <?php } ?>
        <a href="admin.php?page=smart-cleanup-tools-scheduler"><?php _e("Scheduler", "smart-cleanup-tools"); ?></a>
        <a href="admin.php?page=smart-cleanup-tools-statistics"><?php _e("Statistics", "smart-cleanup-tools"); ?></a>
        <a href="admin.php?page=smart-cleanup-tools-logs"><?php _e("View Logs", "smart-cleanup-tools"); ?></a>
        <a href="admin.php?page=smart-cleanup-tools-impexp"><?php _e("Export / Import", "smart-cleanup-tools"); ?></a>
        <a href="admin.php?page=smart-cleanup-tools-settings"><?php _e("Settings", "smart-cleanup-tools"); ?></a>
        <a href="admin.php?page=smart-cleanup-tools-about"><?php _e("About", "smart-cleanup-tools"); ?></a>
    </div>
    <?php if (isset($_GET['settings-updated'])) { ?>
        <div id="message" class="sct-message updated"><p><strong><?php _e("Settings saved.", "smart-cleanup-tools"); ?></strong></p></div>
    <?php } if (isset($_GET['import-failed'])) { ?>
        <div id="message" class="sct-message error"><p><strong><?php _e("File import failed.", "smart-cleanup-tools"); ?></strong></p></div>
    <?php } if (isset($_GET['import-nothing'])) { ?>
        <div id="message" class="sct-message error"><p><strong><?php _e("Nothing imported.", "smart-cleanup-tools"); ?></strong></p></div>
    <?php } if (isset($_GET['job-saved'])) { ?>
        <div id="message" class="sct-message updated"><p><strong><?php _e("Job saved successfully.", "smart-cleanup-tools"); ?></strong></p></div>
    <?php } if (isset($_GET['job-empty'])) { ?>
        <div id="message" class="sct-message error"><p><strong><?php _e("Job must have at least on tool selected.", "smart-cleanup-tools"); ?></strong></p></div>
    <?php } if (isset($_GET['logs-cleared'])) { ?>
        <div id="message" class="sct-message updated"><p><strong><?php _e("All logs are cleared.", "smart-cleanup-tools"); ?></strong></p></div>
    <?php } ?>

    <div id="ddw-panel" class="ddw-panel-<?php echo $current; ?>">