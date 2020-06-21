<?php
if (!defined('ABSPATH')) {
    exit;
}    // Exit if accessed directly

$post_id = get_the_ID();

// check if we got posts to display:
if (have_posts()) :

    while (have_posts()) : the_post();
?>

    <li>
        <?php
        $cat = get_the_category($post_id);
        print_r($cat);
        ?>
    </li>

    <?php
    endwhile;
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
