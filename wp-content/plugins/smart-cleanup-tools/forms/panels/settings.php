<?php

function sct_draw_multiselect($values = array(), $selected = array(), $code = 'cleanup') {
    $render = '<select multiple="multiple" name="disable_tools['.$code.'][]" id="sct_tools_'.$code.'" class="sct-dropdown-disabled">';

    foreach ($values as $cpt => $data) {
        $sel = in_array($cpt, $selected) ? ' selected="selected"' : '';
        $render.= '<option value="'.$cpt.'"'.$sel.'>'.$data['label'].'</option>';
    }

    $render.= '</select>';

    return $render;
}

?>
<form method="POST" id="sct-cleanup-form">
    <?php settings_fields('smart-cleanup-tools'); ?>
    <input type="hidden" name="cleanup_scope" value="<?php echo $scope; ?>" />

    <div class="sct-cleanup-left">
        <div id="scs-scroll-sidebar">
            <p>
                <?php _e("Settings here can control tools allowed for use, cleanup logs and other things.", "smart-cleanup-tools"); ?>
            </p>
            <input class="button-primary" type="submit" value="<?php _e("Save Settings", "smart-cleanup-tools"); ?>" />
        </div>
    </div>
    <div class="sct-cleanup-right sct-normal">
        <h3 style='margin-top: 0;'><?php _e("Disable Tools", "smart-cleanup-tools"); ?></h3>
        <table class="form-table" style="width: 640px;">
            <tbody>
                <tr valign="top">
                    <th scope="row"><?php _e("Cleanup", "smart-cleanup-tools"); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e("Cleanup", "smart-cleanup-tools"); ?></span></legend>
                            <?php

                            if ($scope == 'site') {
                                $tools = smart_sct_core()->get_tools_site();
                                echo sct_draw_multiselect($tools, $settings['disabled']['site'], 'site');
                            } else {
                                $tools = smart_sct_core()->get_tools_network();
                                echo sct_draw_multiselect($tools, $settings['disabled']['network'], 'network');
                            }

                            ?>
                        </fieldset>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e("Reset", "smart-cleanup-tools"); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e("Reset", "smart-cleanup-tools"); ?></span></legend>
                            <?php

                            if ($scope == 'site') {
                                $tools = smart_sct_core()->get_tools_reset();
                                echo sct_draw_multiselect($tools, $settings['disabled']['reset'], 'reset');
                            } else {
                                $tools = smart_sct_core()->get_tools_netreset();
                                echo sct_draw_multiselect($tools, $settings['disabled']['netreset'], 'netreset');
                            }

                            ?>
                        </fieldset>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e("Remove", "smart-cleanup-tools"); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e("Remove", "smart-cleanup-tools"); ?></span></legend>
                            <?php

                            if (!isset($settings['disabled']['removal'])) {
                                $settings['disabled']['removal'] = array();
                            }

                            if ($scope == 'site') {
                                $tools = smart_sct_core()->get_removal_site();
                                echo sct_draw_multiselect($tools, $settings['disabled']['removal'], 'removal');
                            } else {
                                $tools = smart_sct_core()->get_removal_network();
                                echo sct_draw_multiselect($tools, $settings['disabled']['removal'], 'removal');
                            }

                            ?>
                        </fieldset>
                    </td>
                </tr>
            </tbody>
        </table>

        <h3><?php _e("Cleanup Summary", "smart-cleanup-tools"); ?></h3>
        <table class="form-table" style="width: 640px;">
            <tbody>
                <tr valign="top">
                    <th scope="row"><?php _e("Show Summary", "smart-cleanup-tools"); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e("Show Summary", "smart-cleanup-tools"); ?></span></legend>
                            <label for="cleanup_summary">
                                <input<?php echo $settings['cleanup_summary'] ? ' checked="checked"' : ''; ?> type="checkbox" value="1" id="cleanup_summary" name="cleanup_summary">
                                <?php _e("Active", "smart-cleanup-tools"); ?></label>
                        </fieldset>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e("Show Inactive Tools", "smart-cleanup-tools"); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e("Show Inactive Tools", "smart-cleanup-tools"); ?></span></legend>
                            <label for="cleanup_show_inactive">
                                <input<?php echo $settings['cleanup_show_inactive'] ? ' checked="checked"' : ''; ?> type="checkbox" value="1" id="cleanup_show_inactive" name="cleanup_show_inactive">
                                <?php _e("Active", "smart-cleanup-tools"); ?></label>
                        </fieldset>
                    </td>
                </tr>
            </tbody>
        </table>

        <h3><?php _e("Cleanup Logs", "smart-cleanup-tools"); ?></h3>
        <table class="form-table" style="width: 640px;">
            <tbody>
                <tr valign="top">
                    <th scope="row"><?php _e("Enable Log", "smart-cleanup-tools"); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e("Enable Log", "smart-cleanup-tools"); ?></span></legend>
                            <label for="log_enabled">
                                <input<?php echo $settings['log_enabled'] ? ' checked="checked"' : ''; ?> type="checkbox" value="1" id="log_enabled" name="log_enabled">
                                <?php _e("Active", "smart-cleanup-tools"); ?></label>
                        </fieldset>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e("What to Log", "smart-cleanup-tools"); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e("What to Log", "smart-cleanup-tools"); ?></span></legend>
                            <label for="log_run_report">
                                <input<?php echo $settings['log_run_report'] ? ' checked="checked"' : ''; ?> type="checkbox" value="1" id="log_run_report" name="log_run_report">
                                <?php _e("Reports for normal Cleanup Run", "smart-cleanup-tools"); ?></label>

                                <br/><em>
                                    <?php _e("After each run, report will contain all the information about tools used and the cleanup results.", "smart-cleanup-tools"); ?></em>
                            <br/><br/>
                            <label for="log_cron_report">
                                <input<?php echo $settings['log_cron_report'] ? ' checked="checked"' : ''; ?> type="checkbox" value="1" id="log_cron_report" name="log_cron_report">
                                <?php _e("Reports for scheduled Cleanup Jobs", "smart-cleanup-tools"); ?></label>

                                <br/><em>
                                    <?php _e("After each run, report will contain all the information about tools used and the cleanup results.", "smart-cleanup-tools"); ?></em>
                            <br/><br/>
                            <label for="log_sql_run">
                                <input<?php echo $settings['log_sql_run'] ? ' checked="checked"' : ''; ?> type="checkbox" value="1" id="log_sql_run" name="log_sql_run">
                                <?php _e("SQL Queries for Cleanup Run", "smart-cleanup-tools"); ?></label>

                                <br/><em>
                                    <?php _e("Do not use this option all the time, it can grow the log file really fast. Use for debugging purposes only.", "smart-cleanup-tools"); ?></em>
                            <br/><br/>
                            <label for="log_sql_check">
                                <input<?php echo $settings['log_sql_check'] ? ' checked="checked"' : ''; ?> type="checkbox" value="1" id="log_sql_check" name="log_sql_check">
                                <?php _e("SQL Queries for Cleanup Check", "smart-cleanup-tools"); ?></label>

                                <br/><em>
                                    <?php _e("Do not use this option all the time, it can grow the log file really fast. Use for debugging purposes only.", "smart-cleanup-tools"); ?></em>
                        </fieldset>
                    </td>
                </tr>
            </tbody>
        </table>

        <h3><?php _e("WordPress Integration", "smart-cleanup-tools"); ?></h3>
        <table class="form-table" style="width: 640px;">
            <tbody>
                <tr valign="top">
                    <th scope="row"><?php _e("Toolbar Menu", "smart-cleanup-tools"); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e("Toolbar Menu", "smart-cleanup-tools"); ?></span></legend>
                            <label for="toolbar_menu_active">
                                <input<?php echo $settings['toolbar_menu_active'] ? ' checked="checked"' : ''; ?> type="checkbox" value="1" id="toolbar_menu_active" name="toolbar_menu_active">
                                <?php _e("Active", "smart-cleanup-tools"); ?></label>
                        </fieldset>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</form>