<div class="sct-dim">
    <div id="sct-dim-outer">
        <div id="sct-dim-middle">
            <div id="sct-dim-inner">
                <?php if ($current == 'cleanup' || $current == 'reset') { ?>
                    <?php _e("Please wait for the cleanup to complete.", "smart-cleanup-tools"); ?><br/>
                    <em><?php _e("In some cases, process can take up to 15 minutes to complete.", "smart-cleanup-tools");
                        echo '<br/>';
                        _e("If it takes longer than that, refresh the page.", "smart-cleanup-tools"); ?></em>
                <?php } if ($current == 'remove') { ?>
                    <div class="dim-remove" id="dim-remove-removal" style="display: none;">
                        <?php _e("Please wait for the removal to complete.", "smart-cleanup-tools"); ?><br/>
                        <em><?php _e("This process can take a long time to complete, depending on the amount od data that needs to be removed.", "smart-cleanup-tools");
                            echo '<br/>';
                            _e("If it takes longer than 30 minutes, refresh the page, and start process again.", "smart-cleanup-tools"); ?></em>
                    </div>
                    <div class="dim-remove" id="dim-remove-analyze" style="display: none;">
                        <?php _e("Please wait to generate removal preview.", "smart-cleanup-tools"); ?><br/>
                    </div>
                <?php } ?>

                <div id="sct-load-timer">00:00:00.00</div>
            </div>
        </div>
    </div>
</div>