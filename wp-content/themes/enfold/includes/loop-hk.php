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
        $catParent = $cat;
        break;
    endwhile;
    $categoriesChilds = get_categories([
        'parent' => $catParent->cat_ID,
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
            $count = $cat['count'];
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

endif;
