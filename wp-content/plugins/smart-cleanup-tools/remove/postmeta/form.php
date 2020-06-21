<input class="sct-tool-active" type="hidden" value="yes" />
<h3 class="sct-remove-title"><?php _e("Empty Postmeta records", "smart-cleanup-tools"); ?></h3>
<p class="sct-remove-info">
    <?php _e("This tool removes postmeta records based on selected criteria.", "smart-cleanup-tools"); ?>
</p>
<table class="form-table" style="width: 690px;">
    <tbody>
        <tr valign="top">
            <th scope="row"><?php _e("Remove Postmeta Data", "smart-cleanup-tools"); ?></th>
            <td>
                <fieldset>
                    <legend class="screen-reader-text"><span><?php _e("Remove Postmeta Data", "smart-cleanup-tools"); ?></span></legend>
                    <select class="widefat sct-dropdown-single" name="remove[postmeta][remove]">
                        <option value="empty"><?php _e("Remove records with no value set", "smart-cleanup-tools"); ?></option>
                    </select>
                </fieldset>
            </td>
        </tr>
    </tbody>
</table>
