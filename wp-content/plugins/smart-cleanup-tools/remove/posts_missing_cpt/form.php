<?php

$_posts_missing_cpt = new sct_removal_posts_missing_cpt();
$post_types = $_posts_missing_cpt->list_post_types();

?>
<h3 class="sct-remove-title"><?php _e("Missing Post Types: Posts", "smart-cleanup-tools"); ?></h3>
<p class="sct-remove-info">
    <?php _e("With this tool you can remove posts belonging to post types that are no longer registered.", "smart-cleanup-tools"); ?>
</p>
<?php

if (empty($post_types)) {
    echo '<input class="sct-tool-active" type="hidden" value="no" />';
    _e("There are no posts found belonging to post types that are no longer registered.", "smart-cleanup-tools");
} else {
    
?>

<input class="sct-tool-active" type="hidden" value="yes" />
<table class="form-table" style="width: 690px;">
    <tbody>
        <tr valign="top">
            <th scope="row"><?php _e("Post Types", "smart-cleanup-tools"); ?></th>
            <td>
                <fieldset>
                    <legend class="screen-reader-text"><span><?php _e("Post Types", "smart-cleanup-tools"); ?></span></legend>
                    <select class="widefat sct-dropdown-single" name="remove[posts_missing_cpt][cpt]">
                        <?php foreach ($post_types as $cpt => $data) { ?>
                        <option value="<?php echo $cpt; ?>"><?php echo $cpt; ?> (<?php echo $data->posts.' '._n("post", "posts", $data->posts, "smart-cleanup-tools"); ?>)</option>
                        <?php } ?>
                    </select>
                </fieldset>
            </td>
        </tr>
    </tbody>
</table>

<?php } ?>