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
    // print_r($catParent);
    // die();
    $categoriesChilds = get_categories([
        'parent' => $catParent->term_id,
        'orderby'      => 'name', // сортировка по названию
        'order'        => 'ASC', // сортировка от меньшего к большему
        'hide_empty'   => 1, // скрыть пустые рубрики
    ]);

    $outputChild = [];
    foreach ($categoriesChilds as $catChild) {
        $childLink = get_category_link($catChild->term_id);
        $outputChild[$catChild->name] = [
            'link' => $childLink,
            'name' => $catChild->name,
            'count' => $catChild->category_count,
        ];
    }
    
    ksort($outputChild);
    if (count($outputChild) > 1) :
        foreach ($outputChild as $cat) :
            $link = $cat['link'];
            $name = $cat['name'];
            $count = $cat['count'] - 2;
            if ($count <= 0) continue;
?>
            <li class='research-list__item'>
                <?php
                $selectedClass = $name === $pageTilte ? 'research-list__link--selected' : '';
                echo "<a href='$link' class='research-list__link $selectedClass'>$name ($count)</a>";
                ?>
            </li>
    <?php
        endforeach;
    endif;
else :
    ?>

    <article class="entry">
        <header class="entry-content-header">
            <h1 class='post-title entry-title'><?php _e('Nothing Found', 'avia_framework'); ?></h1>
        </header>

        <?php get_template_part('includes/error404'); ?>

        <footer class="entry-footer"></footer>
    </article>

<?php

endif;
