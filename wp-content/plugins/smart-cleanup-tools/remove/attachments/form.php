<?php

$_attachments = new sct_removal_attachments();
$mime_types = $_attachments->list_mime_types();

?>
<input class="sct-tool-active" type="hidden" value="yes" />
<h3 class="sct-remove-title"><?php _e("Attachments", "smart-cleanup-tools"); ?></h3>
<p class="sct-remove-info">
    <?php _e("With this tool you can remove attachments based on different criteria.", "smart-cleanup-tools"); ?>
</p>
<table class="form-table" style="width: 690px;">
    <tbody>
        <tr valign="top">
            <th scope="row"><?php _e("Remove Attachments", "smart-cleanup-tools"); ?></th>
            <td>
                <fieldset>
                    <legend class="screen-reader-text"><span><?php _e("Remove Attachments", "smart-cleanup-tools"); ?></span></legend>
                    <select class="widefat sct-dropdown-single" name="remove[attachments][remove]">
                        <option value="missing_parent"><?php _e("Missing parent posts", "smart-cleanup-tools"); ?></option>
                        <option value="missing_file"><?php _e("Missing file from disk", "smart-cleanup-tools"); ?></option>
                        <option value="unattached"><?php _e("Unattached", "smart-cleanup-tools"); ?></option>
                    </select>
                </fieldset>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e("MIME Types", "smart-cleanup-tools"); ?></th>
            <td>
                <fieldset>
                    <legend class="screen-reader-text"><span><?php _e("MIME Types", "smart-cleanup-tools"); ?></span></legend>
                    <select class="widefat sct-dropdown-single" name="remove[attachments][mime]">
                        <option value=""><?php _e("All", "smart-cleanup-tools"); ?></option>
                        <?php foreach ($mime_types as $data) { ?>
                        <option value="<?php echo $data->post_mime_type; ?>"><?php echo $data->post_mime_type; ?> (<?php echo $data->posts.' '._n("attachment", "attachments", $data->posts, "smart-cleanup-tools"); ?>)</option>
                        <?php } ?>
                    </select>
                </fieldset>
            </td>
        </tr>
    </tbody>
</table>