<?php
if (!defined('ABSPATH')) {
    exit;
}    // Exit if accessed directly

$arrCat = [];
$pageTilte = single_cat_title("", false);
// check if we got posts to display:
if (have_posts()) :
    $catParent = null;
    while (have_posts()) : the_post();
        $post_id = get_the_ID();
        $cat = get_the_category($post_id)[0];
        if (!$cat) continue;
        while ($cat->parent) {
            $cat = get_term_by('id', $cat->parent, 'category');
        }
        $catParent = $cat;
        break;
    endwhile;
    $link = get_category_link($catParent->term_id);
    $name = $catParent->name;
    echo "<div class='research-page__breadcrumbs' style='margin-bottom: 40px'><a href=\"http://aipr-rf.ru/\" rel=\"tag\">Главная</a>  >  <a href=\"$link\"> $name</a> </div>"; 
?>
    <?php
else :
    ?>

<?php

endif;
