<?php

$log_name = isset($_GET['log']) ? $_GET['log'] : 'run_report';
$log_path = smart_sct_core()->_get_log_file($log_name, $scope);

?>
<div class="sct-cleanup-left">
    <p>
        <?php _e("From here you can view various log files created by this plugin.", "smart-cleanup-tools"); ?><br/>
    </p>
    <h3><?php _e("Available Log Files", "smart-cleanup-tools"); ?></h3>
    <p>
        <?php

        foreach (smart_sct_core()->log_files as $log => $name) {
            echo '<a href="admin.php?page=smart-cleanup-tools-logs&log='.$log.'">'.$name.'</a><br/>';
        }

        ?>
    </p>

    <form method="POST" id="sct-cleanup-clearlogs">
        <h3><?php _e("Delete Log Files", "smart-cleanup-tools"); ?></h3>
        <?php settings_fields('smart-cleanup-tools-clearlogs'); ?>
        <input type="hidden" name="sct[scope]" value="<?php echo $scope; ?>" />

        <p style="border-bottom: 0; margin-bottom: 0;">
            <?php _e("Use this to delete contents from all 4 plugins log files.", "smart-cleanup-tools"); ?><br/>
        </p>

        <input id="sct-cleanup-clearlogs" class="button-primary" type="submit" value="<?php _e("Delete Logs", "smart-cleanup-tools"); ?>" />
    </form>
</div>
<div class="sct-cleanup-right sct-normal">
    <h3 style="margin-top: 0;"><?php _e("Log Status", "smart-cleanup-tools"); ?></h3>
    <table class="form-table">
        <tbody>
            <tr valign="top">
                <th scope="row"><?php _e("Location", "smart-cleanup-tools"); ?></th>
                <td>
                    <?php

                    echo smart_sct_core()->_get_log_root();

                    ?>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e("Path", "smart-cleanup-tools"); ?></th>
                <td>
                    <?php

                    if ($log_path === false) {
                        echo '<strong style="color: #cc0000;">'.__("Log location or file not writable. Unable to create log file.", "smart-cleanup-tools").'</strong>';
                    } else {
                        echo $log_path;
                    }

                    ?>
                </td>
            </tr>
        </tbody>
    </table>
    <h3><?php _e("Log Content", "smart-cleanup-tools"); ?></h3>
    <?php

    if ($log_path !== false) {
        echo '<div id="sct-log-content"><pre>'.file_get_contents($log_path).'</pre></div>';
    }

    ?>
</div>