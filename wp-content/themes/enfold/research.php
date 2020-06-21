<?php
/* Template Name: research page */
if (!defined('ABSPATH')) {
	die();
}

global $avia_config, $wp_query;

/*
	 * get_header is a basic wordpress function, used to retrieve the header.php file in your theme directory.
	 */
get_header();

/**
 * @used_by				enfold\config-wpml\config.php				10
 * @since 4.5.1
 */
do_action('ava_page_template_after_header');

if (get_post_meta(get_the_ID(), 'header', true) != 'no') echo avia_title();

do_action('ava_after_main_title');
?>

<div class='container_wrap container_wrap_first main_color <?php avia_layout_class('main'); ?>'>

	<div class='container'>

		<main class='template-page content  <?php avia_layout_class('content'); ?> units' <?php avia_markup_helper(array('context' => 'content', 'post_type' => 'page')); ?>>
			<? $arg_cat = array(
				'orderby'      => 'name', // сортировка по названию
				'order'        => 'ASC', // сортировка от меньшего к большему
				'hide_empty'   => 1, // скрыть пустые рубрики
				'exclude'      => '', // id рубрики, которые надо исключить
				'include'      => '', // id рубрики, из которых надо выводить
				'taxonomy'     => 'category', // название таксономии
			);
			$categories = get_categories($arg_cat);
			if ($categories) {
				foreach ($categories as $cat) {
					echo $cat->name;
				}
			}
			?>
		</main>

		<?php

		//get the sidebar
		$avia_config['currently_viewing'] = 'page';
		get_sidebar();

		?>

	</div>
	<!--end container-->

</div><!-- close default .container_wrap element -->



<?php
get_footer();
