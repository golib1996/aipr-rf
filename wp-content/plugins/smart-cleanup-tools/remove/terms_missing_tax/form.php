<?php

$_terms_missing_tax = new sct_removal_terms_missing_tax();
$taxonomies = $_terms_missing_tax->list_taxonomies();

?>
<h3 class="sct-remove-title"><?php _e("Missing Taxonomies: Terms", "smart-cleanup-tools"); ?></h3>
<p class="sct-remove-info">
    <?php _e("With this tool you can remove terms belonging to taxonomies that are no longer registered.", "smart-cleanup-tools"); ?>
</p>
<?php

if (empty($taxonomies)) {
    echo '<input class="sct-tool-active" type="hidden" value="no" />';
    _e("There are no terms found belonging to taxonomies that are no longer registered.", "smart-cleanup-tools");
} else {
    
?>

<input class="sct-tool-active" type="hidden" value="yes" />
<table class="form-table" style="width: 690px;">
    <tbody>
        <tr valign="top">
            <th scope="row"><?php _e("Taxonomies", "smart-cleanup-tools"); ?></th>
            <td>
                <fieldset>
                    <legend class="screen-reader-text"><span><?php _e("Taxonomies", "smart-cleanup-tools"); ?></span></legend>
                    <select class="widefat sct-dropdown-single" name="remove[terms_missing_tax][tax]">
                        <?php foreach ($taxonomies as $tax => $data) { ?>
                        <option value="<?php echo $tax; ?>"><?php echo $tax; ?> (<?php echo $data->terms.' '._n("term", "terms", $data->terms, "smart-cleanup-tools"); ?>)</option>
                        <?php } ?>
                    </select>
                </fieldset>
            </td>
        </tr>
    </tbody>
</table>

<?php } ?>