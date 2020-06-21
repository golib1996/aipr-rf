<?php
if (!defined('ABSPATH')) {
	die();
}

global $avia_config;


/*
	 * get_header is a basic wordpress function, used to retrieve the header.php file in your theme directory.
	 */
get_header();

//	allows to customize the layout
do_action('ava_search_after_get_header');


$results = avia_which_archive();
echo avia_title(array('title' => $results));

do_action('ava_after_main_title');
?>

<div class='container_wrap container_wrap_first main_color <?php avia_layout_class('main'); ?>'>
	<div class='container template-blog'>
		<main class='content <?php avia_layout_class('content'); ?> units' <?php avia_markup_helper(array('context' => 'content', 'post_type' => 'post')); ?>>
			<h1 class="">
				<? 
                echo 'Поиск: ' . $_GET['s'];
        ?>
			</h1>
			<?
				if (have_posts()) {
					?>
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
						get_template_part('includes/loop', 'research');
						?>
					</tbody>
				</table>
			</div>

			<?
				} else {
				?>
			<p>К сожалению, по вашему запросу исследования не было найдено, оставьте заявку и наши специалисты свяжутся с вами.</p>
			<button class="button reserach-not-found-popup-btn" style="cursor: pointer;">Оставить заявку</button>

			<script>
				window.onload = () => {
					const form = document.querySelector('.not-found-researh-form');
					if (!form) {
						alert('notFound');
					}

					const setStatus = (message) => {
						const div = document.createElement("div");
						const messageWrap = form.appendChild(div);
						messageWrap.innerText = message;
						setTimeout(() => {
							messageWrap.remove();
						}, 3000);
					};

					form.onsubmit = notResearchFound;

					function notResearchFound(e) {
						e.preventDefault();
						const data = {};
						const form = e.target;
						const inputs = form.querySelectorAll("input");
						if (!inputs) return;
						inputs.forEach((input) => {
							if (input.name === "name" || input.name === "phone" || input.name === 'email' || input.name === 'title') {
								data[input.name] = input.value;
							}
						});
						data.isNotFound = true;
						const xhttp = new XMLHttpRequest();
						xhttp.onreadystatechange = function(status) {
							if (this.readyState == 4 && this.status == 200) {
								setStatus("Ваша заявка направлена. В ближайшее время с Вами свяжется наш специалист.");
							} else {}
						};
						xhttp.open("POST", "/build/Ajax.php", true);
						xhttp.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
						xhttp.send(JSON.stringify(data));
					};
				}
			</script>
			<?
					 
				}?>
			<main>
	</div>

</div><!-- close default .container_wrap element -->




<?php
get_footer();
