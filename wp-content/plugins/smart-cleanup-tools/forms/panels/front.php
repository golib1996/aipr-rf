<?php require(SCT_PATH.'forms/shared/dimer.php'); ?>
<?php require(SCT_PATH.'forms/shared/status.php'); ?>

<div class="sct-front-wrapper">
    <div class="sct-front-title">
        <h1>SMART CLEANUP TOOLS</h1>
        <?php if (is_network_admin()) { ?>
        <h4><?php echo __("Network Mode", "smart-cleanup-tools"); ?></h4>
        <div style="clear: both"></div>
        <?php } ?>
        <h5><?php echo __("Version", "smart-cleanup-tools").': '.$settings['__version__']; ?></h5>
    </div>
    <div class="sct-front-links">
        <div><a href="admin.php?page=smart-cleanup-tools-cleanup"><?php _e("Cleanup", "smart-cleanup-tools"); ?></a></div>
        <div><a href="admin.php?page=smart-cleanup-tools-reset"><?php _e("Reset", "smart-cleanup-tools"); ?></a></div>
        <?php if (!is_network_admin()) { ?>
        <div><a href="admin.php?page=smart-cleanup-tools-removal"><?php _e("Removal", "smart-cleanup-tools"); ?></a></div>
        <?php } ?>
        <div><a href="admin.php?page=smart-cleanup-tools-scheduler"><?php _e("Scheduler", "smart-cleanup-tools"); ?></a></div>
        <div><a href="admin.php?page=smart-cleanup-tools-statistics"><?php _e("Statistics", "smart-cleanup-tools"); ?></a></div>
        <div><a href="admin.php?page=smart-cleanup-tools-logs"><?php _e("View Logs", "smart-cleanup-tools"); ?></a></div>
        <div><a href="admin.php?page=smart-cleanup-tools-settings"><?php _e("Settings", "smart-cleanup-tools"); ?></a></div>
        <div><a href="admin.php?page=smart-cleanup-tools-impexp"><?php _e("Export / Import", "smart-cleanup-tools"); ?></a></div>
        <div><a href="admin.php?page=smart-cleanup-tools-about"><?php _e("About", "smart-cleanup-tools"); ?></a></div>
    </div>
    <div class="sct-front-content" id="sct-quick-cleanup-results">
        <form method="POST" id="sct-cleanup-form-quick">
            <?php wp_nonce_field('smart-cleanup-tools-cleanup'); ?>

            <input type="hidden" name="sct[scope]" value="<?php echo $scope; ?>" />
            <input type="hidden" name="sct[quick]" value="on" />

            <div class="sct-front-quick">
                <div><?php _e("Quick Cleanup", "smart-cleanup-tools"); ?></div>
            </div>
            <div class="sct-front-quick-data">
                <?php echo ' '.__("Tools", "smart-cleanup-tools").': '.$summary['active'].' '; ?></div>
            <div class="sct-front-quick-data">
                <?php echo __("Records", "smart-cleanup-tools").': '.$summary['records'].' '; ?></div>
            <div class="sct-front-quick-data">
                <?php echo __("Size", "smart-cleanup-tools").': '.str_replace(' ', '', sct_size_format($summary['size'])); ?>
            </div>

            <?php echo join('', $results['quick']); ?>

            <input id="sct-cleanup-quick" class="button-primary" type="submit" value="<?php _e("Run Quick Cleanup", "smart-cleanup-tools"); ?>" />
        </form>
    </div>
</div>
<div style="clear: both"></div>

<script type="text/javascript">
jQuery(document).ready(function() {
    sct_admin.front();
    sct_admin.quick();
});
</script>