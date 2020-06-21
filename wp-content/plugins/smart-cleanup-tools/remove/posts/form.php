<?php

$post_types = get_post_types(array(), 'object');

?>
<h3 class="sct-remove-title"><?php _e("Posts", "smart-cleanup-tools"); ?></h3>
<p class="sct-remove-info">
    <?php _e("With this tool you can remove posts based on different criteria.", "smart-cleanup-tools"); ?>
</p>
<input class="sct-tool-active" type="hidden" value="yes" />
<table class="form-table" style="width: 690px;">
    <tbody>
        <tr valign="top">
            <th scope="row"><?php _e("Post Type", "smart-cleanup-tools"); ?></th>
            <td>
                <fieldset>
                    <legend class="screen-reader-text"><span><?php _e("Post Type", "smart-cleanup-tools"); ?></span></legend>
                    <select class="widefat sct-dropdown-single" name="remove[posts][cpt]">
                        <?php foreach ($post_types as $cpt => $data) { ?>
                        <option value="<?php echo $cpt; ?>"><?php echo $data->label; ?></option>
                        <?php } ?>
                    </select>
                </fieldset>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e("Remove", "smart-cleanup-tools"); ?></th>
            <td>
                <fieldset>
                    <legend class="screen-reader-text"><span><?php _e("Remove", "smart-cleanup-tools"); ?></span></legend>
                    <select class="widefat sct-dropdown-single" name="remove[posts][remove]">
                        <option value="draft"><?php _e("Drafts", "smart-cleanup-tools"); ?></option>
                    </select>
                </fieldset>
            </td>
        </tr>
    </tbody>
</table>
