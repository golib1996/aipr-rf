<?php
if (!defined('ABSPATH')) {
    exit;
}    // Exit if accessed directly


global $avia_config, $post_loop_count;

$post_loop_count = 1;
$post_class     = "post-entry-" . avia_get_the_id();


$arrPost = [];
$searchName = $_GET['search'];
if (have_posts()) :

    while (have_posts()) : the_post();
        $post_id = get_the_ID();
        $title = get_the_title($post_id);
        $normalizeStr = function ($str) {
            return  mb_strtolower($str);
        };

        $compareStr = function ($title, $searchName) {
            $isInSubStr = strpos($title, $searchName);
            if ($isInSubStr) return true;
            $words = explode (' ', $title);
            foreach ($words as $word) {
                $countOfWord = strlen($word);
                $countInSearch = 0;
                $letters = str_split($word);
                foreach ($letters as $letter) {
                    $searchLetters = str_split($searchName);
                    foreach ($searchLetters as $searchLetter) {
                        if ($searchLetter == $letter) {
                            $countInSearch += 1;
                        }
                    }
                }
                $percentage = $countInSearch / $countOfWord;
                if ($percentage  > 0.7) {
                    return true;
                }
            }
            return false;
        };

        if ($searchName && $compareStr($normalizeStr($title), $normalizeStr($searchName)) === false) continue;

        $arrPost[strtolower($title)] = [
            'link' => get_permalink($post_id),
            'name' => $title,
            'price' => get_post_meta($post_id, 'Price')[0],
        ];
        $post_loop_count++;
    endwhile;
    ksort($arrPost);
    foreach ($arrPost as $post) :
?>
        <tr>
            <td>
                <?php
                $link = $post['link'];
                $trimName = trim($post['name']);
                $name = mb_strtoupper(mb_substr($trimName, 0, 1)) . mb_substr($trimName, 1);
                echo "<a href='$link' class='research-title'>$name</a>";
                ?>
            </td>
            <td width="110px"> 
                <?php
                $price = $post['price'];
                echo "$price &#8381;";
                ?>
            </td>
            <td width="125px">
                <button class="button reserach-popup-btn">Купить</button>
            </td>
        </tr>
    <?php
    endforeach;
else :
    ?>

    <article class="entry">
        <!-- <header class="entry-content-header">
            <h1 class='post-title entry-title'><?php _e('Nothing Found', 'avia_framework'); ?></h1>
        </header> -->

        <?php get_template_part('includes/error404'); ?>

        <footer class="entry-footer"></footer>
    </article>

<?php

endif;
