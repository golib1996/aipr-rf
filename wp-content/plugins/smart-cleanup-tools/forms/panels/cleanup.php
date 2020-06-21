<?php require(SCT_PATH.'forms/shared/status.php'); ?>
<form method="POST" id="sct-cleanup-form">
    <?php wp_nonce_field('smart-cleanup-tools-cleanup'); ?>

    <input type="hidden" name="sct[scope]" value="<?php echo $scope; ?>" />
    <input type="hidden" name="sct[quick]" value="off" />

    <div class="sct-cleanup-left">
        <p>
            <?php _e("Activate all the cleanup tools you want to use. If you need to, set up additional options some tools have.", "smart-cleanup-tools"); ?>
        </p>

        <?php if ($settings['cleanup_summary']) { ?>
        <div class="sct-cleanup-alls">
            <table style="width: 100%;">
                <tr><td style="text-align: left;"><?php _e("Active Tools", "smart-cleanup-tools"); ?></td>
                    <td style="text-align: right; font-weight: bold;"><?php echo $summary['active']; ?></td></tr>
                <tr><td style="text-align: left;"><?php _e("Records Found", "smart-cleanup-tools"); ?></td>
                    <td style="text-align: right; font-weight: bold;"><?php echo $summary['records']; ?></td></tr>
                <tr><td style="text-align: left;"><?php _e("Estimated Size", "smart-cleanup-tools"); ?></td>
                    <td style="text-align: right; font-weight: bold;"><?php echo sct_size_format($summary['size']); ?></td></tr>
            </table>
            <table style="width: 100%; border-top: 1px dotted #333;">
                <tr><td style="text-align: left;"><?php _e("Inactive Tools", "smart-cleanup-tools"); ?></td>
                    <td style="text-align: right; font-weight: bold;"><?php echo $summary['inactive']; ?></td></tr>
                <tr><td style="text-align: left;"><?php _e("Disabled Tools", "smart-cleanup-tools"); ?></td>
                    <td style="text-align: right; font-weight: bold;"><?php echo $summary['disabled']; ?></td></tr>
            </table>
        </div>
        <?php } ?>

        <div class="sct-cleanup-alls">
            <a class="sct-auto-enabler" href="#enable"><?php _e("enable all", "smart-cleanup-tools"); ?></a> | <a class="sct-auto-enabler" href="#disable"><?php _e("disable all", "smart-cleanup-tools"); ?></a>
        </div>
        <input id="sct-cleanup-run" class="button-primary" type="submit" value="<?php _e("Run Cleanup", "smart-cleanup-tools"); ?>" />
        <p class="sct-basic-info">
            <?php _e("These tools are considered safe to use, but to be extra safe you should backup your database first.", "smart-cleanup-tools"); ?>
        </p>
        <p class="sct-left-info sct-top-notice">
            <?php _e("Time needed to complete cleanup process depends on the database size, number of cleanup operations and amount of data that will get removed during this process.", "smart-cleanup-tools"); ?>
        </p>

        <div class="sct-side-banner">
            <a target="_blank" href="http://d4p.me/ccsoo" title="Smart Options Optimizer"><img src="https://s3.amazonaws.com/smartplugins/banners/250x125/smart-options-optimizer.png" alt="Smart Options Optimizer" /></a>
        </div>
    </div>
    <div class="sct-cleanup-right" style="padding: 15px 0 15px 5px">
        <?php

        echo join('', $results['active']);
        echo join('', $results['inactive']);

        if ($summary['usable'] == 0) {
            echo '<h3 style="margin-top: 0; margin-left: 15px;">'.__("All available cleanup tools are disabled.", "smart-cleanup-tools").'</h3>';
        }

        ?>
    </div>
</form>

<div style="display: none">
    <div id="sct-dialog-tool-details" title="">
        <div id="sct-details-content"></div>
    </div>
</div>