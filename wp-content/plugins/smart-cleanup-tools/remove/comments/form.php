<input class="sct-tool-active" type="hidden" value="yes" />
<h3 class="sct-remove-title"><?php _e("Comments", "smart-cleanup-tools"); ?></h3>
<p class="sct-remove-info">
    <?php _e("With this tool you can remove comments and various comments related data.", "smart-cleanup-tools"); ?>
</p>
<table class="form-table" style="width: 690px;">
    <tbody>
        <tr valign="top">
            <th scope="row"><?php _e("Remove Comments Data", "smart-cleanup-tools"); ?></th>
            <td>
                <fieldset>
                    <legend class="screen-reader-text"><span><?php _e("Remove Attachments", "smart-cleanup-tools"); ?></span></legend>
                    <select class="widefat sct-dropdown-single" name="remove[comments][remove]">
                        <option value="remove_pingbacks"><?php _e("Remove pingbacks", "smart-cleanup-tools"); ?></option>
                        <option value="clean_useragent"><?php _e("Remove user agent data", "smart-cleanup-tools"); ?></option>
                        <option value="clean_akismet"><?php _e("Remove Akismet data", "smart-cleanup-tools"); ?></option>
                        <option value="empty_commentmeta"><?php _e("Remove meta records with no value set", "smart-cleanup-tools"); ?></option>
                    </select>
                </fieldset>
            </td>
        </tr>
    </tbody>
</table>