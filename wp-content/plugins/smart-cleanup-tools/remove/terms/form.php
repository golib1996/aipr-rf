<?php

$_terms = new sct_removal_terms();
$taxonomies = get_taxonomies(array(), 'objects');

?>
<input class="sct-tool-active" type="hidden" value="yes" />
<h3 class="sct-remove-title"><?php _e("Taxonomy Terms Filter", "smart-cleanup-tools"); ?></h3>
<p class="sct-remove-info">
    <?php _e("With this tool you can remove terms from any taxonomy based on different criteria.", "smart-cleanup-tools"); ?>
</p>
<table class="form-table" style="width: 690px;">
    <tbody>
        <tr valign="top">
            <th scope="row"><?php _e("Taxonomy", "smart-cleanup-tools"); ?></th>
            <td>
                <fieldset>
                    <legend class="screen-reader-text"><span><?php _e("Taxonomy", "smart-cleanup-tools"); ?></span></legend>
                    <select class="widefat sct-dropdown-single" name="remove[terms][tax]">
                        <?php foreach ($taxonomies as $tax => $data) { ?>
                        <option value="<?php echo $tax; ?>"><?php echo $data->labels->name; ?> (<?php echo $tax; ?>)</option>
                        <?php } ?>
                    </select>
                </fieldset>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e("Terms Scope", "smart-cleanup-tools"); ?></th>
            <td>
                <fieldset>
                    <legend class="screen-reader-text"><span><?php _e("Terms Scope", "smart-cleanup-tools"); ?></span></legend>
                    <select class="widefat sct-dropdown-single" name="remove[terms][scope]">
                        <option value="unassigned"><?php _e("Unassigned terms", "smart-cleanup-tools"); ?></option>
                        <option value="all"><?php _e("All available terms", "smart-cleanup-tools"); ?></option>
                    </select>
                </fieldset>
            </td>
        </tr>
    </tbody>
</table>