<?php
if (!defined('ABSPATH')) {
    exit;
}    // Exit if accessed directly


global $avia_config, $post_loop_count;


if (empty($post_loop_count)) $post_loop_count = 1;
$blog_style = !empty($avia_config['blog_style']) ? $avia_config['blog_style'] : avia_get_option('blog_style', 'multi-big');
if (is_single()) $blog_style = avia_get_option('single_post_style', 'single-big');

$blog_global_style = avia_get_option('blog_global_style', ''); //alt: elegant-blog

$blog_disabled = (avia_get_option('disable_blog') == 'disable_blog') ? true : false;
if ($blog_disabled) {
    if (current_user_can('edit_posts')) {
        $msg =     '<strong>' . __('Admin notice for:') . "</strong><br>" .
            __('Blog Posts', 'avia_framework') . "<br><br>" .
            __('This element was disabled in your theme settings. You can activate it here:') . "<br>" .
            '<a target="_blank" href="' . admin_url('admin.php?page=avia#goto_performance') . '">' . __("Performance Settings", 'avia_framework') . "</a>";

        $content     = "<span class='av-shortcode-disabled-notice'>{$msg}</span>";

        echo $content;
    }

    return;
}




$initial_id = avia_get_the_ID();

// check if we got posts to display:
if (have_posts()) :

    while (have_posts()) : the_post();

        /*
     * get the current post id, the current post class and current post format
 	 */
        $url = "";
        $current_post = array();
        $current_post['post_loop_count'] = $post_loop_count;
        $current_post['the_id']           = get_the_ID();
        $current_post['parity']           = $post_loop_count % 2 ? 'odd' : 'even';
        $current_post['last']          = count($wp_query->posts) == $post_loop_count ? " post-entry-last " : "";
        $current_post['post_type']    = get_post_type($current_post['the_id']);
        $current_post['post_class']     = "post-entry-" . $current_post['the_id'] . " post-loop-" . $post_loop_count . " post-parity-" . $current_post['parity'] . $current_post['last'] . " " . $blog_style;
        $current_post['post_class']    .= ($current_post['post_type'] == "post") ? '' : ' post';
        $current_post['post_format']     = get_post_format() ? get_post_format() : 'standard';
        $current_post['post_layout']    = avia_layout_class('main', false);
        $cat = get_the_category($post_id)[1];
        $blog_content = !empty($avia_config['blog_content']) ? $avia_config['blog_content'] : "content";

        /*If post uses builder change content to exerpt on overview pages*/
        if (Avia_Builder()->get_alb_builder_status($current_post['the_id']) && !is_singular($current_post['the_id']) && $current_post['post_type'] == 'post') {
            $current_post['post_format'] = 'standard';
            $blog_content = "excerpt_read_more";
        }


        /*
     * retrieve slider, title and content for this post,...
     */
        $size = strpos($blog_style, 'big') ? (strpos($current_post['post_layout'], 'sidebar') !== false) ? 'entry_with_sidebar' : 'entry_without_sidebar' : 'square';

        if (!empty($avia_config['preview_mode']) && !empty($avia_config['image_size']) && $avia_config['preview_mode'] == 'custom') $size = $avia_config['image_size'];

        /**
         * @since 4.5.4
         * @return string 
         */
        $current_post['slider'] = apply_filters('avf_post_featured_image_link', get_the_post_thumbnail($current_post['the_id'], $size), $current_post, $size);

        /**
         * Backwards comp. to checkbox prior v4.5.3 (now selectbox with '' or '1')
         */
        $hide_featured_image = empty(get_post_meta($current_post['the_id'], '_avia_hide_featured_image', true)) ? false : true;
        if (is_single($initial_id) && $hide_featured_image) {
            $current_post['slider'] = '';
        }

        $current_post['title']       = get_the_title();
        $current_post['content']     = $blog_content == "content" ? get_the_content(__('Read more', 'avia_framework') . '<span class="more-link-arrow"></span>') : get_the_excerpt();
        $current_post['content']     = $blog_content == "excerpt_read_more" ? $current_post['content'] . '<div class="read-more-link"><a href="' . get_permalink() . '" class="more-link">' . __('Read more', 'avia_framework') . '<span class="more-link-arrow"></span></a></div>' : $current_post['content'];
        $current_post['before_content'] = "";

        /*
     * ...now apply a filter, based on the post type... (filter function is located in includes/helper-post-format.php)
     */
        $current_post    = apply_filters('post-format-' . $current_post['post_format'], $current_post);
        $with_slider    = empty($current_post['slider']) ? "" : "with-slider";
        /*
     * ... last apply the default wordpress filters to the content
     */


        $current_post['content'] = str_replace(']]>', ']]&gt;', apply_filters('the_content', $current_post['content']));

        /*
	 * Now extract the variables so that $current_post['slider'] becomes $slider, $current_post['title'] becomes $title, etc
	 */
        extract($current_post);








        /*
	 * render the html:
	 */

        echo "<article id='researchPage' class='" . implode(" ", get_post_class('post-entry post-entry-type-' . $post_format . " " . $post_class . " " . $with_slider)) . "' " . avia_markup_helper(array('context' => 'entry', 'echo' => false)) . ">";



        //default link for preview images
        $link = !empty($url) ? $url : get_permalink();

        //preview image description
        $desc = get_post(get_post_thumbnail_id());
        if (is_object($desc))  $desc = $desc->post_excerpt;
        $featured_img_desc = ($desc != "") ? $desc : the_title_attribute('echo=0');

        //on single page replace the link with a fullscreen image
        if (is_singular()) {
            $link = avia_image_by_id(get_post_thumbnail_id(), 'large', 'url');
        }

        if (!in_array($blog_style, array('bloglist-simple', 'bloglist-compact', 'bloglist-excerpt'))) {
            //echo preview image
            if (strpos($blog_global_style, 'elegant-blog') === false) {
                if (strpos($blog_style, 'big') !== false) {
                    if ($slider) $slider = '<a href="' . $link . '" title="' . $featured_img_desc . '">' . $slider . '</a>';
                    if ($slider) echo '<div class="big-preview ' . $blog_style . '">' . $slider . '</div>';
                }

                if (!empty($before_content))
                    echo '<div class="big-preview ' . $blog_style . '">' . $before_content . '</div>';
            }
        }

        echo "<div class='blog-meta'>";

        $blog_meta_output = "";
        $icon =  '<span class="iconfont" ' . av_icon_string($post_format) . '></span>';

        if (strpos($blog_style, 'multi') !== false) {
            $gravatar = "";
            $link = get_post_format_link($post_format);
            if ($post_format == 'standard') {
                $author_name = apply_filters('avf_author_name', get_the_author_meta('display_name', $post->post_author), $post->post_author);
                $author_email = apply_filters('avf_author_email', get_the_author_meta('email', $post->post_author), $post->post_author);

                $gravatar_alt = esc_html($author_name);
                $gravatar = get_avatar($author_email, '81', "blank", $gravatar_alt);
                $link = get_author_posts_url($post->post_author);
            }

            $blog_meta_output = "<a href='{$link}' class='post-author-format-type'><span class='rounded-container'>" . $gravatar . $icon . "</span></a>";
        } else if (strpos($blog_style, 'small')  !== false) {
            $blog_meta_output = "<a href='{$link}' class='small-preview' title='{$featured_img_desc}'>" . $slider . $icon . "</a>";
        }

        echo apply_filters('avf_loop_index_blog_meta', $blog_meta_output);

        echo "</div>";

        echo "<div class='{$post_format}-content'>";
            $taxonomies  = get_object_taxonomies(get_post_type($the_id));
            $post_categories = wp_get_post_categories( $the_id );
            sort($post_categories);
            echo "<div class='research-page__breadcrumbs'><a href=\"http://aipr-rf.ru/\" rel=\"tag\">Главная</a>  > "; 
                foreach($post_categories as $key => $c){
                    $cat = get_category( $c );
                    $name = $cat->name;
                    $link = $cat->term_id;
                    echo "<a href=\"$link\">$name</a>";
                    if ($key + 1 !== count($post_categories)) {
                        echo "  > ";
                    }
                }
            echo "</div>";
       
       
            echo "<div class='research-page__header-wrapper'>";
                echo "<h2>$cat->name</h2>";
                echo "<h2 class='research-page__block-title title-primary'>Описание иследования</h2>";
                echo "<div style=\"width: 100%;\">";
                    echo "<h3 class='title-primary'>Тема иcследования</h3>";
                    echo '<header class="entry-content-header research-title-wrap">';
                    $postTitle =  get_the_title();
                    echo "<h1 class=\"post-title entry-title\" itemprop=\"headline\">	
                        <a href=\"\" rel=\"bookmark\" data-title=\"Маркетинговое исследование рынка $postTitle\">Маркетинговое исследование рынка $postTitle</a>
                    </h1>";
                echo "</div>";

                echo "<div class='research-page__common-block'>";
                    echo "<h3 class='title-primary'>Параметры исследования<h3>";
                    echo "<h4 class='title-primary research-page__block-sub-title'>География исследования: РФ</h4>";
                    echo "<h4 class='title-primary research-page__block-sub-title'>Исследуемый период: 2019</h4>";
                    echo "<h4 class='title-primary research-page__block-sub-title'>Прогнозный период: до 2023</h4>";
                echo "</div>";
            echo "</div>";


        // echo the post content
        if ($blog_style == 'bloglist-excerpt') {
            the_excerpt();
            echo '<div class="read-more-link"><a href="' . get_permalink() . '" class="more-link">' . __('Read more', 'avia_framework') . '<span class="more-link-arrow"></span></a></div>';
        }

        if (!in_array($blog_style, array('bloglist-simple', 'bloglist-compact', 'bloglist-excerpt'))) {
            echo "<div class='research-page__content-wrapper'>";
                    echo '<div class="entry-content" ' . avia_markup_helper(array('context' => 'entry_content', 'echo' => false)) . '>';
                    echo $content;
                    echo '</div>';
                    echo "<div>";
                        echo "<div class='research-page__common-block'>";
                        echo "<h3 class='title-primary'>Методика</h3>";
                        echo "<p>Наиболее эффективным методом проведения данного исследования является 
                                сочетание кабинетного метода и последующей верификации полученных данных путем качественных
                                и количественных исследований.
                            </p>
                            <p>
                            Цель кабинетного исследования заключается в сборе всей имеющийся в открытых 
                            источниках информации, создании информационной основы для дальнейшей 
                            разработки проекта.
                            </p>";
                        echo "</div>";

                        
                        echo "<div class='research-page__common-block'>";
                            echo "<h3 class='title-primary'>Источники:</h3>";
                            echo "<ul>
                                <li>Анализ баз данных федеральной томоженной слжбы (ФТС) по импорту и экспорту
                                продукции.</li>
                                <li>Сбор и оценка данных Федеральной службы государственной статистики (ФСГС)</li>
                                <li>Анализ финансово-экономической стастистики российских предприятий, данных
                                    Федеральной налоговой службы (ФНС)
                                </li>
                                <li>Проведение интервью под легендой с компаниями, занимающимися производством
                                и реализацией иссделуемой продукции в РФ. 
                                </li>
                                <li>Интервью с отдельными экспертами отрасли.</li>
                            </ul>";
                        echo "</div>";



                        echo "<div class='research-page__common-block'>";
                            echo "<h3 class='title-primary'>Стоимость исследования</h3>";
                            $pirce = get_post_meta($current_post['the_id']  , 'Price')[0];
                            echo "<p>$pirce &#8381; включая НДС 20%</p>";
                        echo "</div>";

                        echo "<div class='research-page__common-block'>";
                            echo "<h3 class='title-primary'>Условия оплаты:</h3>";
                            echo "100% пердоплата";
                        echo "</div>";

                        echo "<div class='research-page__common-block'>";
                            echo "<h3 class='title-primary'>Дополнительные услуги:</h3>";
                            echo "<ul>
                                <li>Перевод отчета на английский язык от 30 000 рублей</li>
                                <li>Подготовка презентации по исследуемой тематике на русском и/или английском языках от 30 000 рублей</li>
                                <li>Подготовка плана маркетинга, плана стратегического развития и прочие услуги маркетингового консалтинга</li>
                            </ul>";
                        echo "</div>";

                        echo "<div>";
                            echo "<button class='button reserach-popup-btn'>Получить демо версию </button>";
                        echo "</div>";
                echo "</div>";
            echo "</div>";
        }
        echo "</article>";

        $post_loop_count++;
    endwhile;
else :

?>

    <article class="entry">
        <header class="entry-content-header">
            <h1 class='post-title entry-title'><?php _e('Nothing Found', 'avia_framework'); ?></h1>
        </header>

        <p class="entry-content" <?php avia_markup_helper(array('context' => 'entry_content')); ?>><?php _e('Sorry, no posts matched your criteria', 'avia_framework'); ?></p>

        <footer class="entry-footer"></footer>
    </article>

<?php

endif;

if (empty($avia_config['remove_pagination'])) {
    echo "<div class='{$blog_style}'>" . avia_pagination('', 'nav') . "</div>";
}
