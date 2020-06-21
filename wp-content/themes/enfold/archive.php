<?php
if (!defined('ABSPATH')) {
    die();
}

global $avia_config, $more;

/*
	 * get_header is a basic wordpress function, used to retrieve the header.php file in your theme directory.
	 */
get_header();

$showheader = true;
if (avia_get_option('frontpage') && $blogpage_id = avia_get_option('blogpage')) {
    if (get_post_meta($blogpage_id, 'header', true) == 'no') $showheader = false;
}

if ($showheader) {
    echo avia_title(array('title' => avia_which_archive()));
}

do_action('ava_after_main_title');
$searchName = $_GET['search'];
?>

<div class='container_wrap container_wrap_first main_color <?php avia_layout_class('main'); ?>'>

    <div class='container template-blog'>
        <main class='content <?php avia_layout_class('content'); ?> units' <?php avia_markup_helper(array('context' => 'content', 'post_type' => 'post')); ?>>
        <?
        get_template_part('includes/loop', 'breadcrumbs');
        ?>
            <h1 class="">
                <? 
                single_cat_title();
                ?>
            </h1>
            <ul class="research-list research-list-page" id="researchList">
                <?php
                get_template_part('includes/loop', 'category');
                ?>
            </ul>
            <?
            if ($searchName) :
            ?>
                <form action="">
                    <input type="submit" value="Полный список" />
                </form>
            <?
            endif;
            ?>


            <form method="GET" style="display: grid; grid-template-columns: 1fr 200px;">
                <input type="search" style="margin-bottom: 0px;" name="search">
                <input type="submit" value="Поиск" />
            </form>

            <div style="overflow: autto">
                <table class="research-table">
                    <thead>
                        <tr>
                            <th>Маркетинговое исследование рынка
                                </td>
                            <th>Цена</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // $more = 0;
                        get_template_part('includes/loop', 'research');
                        // get_template_part( 'includes/loop', 'index' );
                        ?>
                    </tbody>
                </table>
            </div>

            <main>
    </div>
    <!--end container-->

</div><!-- close default .container_wrap element -->



<?php
get_footer();
